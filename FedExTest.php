#!/usr/bin/env php
<?php
//	Raw, block data, FedEx test.
//	Copyright 2017, Magento. All rights reserved.
//	Version 1.0.0
//	Created and tested using PHP 5.5.

function pout($s)
{
	fwrite( STDOUT, $s );
}

function perr($s)
{
	fwrite( STDERR, $s );
}

/**
 * Convert all errors into ErrorExceptions
 */
set_error_handler(
	function ($severity, $errstr, $errfile, $errline) {
		throw new ErrorException($errstr, 1, $severity, $errfile, $errline);
	},
	E_ALL
);


try {

//	Your FedEx credentials.
define('DEV_KEY', '');
define('TEST_PASSWORD', '');
define('ACCOUNT_NUMBER', '');
define('METER_NUMBER', '');


//	Path to WSDL file.
$pathToWsdl = __DIR__ . '/RateService_v20.wsdl';

//	The version numbers in the WSDL file and in the request structure below
//		(request->Version->Major) must match. The version number is also used
//		in the WSDL file name and URI path name.

//	Data to send to FedEx.
$request = [
	'WebAuthenticationDetail' => [
		'UserCredential' => [
			'Key' => DEV_KEY,
			'Password' => TEST_PASSWORD,
		],
	],
	'ClientDetail' => [
		'AccountNumber' => ACCOUNT_NUMBER,
		'MeterNumber' => METER_NUMBER,
	],
	'TransactionDetail' => [
		'CustomerTransactionId' => md5(time()),
	],
	'Version' => [
		'ServiceId' => 'crs',
		'Major' => '20',
		'Intermediate' => '0',
		'Minor' => '0',
	],
	'ReturnTransitAndCommit' => true,
	'RequestedShipment' => [
		'DropoffType' => 'REGULAR_PICKUP',
		'ShipTimestamp' => date('c'),
		'ServiceType' => 'GROUND_HOME_DELIVERY',
		'PackagingType' => 'YOUR_PACKAGING',
		'TotalInsuredValue' => [
			'Ammount' => 10,
			'Currency' => 'USD',
		],
		'Shipper' => [
			'Contact' =>[
				'PersonName' => 'Sender Name',
				'CompanyName' => 'Sender Company Name',
				'PhoneNumber' => '9012638716',
			],
			'Address' => [
				'StreetLines' => [
					0 => '1234 Main St',
				],
				'City' => 'El Cajon',
				'StateOrProvinceCode' => 'CA',
				'PostalCode' => '92020',
				'CountryCode' => 'US',
			],
		],
		'Recipient' => [
			'Contact' => [
				'PersonName' => 'Recipient Name',
				'CompanyName' => 'Company Name',
				'PhoneNumber' => '9012637906',
			],
			'Address' => [
				'StreetLines' => [
					0 => '123 Ash Ln.',
				],
				'City' => 'Austin',
				'StateOrProvinceCode' => 'TX',
				'PostalCode' => '73301',
				'CountryCode' => 'US',
				'Residential' => true,
			],
		],
		'ShippingChargesPayment' => [
			'PaymentType' => 'SENDER',
			'Payor' => [
				'ResponsibleParty' => [
					'AccountNumber' => ACCOUNT_NUMBER,
					'CountryCode' => 'US',
				],
			],
		],
		'RateRequestTypes' => 'LIST',
		'PackageCount' => '1',
		'RequestedPackageLineItems' => [
			'SequenceNumber' => 1,
			'GroupPackageCount' => 1,
			'Weight' => [
				'Value' => 22,
				'Units' => 'LB',
			],
// 			'Dimensions' => [
// 				'Length' => 12,
// 				'Width' => 12,
// 				'Height' => 12,
// 				'Units' => 'IN',
// 			],
// 			'ContentRecords' => [
// 				'PartNumber' => '123445',
// 				'ItemNumber' => 'kjdjalsro1262739827',
// 				'ReceivedQuantity' => 12,
// 				'Description' => 'Content Description'
// 			],
		],
	],
];

//	Show us the request data structure.
pout( print_r($request, true) . PHP_EOL );


//	Set options for SOAP call.
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);
$options = [
// 	'location' => 'https://wsbeta.fedex.com:443/web-services',	//	remove "beta" for live data.
// 	'uri' => 'http://fedex.com/ws/rate/v20',
	'stream_context'=> stream_context_create(['ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]),
	'trace' => true,
	'exceptions' => true,
	'encoding' => 'UTF-8',
	'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_DEFLATE,
	'cache_wsdl' => WSDL_CACHE_NONE
];

//	Create client and call service.
//	Set $pathToWsdl to NULL when using 'location' and 'uri' options.
$client = new SoapClient($pathToWsdl, $options);
$response = $client->getRates($request);

//	There will be a pause while waiting for FedEx to respond.

//	Show us the response data structure.
pout( print_r($response, true) );


//	End 'try'.
}
catch ( Exception $e ) {
	perr( $e . PHP_EOL );
	exit( $e->getCode() );
}

# FedExApiTest
This simple script requires moderate PHP skills. It is intended to fill two tasks. First, it's to be an example of a current working connection to the FedEx API and all the options required to make that work. I have yet to work with any company's WEB API that wasn't quite under-documented. Second, as a transparent as reasonable, one-level-up way of retrieving shipping rates  without (much) intervening code. That being one level away from using `curl` and raw properly built XML files.

FedEx provides a number of sample SOAP formatted XML request and reply files on their [Developer Resource Center](http://www.fedex.com/us/developer/web-services/index.html) along with static WSDL files for download. Version 20 of the “Quote Rates” is included here. After you have signed up (free) go to their “Documents and Downloads” page to retrieve the files. Their `include` files are not required for this script to work, only a WSDL file. If you need to change the version number be aware there are a number of places that need to be changed and they must all match.

This script, FedExApiTest.php, has the first line `#!/usr/bin/env php` which allows it to be run directly on the command line on most Linux/Unix systems including OS X. Just be sure the file has its executable bit set and that the PHP version is at least 5.4.
```
> ./FedExApiTest.php
```

Their raw XML sample files can be modified if you are comfortable with SOAP XML encoding. (For some reason all the files end in “txt”.) They will require your FedEx provided credentials to be manually entered into them. Use this CLI command to try out your modified file:
```
> curl -k 'https://wsbeta.fedex.com:443/web-services' --data @path/to/your/file.xml
```

This PHP code has been greatly simplified from their examples. All values are hard coded in one easy to visualize single nested structure so that the full structure and data membership can be seen at a glance. The `$request` associative array, or structure, is then converted to SOAP XML by the `$client` instance of the standard PHP SoapClient class before being sent to FedEx. Hopefully there are enough comments in the script.

Check the FedEx documentation for additional structure members and for other valid values for enumerated types.

# License
This software is made available with the standard MIT license, without warranty of any kind.

The “RateService” WSDL file is provided as a convenience and is licensed by [FedEx Corp](https://www.fedex.com/).

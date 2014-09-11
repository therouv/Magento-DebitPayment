DebitPayment
============
This extension allows shop owners to provide the payment method "DebitPayment" to their customers.

Build Status
------------
* Latest Release: [![Master Branch](https://travis-ci.org/therouv/Magento-DebitPayment.svg?branch=master)](https://travis-ci.org/therouv/Magento-DebitPayment)
* Development Branch: [![Develop Branch](https://travis-ci.org/therouv/Magento-DebitPayment.svg?branch=develop)](https://travis-ci.org/therouv/Magento-DebitPayment)

Facts
-----
- version: 1.1.3
- extension key: DebitPayment
- [extension on Magento Connect](http://www.magentocommerce.com/magento-connect/debitpayment.html)
- Magento Connect 1.0 extension key: magento-community/DebitPayment
- Magento Connect 2.0 extension key: http://connect20.magentocommerce.com/community/DebitPayment
- [extension on GitHub](https://github.com/therouv/Magento-DebitPayment)

Description
-----------
This extension allows shop owners to provide the payment method "DebitPayment" to their customers.
This includes:
- Complete order via DebitPayment
- Choose between DebitPayment via normal bank data or via SEPA data
- Find the correct German bank name given by the entered routing number
- Save account data encrypted in database to pre-fill checkout fields on further checkouts
- Export all DebitPayment orders as CSV file or DTAUS file
- SEPA Mandate PDF generation

Requirements
------------
- PHP >= 5.3.0
- PHP class [ZipArchive](http://php.net/manual/en/class.ziparchive.php)

Compatibility
-------------
- Magento >= 1.6
- Versions below should work to version 1.4 without any problems but it is not actively tested.

Installation Instructions
-------------------------
1. Install the extension via Magento Connect with the key shown above or copy all the files into your document root.
2. Clear the cache, logout from the admin panel and then login again.
3. You can now enable the payment method via *System -> Configuration -> Sales -> Payment -> DebitPayment*

Uninstallation
--------------
To uninstall this extension you need to run the following SQL after removing the extension files:
```sql
  DELETE FROM `core_config_data` WHERE path LIKE 'payment/debit/%';
  DELETE FROM `core_config_data` WHERE path LIKE 'debitpayment/%';
  DELETE FROM `core_resource` WHERE code = 'debit_setup';
  DELETE FROM `eav_attribute` WHERE attribute_code = 'debit_payment_acount_update';
  DELETE FROM `eav_attribute` WHERE attribute_code = 'debit_payment_acount_name';
  DELETE FROM `eav_attribute` WHERE attribute_code = 'debit_payment_acount_number';
  DELETE FROM `eav_attribute` WHERE attribute_code = 'debit_payment_acount_blz';
  DELETE FROM `eav_attribute` WHERE attribute_code = 'debit_payment_account_swift';
  DELETE FROM `eav_attribute` WHERE attribute_code = 'debit_payment_account_iban';
  DROP TABLE `debit_order_grid`;
```


Support & Feature-Wishes
------------------------
If you have any issues or you are missing an feature with this extension, please open an issue on [GitHub](https://github.com/therouv/Magento-DebitPayment/issues). Thank you.

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Rouven Alexander Rieker
- Website: [http://rouven-rieker.com](http://rouven-rieker.com)
- Twitter: [@therouv](https://twitter.com/therouv)

Licence
-------
[Open Software License (OSL 3.0)](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2008-2014 Rouven Alexander Rieker / ITABS GmbH

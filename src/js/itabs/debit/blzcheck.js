/**
 * This file is part of the Itabs_Debit module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    ITABS GmbH <info@itabs.de>
 * @copyright 2008-2014 ITABS GmbH (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   1.1.4
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */

var blzAjaxCheck = Class.create();
blzAjaxCheck.prototype = {
    initialize: function(checkBlzUrl, checkoutValidBlz) {
        this.checkBlzUrl = checkBlzUrl;
        this.isBlzValid = false;
        this.checkoutValidBlz = checkoutValidBlz;
    },
    checkBlz: function() {
        var param = '';
        var identifier = '';
        if ($('bankleitzahl')) {
            param = $('bankleitzahl').value;
            identifier = 'routing';
        } else {
            param = $('swiftcode').value;
            identifier = 'swift';

            if (8 == param.length) {
                param = param + 'XXX';
                $('swiftcode').setValue(param);
            }
        }

        new Ajax.Request(
            this.checkBlzUrl,
            {
                method:'post',
                asynchronous: false,
                onSuccess: this.setStatus.bind(this),
                parameters: {bankparam:param,identifier:identifier}
            }
        );
    },
    setStatus: function(transport) {
        if (transport && transport.responseText) {
            $('bank_name').value = '';
            try {
                response = eval('(' + transport.responseText + ')');
            } catch (e) {
                response = {};
            }
        }

        if (response.found && response.found == 1) {
            this.isBlzValid = true;
            $('bank_name').value = response.bank;
        } else {
            this.isBlzValid = false;
            $('bank_name').value = '';
        }
    },
    validateIban: function(iban){
        if (iban != $('iban').value){
            iban = $('iban').value
        }

        iban = iban.replace(/\s/g, "");
        var newIban = iban.toUpperCase(),

        modulo = function(divident, divisor) {
            var m = 0;
            for (var i = 0; i < divident.length; ++i) {
                m = (m * 10 + parseInt(divident.charAt(i))) % divisor;
            }
            return m;
        };

        if (newIban.search(/^[A-Z]{2}/gi) < 0) {
            return false;
        }

        newIban = newIban.substring(4) + newIban.substring(0, 4);
        newIban = newIban.replace(/[A-Z]/g, function (match) {
            return match.charCodeAt(0) - 55;
        });
        return (parseInt(modulo(newIban, 97)) === 1);
    }
}

Event.observe(window, 'load', function() {
    Validation.add('validate-debit-blz', Translator.translate('Please enter a valid bank code.'), function(v) {
        blzCheck.checkBlz();
        if (blzCheck.checkoutValidBlz == 1) {
            if (!blzCheck.isBlzValid) {
                return false;
            }
        }

        if (v.length == 8 || v.length == 5) {
            return true;
        }
        return false;
    });

    Validation.add('validate-debit-number',  Translator.translate('Please enter a valid bank acount number.'), function(v) {
        if (v.length > 4 && v.length <= 11) {
            return true;
        }
        return false;
    });

    Validation.add('validate-debit-iban',  Translator.translate('Please enter a valid international bank account number.'), function(v) {
        if (blzCheck.validateIban()) {
            return true;
        }
        return false;
    });

    Validation.add('validate-debit-swift',  Translator.translate('Please enter a valid swift code.'), function(v) {
        if (v.length < 11 || v.length > 11) {
            return false;
        }
        var regex = /[a-zA-Z]{4}[a-zA-Z]{2}[a-zA-Z0-9]{2}[a-zA-Z0-9]{3}/;
        return regex.test(v);
    });
});

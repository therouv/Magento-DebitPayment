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
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */

var blzAjaxCheck = Class.create();
blzAjaxCheck.prototype = {
    initialize: function(checkBlzUrl, checkoutValidBlz){
        this.checkBlzUrl = checkBlzUrl;
        this.isBlzValid = false;
        this.checkoutValidBlz = checkoutValidBlz;
    },
    checkBlz: function() {
        var request = new Ajax.Request(
            this.checkBlzUrl,
            {
                method:'post',
                asynchronous: false,
                onSuccess: this.setStatus.bind(this),
                parameters: {blz:$('bankleitzahl').value}
            }
        );
    },
    setStatus: function(transport) {
        if (transport && transport.responseText){
            $('blz_bank_name').update('');
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }
        if (response.found && response.found == 1) {
            this.isBlzValid = true;
        } else {
            this.isBlzValid = false;
        }
        $('blz_bank_name').update(response.bank);
        $('bankleitzahl').value = response.blz;
    }
}

Event.observe(window, 'load', function() {
    Validation.add('validate-debit-blz', Translator.translate('Please enter a valid bank code.'), function(v) {

        blzCheck.checkBlz();
        if(blzCheck.checkoutValidBlz == 1) {
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
        if (v.length > 4 && v.length < 11) {
            return true;
        }
        return false;
    });
});

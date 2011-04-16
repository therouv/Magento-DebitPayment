/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package    Mage_Debit
 * @copyright  2011 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright  2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
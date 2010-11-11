<?php

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
 * @copyright  Copyright (c) 2010 ITABS GbR - Rouven Alexander Rieker
 * @copyright  Copyright (c) 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Debit_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('debit/form.phtml');
    }

    public function getBankName()
    {
        $blz = $this->getAccountBLZ();
        if (empty($blz)) {
            return $this->__('-- will be filled in automatically --');
        }
        $bankName = Mage::helper('debit')->getBankByBlz($blz);
        if ($bankName == null) {
            $bankName = $this->__('not available');
        }
        return $bankName;
    }

    public function getAccountBLZ()
    {
    	if ($data = $this->getInfoData('cc_type')) {
	    	return $data;
	    } elseif($data = $this->_getAccountData('debit_payment_acount_blz')) {
	    	return $data;
	    } else {
	    	return '';
	    }
    }

    public function getAccountName()
    {
        if ($data = $this->getInfoData('cc_owner')) {
            return $data;
        }
        return $this->_getAccountData('debit_payment_acount_name');
    }

    public function getAccountNumber()
    {
	    if ($data = $this->getInfoData('cc_number')) {
	    	return $data;
	    } elseif($data = $this->_getAccountData('debit_payment_acount_number')) {
	    	return $data;
	    } else {
	    	return '';
	    }
    }

    protected function _getAccountData($field)
    {
        if (!Mage::getStoreConfigFlag('payment/debit/save_account_data')) {
            return '';
        }
        $data = $this->getCustomer()->getData($field);
        if (strlen($data) == 0) {
            return '';
        }
        return $this->htmlEscape($data);
    }

    public function getCustomer()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getCustomer();
        }
        return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    public function getCheckoutValidBlz() {
    	return Mage::getStoreConfigFlag('payment/debit/checkout_valid_blz');
    }
}

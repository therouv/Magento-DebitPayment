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
 * @copyright  Copyright (c) 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Debit_Block_Account_Data extends Mage_Customer_Block_Account_Dashboard
{
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
        return $this->_getAccountData('debit_payment_acount_blz');
    }

    public function getAccountName()
    {
        return $this->_getAccountData('debit_payment_acount_name');
    }

    public function getAccountNumber()
    {
        return $this->_getAccountData('debit_payment_acount_number');
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
}

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
 * @copyright  2011 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright  2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Debit_Block_Account_Data
    extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * getBankName
     * 
     * Returns the bank name
     * 
     * @return string Bankname
     */
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

    /**
     * getAccountBLZ
     * 
     * Returns the account blz of the specific account
     * 
     * @return string BLZ
     */
    public function getAccountBLZ()
    {
        return $this->_getAccountData('debit_payment_acount_blz');
    }

    /**
     * getAccountName
     * 
     * Returns the account owner name of the specific account
     * 
     * @return string Name
     */
    public function getAccountName()
    {
        return $this->_getAccountData('debit_payment_acount_name');
    }

    /**
     * getAccountNumber
     * 
     * Returns the number of the specific account
     * 
     * @return string Account Number
     */
    public function getAccountNumber()
    {
        return $this->_getAccountData('debit_payment_acount_number');
    }

    /**
     * _getAccountData
     * 
     * Returns the specific value of the requested field from the
     * customer model.
     * 
     * @param string $field Attribute to get
     */
    protected function _getAccountData($field)
    {
        if (!Mage::getStoreConfigFlag('payment/debit/save_account_data')) {
            return '';
        }
        $data = $this->getCustomer()->getData($field);
        if (strlen($data) == 0) {
            return '';
        }
        if ($field != 'debit_payment_acount_name' && !is_numeric($data)) {
            return '';
        }
        return $this->htmlEscape($data);
    }
}
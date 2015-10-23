<?php
/**
 * This file is part of the Itabs_Debit module.
 *
 * PHP version 5
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
 * @version   1.1.6
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */

/**
 * Debit Form Block for customer account page
 */
class Itabs_Debit_Block_Account_Data
    extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * Set the right template depending on the debit type
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        /* @var $helper Itabs_Debit_Helper_Data */
        $helper = Mage::helper('debit');
        if ($helper->getDebitType() == Itabs_Debit_Helper_Data::DEBIT_TYPE_SEPA) {
            $this->setTemplate('debit/sepa/account/data.phtml');
        } else {
            $this->setTemplate('debit/account/data.phtml');
        }
    }

    /**
     * Returns the bank name
     *
     * @return string Bankname
     */
    public function getBankName()
    {
        return $this->_getAccountData('debit_payment_account_bankname');
    }

    /**
     * Returns the account blz of the specific account
     *
     * @return string BLZ
     */
    public function getAccountBLZ()
    {
              if ($data = $this->getInfoData('cc_type')) {
            if (!is_numeric($data)) {
                $data = Mage::helper('core')->decrypt($data);
            }

            return $data;
        } elseif ($data = $this->_getAccountData('debit_payment_acount_blz')) {
            return $data;
        } else {
            return '';
        }
    }

    /**
     * Returns the account owner name of the specific account
     *
     * @return string Name
     */
    public function getAccountName()
    {
        return $this->_getAccountData('debit_payment_acount_name');
    }

    /**
     * Returns the number of the specific account
     *
     * @return string Account Number
     */
    public function getAccountNumber()
    {
        return $this->_getAccountData('debit_payment_acount_number');
    }

    /**
     * Returns the swift code of the specific account
     *
     * @return string
     */
    public function getAccountSwift()
    {
      if (Mage::getStoreConfigFlag('payment/debit/save_account_data')) {
        return Mage::helper('core')->decrypt($this->_getAccountData('debit_payment_account_swift'));
      } else {
        return $this->_getAccountData('debit_payment_account_swift');
      }
    }

    /**
     * Returns the iban of the specific account
     *
     * @return string
     */
    public function getAccountIban()
    {       if (Mage::getStoreConfigFlag('payment/debit/save_account_data')) {
        return Mage::helper('core')->decrypt($this->_getAccountData('debit_payment_account_iban'));
      } else {
        return $this->_getAccountData('debit_payment_account_iban');
      }
    }

    /**
     * Returns the specific value of the requested field from the
     * customer model.
     *
     * @param  string $field Attribute to get
     * @return string Data
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

        return $this->escapeHtml($data);
    }

    /**
     * Retrieve the creditor identification number
     *
     * @return string
     */
    public function getCreditorIdentificationNumber()
    {
        return Mage::helper('debit')->getCreditorIdentificationNumber();
    }

    /**
     * Retrieve the hint for the IBAN field
     *
     * @return string|bool
     */
    public function getHintForIbanField()
    {
        return Mage::helper('debit')->getHintForIbanField();
    }

    /**
     * Retrieve the hint for the BIC field
     *
     * @return string|bool
     */
    public function getHintForBicField()
    {
        return Mage::helper('debit')->getHintForBicField();
    }
}

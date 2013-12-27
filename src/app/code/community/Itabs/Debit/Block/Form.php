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
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2014 ITABS GmbH (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
/**
 * Debit Form Block
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2014 ITABS GmbH (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Construct payment form block and set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('debit/form.phtml');
    }

    /**
     * Returns the account name from the payment info instance
     *
     * @return string Name
     */
    public function getAccountName()
    {
        if ($data = $this->getInfoData('cc_owner')) {
            return $data;
        }

        return $this->_getAccountData('debit_payment_acount_name');
    }

    /**
     * Returns the swift code of the specific account
     *
     * @return string
     */
    public function getAccountSwift()
    {
        return $this->_getAccountData('debit_payment_account_swift');
    }

    /**
     * Returns the iban of the specific account
     *
     * @return string
     */
    public function getAccountIban()
    {
        return $this->_getAccountData('debit_payment_account_iban');
    }

    /**
     * Returns the company name of the specific account
     *
     * @return string
     */
    public function getAccountCompany()
    {
        return $this->_getAccountData('debit_company');
    }

    /**
     * Returns the street of the specific account
     *
     * @return string
     */
    public function getAccountStreet()
    {
        return $this->_getAccountData('debit_street');
    }

    /**
     * Returns the city of the specific account
     *
     * @return string
     */
    public function getAccountCity()
    {
        return $this->_getAccountData('debit_city');
    }

    /**
     * Returns the email address of the specific account
     *
     * @return string
     */
    public function getAccountEmail()
    {
        return $this->_getAccountData('debit_email');
    }

    /**
     * Returns the country_id of the specific account
     *
     * @return string
     */
    public function getAccountCountry()
    {
        $country = $this->_getAccountData('debit_country');
        if ($country == '') {
            return null;
        }

        return $country;
    }

    /**
     * Returns the account number from the payment info instance
     *
     * @return string Number
     */
    public function getAccountNumber()
    {
        $attribute = 'debit_payment_acount_number';
        if ($data = $this->getInfoData('cc_number')) {
            return $data;
        } elseif ($data = $this->_getAccountData($attribute)) {
            return $data;
        } else {
            return '';
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
     * Returns the current customer
     *
     * @return Mage_Customer_Model_Customer Customer
     */
    public function getCustomer()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getCustomer();
        }

        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * Retrieve the country html select field
     *
     * @return string
     */
    public function getCountryHtmlSelect()
    {
        return Mage::getBlockSingleton('directory/data')->getCountryHtmlSelect(
            $this->getAccountCountry(),
            'payment[debit_country]',
            'debit_country',
            $this->helper('customer')->__('Country')
        );
    }

    /**
     * Retrieve the hint for the IBAN field
     *
     * @return string|bool
     */
    public function getHintForIbanField()
    {
        $field = Mage::getStoreConfig('debitpayment/sepa/hint_iban_field');
        if ($field == '') {
            return false;
        }

        return $field;
    }

    /**
     * Retrieve the hint for the BIC/SWIFT-Code field
     *
     * @return string|bool
     */
    public function getHintForSwiftField()
    {
        $field = Mage::getStoreConfig('debitpayment/sepa/hint_swift_field');
        if ($field == '') {
            return false;
        }

        return $field;
    }
}

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
 * @version   1.0.7
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
/**
 * Debit Mandate Block
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Block_Mandate extends Mage_Core_Block_Template
{
    /**
     * Retrieve the name of the payee
     *
     * @return string
     */
    public function getPayee()
    {
        return Mage::getStoreConfig('debitpayment/bankaccount/account_owner');
    }

    /**
     * Retrieve the creditor identification number
     *
     * @return string
     */
    public function getCreditorIdentificationNumber()
    {
        return Mage::getStoreConfig('debitpayment/sepa/creditor_identification_number');
    }

    /**
     * Retrieve the mandate text
     *
     * @return string
     */
    public function getMandateText()
    {
        return Mage::getStoreConfig('debitpayment/sepa/mandate_text');
    }

    /**
     * Get unique mandate reference for this order
     *
     * @return string
     */
    public function getMandateReference()
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if (null === $customerId) {
            $customerId = 0;
        }

        return Mage::helper('debit')->getMandateReference($customerId, $this->getQuote()->getId());
    }

    /**
     * Retrieve the current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Retrieve the payment method instance
     *
     * @return Itabs_Debit_Model_Debit
     */
    public function getPayment()
    {
        return $this->getQuote()->getPayment()->getMethodInstance();
    }

    /**
     * Get the formatted current date
     *
     * @return string Date
     */
    public function getCurrentDate()
    {
        return $this->helper('core')->formatDate(null, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Check if the selected payment method is debit payment. If yes render the
     * selected template, otherwise return an empty string.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getPayment()->getCode() != 'debit') {
            return '';
        }

        if (!$this->helper('debit')->isGenerateMandate()) {
            return '';
        }

        return parent::_toHtml();
    }
}

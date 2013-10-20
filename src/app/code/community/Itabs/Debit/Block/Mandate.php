<?php

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
        return 'lala';
    }

    /**
     * @return Itabs_Debit_Model_Debit
     */
    public function getPayment()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance();
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

        return parent::_toHtml();
    }
}

<?php

class Itabs_Debit_Block_Mandate extends Mage_Core_Block_Template
{
    /**
     * Retruns the current customer from customer session
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Returns the from action url
     *
     * @return string the url
     */
    public function getFormAction()
    {
        return Mage::getUrl('debit/mandate/print');
    }

    /**
     * Returns the current customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
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
     * Retrieve the hint for the BIC field
     *
     * @return string|bool
     */
    public function getHintForBicField()
    {
        $field = Mage::getStoreConfig('debitpayment/sepa/hint_bic_field');
        if ($field == '') {
            return false;
        }

        return $field;
    }
}

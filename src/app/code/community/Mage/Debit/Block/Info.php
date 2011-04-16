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
 * @copyright  Copyright (c) 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Debit_Block_Info extends Mage_Payment_Block_Info
{
    /**
     * _construct
     * 
     * Construct payment info block and set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('debit/info.phtml');
    }

    /**
     * toPdf
     * 
     * Sets the template for PDF print-outs
     * 
     * @returns string Text for PDF
     */
    public function toPdf()
    {
        $this->setTemplate('debit/debit.phtml');
        return $this->toHtml();
    }

    /**
     * isEmailContext
     * 
     * Checks if we are in the email context
     * 
     * @return boolean true/false
     */
    public function isEmailContext()
    {
        $info = $this->getInfo();
        if ($info instanceof Mage_Sales_Model_Quote_Payment) {
            return false;
        } elseif ($info instanceof Mage_Sales_Model_Order_Payment) {
            if (Mage::app()->getStore()->isAdmin()) {
                $action = Mage::app()->getRequest()->getActionName();
                if ($action == 'email' || $action == 'save') {
                    return true;                                         // Admin
                } else {
                    return false;                                        // Admin View
                }   
            } else {
                return true;                                             // Frontend
            }
        }
    }

    /**
     * sendDataInEmail
     * 
     * Returns the config setting if bank data should be send in the email
     * 
     * @return boolean true/false
     */
    public function sendDataInEmail()
    {
        return Mage::getStoreConfigFlag('payment/'.$this->getMethod()->getCode().'/sendmail');
    }

    /**
     * getEmailData
     * 
     * Returns email data and mask the data if necessary
     * 
     * @return array Bank data
     */
    public function getEmailData()
    {
        $payment = $this->getMethod();
        $data = array(
            'account_name'      =>  $payment->getAccountName(),
            'account_number'    =>  $payment->getAccountNumber(),
            'account_blz'       =>  $payment->getAccountBLZ(),
            'bank_name'         =>  $payment->getAccountBankname()
        );
        // mask bank data
        if (Mage::getStoreConfigFlag('payment/'.$this->getMethod()->getCode().'/sendmail_crypt'))
        {
            $data['account_number'] = $payment->maskString($payment->getAccountNumber());
            $data['account_blz'] = $payment->maskString($payment->getAccountBLZ());
            $data['bank_name'] = '';
        }
        return $data;
    }
}
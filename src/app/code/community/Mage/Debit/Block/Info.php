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

class Mage_Debit_Block_Info extends Mage_Payment_Block_Info
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('debit/info.phtml');
    }

    public function toPdf()
    {
        $this->setTemplate('debit/debit.phtml');
        return $this->toHtml();
    }

    public function isEmailContext()
    {
        $info = $this->getInfo();
        if ($info instanceof Mage_Sales_Model_Quote_Payment) {
            return false;
        } elseif ($info instanceof Mage_Sales_Model_Order_Payment) {
            return true;
        }
    }

    public function sendDataInEmail()
    {
        return Mage::getStoreConfigFlag('payment/'.$this->getMethod()->getCode().'/sendmail');
    }

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

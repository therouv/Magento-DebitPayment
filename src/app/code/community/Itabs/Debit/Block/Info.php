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
 * Debit Info Block
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Block_Info extends Mage_Payment_Block_Info
{
    /**
     * Construct payment info block and set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        if (Mage::helper('debit')->getDebitType() == 'bank') {
            $this->setTemplate('debit/bank/info.phtml');
        } else {
            $this->setTemplate('debit/info.phtml');
        }
    }

    /**
     * Sets the template for PDF print-outs
     *
     * @return string Text for PDF print-out
     */
    public function toPdf()
    {
        if (Mage::helper('debit')->getDebitType() == 'bank') {
            $this->setTemplate('debit/bank/debit.phtml');
        } else {
            $this->setTemplate('debit/debit.phtml');
        }

        return $this->toHtml();
    }

    /**
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
                    return true; // Admin
                } else {
                    return false; // Admin View
                }
            } else {
                return true; // Frontend
            }
        }
    }

    /**
     * Returns the config setting if bank data should be send in the email
     *
     * @return boolean true/false
     */
    public function sendDataInEmail()
    {
        return Mage::getStoreConfigFlag('payment/debit/sendmail');
    }

    /**
     * Returns email data and mask the data if necessary
     *
     * @return array Bank data
     */
    public function getEmailData()
    {
        /* @var $payment Itabs_Debit_Model_Debit */
        $payment = $this->getMethod();
        $method  = $this->getMethod()->getCode();

        if (Mage::helper('debit')->getDebitType() == 'bank') {
            $data = array(
                'account_name' => $payment->getAccountName(),
                'account_blz' => $payment->getAccountBLZ(),
                'account_number' => $payment->getAccountNumber()
            );
        } else {
            $data = array(
                'account_name' => $payment->getAccountName(),
                'account_swift' => $payment->getAccountSwift(),
                'account_iban' => $payment->getAccountIban()
            );
        }

        // mask bank data
        if (Mage::getStoreConfigFlag('payment/'.$method.'/sendmail_crypt')) {
            // @TODO: Mask swift / iban
        }

        return $data;
    }
}

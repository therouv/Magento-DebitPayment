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
 * @version   1.1.3
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
/**
 * Debit Info Block
 */
class Itabs_Debit_Block_Info extends Mage_Payment_Block_Info
{
    /**
     * Construct payment info block and set template
     */
    protected function _construct()
    {
        parent::_construct();

        /* @var $helper Itabs_Debit_Helper_Data */
        $helper = Mage::helper('debit');
        if ($helper->getDebitType() == Itabs_Debit_Helper_Data::DEBIT_TYPE_SEPA) {
            $this->setTemplate('debit/sepa/info.phtml');
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
        /* @var $helper Itabs_Debit_Helper_Data */
        $helper = Mage::helper('debit');
        if ($helper->getDebitType() == Itabs_Debit_Helper_Data::DEBIT_TYPE_SEPA) {
            $this->setTemplate('debit/sepa/debit.phtml');
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
        if ($info instanceof Mage_Sales_Model_Order_Payment) {
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

        return false;
    }

    /**
     * Returns the config setting if bank data should be send in the email
     *
     * @return boolean true/false
     */
    public function sendDataInEmail()
    {
        $method = $this->getMethod()->getCode();

        return Mage::getStoreConfigFlag('payment/'.$method.'/sendmail');
    }

    /**
     * Retrieve the debit type
     *
     * @return string
     */
    public function getDebitType()
    {
        return Mage::helper('debit')->getDebitType();
    }

    /**
     * Returns email data and mask the data if necessary
     *
     * @param  bool|string $cryptField Crypt field name to check in system configuration
     * @return array Bank data
     */
    public function getDebitData($cryptField=false)
    {
        $debitType = $this->getDebitType();

        /* @var $payment Itabs_Debit_Model_Debit */
        $payment = $this->getMethod();
        $method  = $this->getMethod()->getCode();
        $data = array(
            'account_name'   => $payment->getAccountName(),
            'account_number' => $payment->getAccountNumber(),
            'account_blz'    => $payment->getAccountBLZ(),
            'bank_name'      => $payment->getAccountBankname(),
            'account_swift'  => $payment->getAccountSwift(),
            'account_iban'   => $payment->getAccountIban(),
            'debit_type'     => $debitType
        );

        // Crypt data if configured
        if ($cryptField && Mage::getStoreConfigFlag('payment/'.$method.'/'.$cryptField)) {
            $data['bank_name'] = '';

            if ($debitType == 'bank') {
                $number  = $payment->maskBankData($payment->getAccountNumber());
                $routing = $payment->maskBankData($payment->getAccountBLZ());
                $data['account_number'] = $number;
                $data['account_blz']    = $routing;
            }

            // mask sepa data
            if ($debitType == 'sepa') {
                $swift = $payment->maskSepaData($payment->getAccountSwift());
                $iban  = $payment->maskSepaData($payment->getAccountIban());
                $data['account_swift'] = $swift;
                $data['account_iban']  = $iban;
            }
        }

        return $data;
    }

    /**
     * Returns email data and mask the data if necessary
     *
     * @deprecated since 1.1.0
     * @return array Bank data
     */
    public function getEmailData()
    {
        return $this->getDebitData('sendmail_crypt');
    }

    /**
     * Retrieve the debit pdf message
     *
     * @return bool|string
     */
    public function getPdfMessage()
    {
        // Check if we already have an order
        if (!($this->getInfo() instanceof Mage_Sales_Model_Order_Payment)) {
            return false;
        }

        /* @var $_coreHelper Mage_Core_Helper_Data */
        $_coreHelper = Mage::helper('core');
        /* @var $info Mage_Sales_Model_Order_Payment */
        $info = $this->getInfo();
        /* @var $order Mage_Sales_Model_Order */
        $order = $info->getOrder();

        $storeId = $order->getStoreId();

        // Check if we can print this message
        if (!Mage::getStoreConfigFlag('payment/debit/print_debit_message_pdf', $storeId)) {
            return false;
        }

        $message    = Mage::getStoreConfig('payment/debit/print_debit_message_text', $storeId);
        $offsetDays = Mage::helper('debit')->getOffset($storeId);

        /* @var $info Mage_Sales_Model_Order_Payment */
        $info = $this->getInfo();

        // Get values for placeholders
        $amount              = $_coreHelper->formatCurrency($info->getOrder()->getGrandTotal(), false);
        $mandate             = $order->getIncrementId();
        $creditorIdentNumber = Mage::getStoreConfig('debitpayment/sepa/creditor_identification_number', $storeId);

        $debitDayObj = $order->getCreatedAtStoreDate();
        $debitDayObj->addDay($offsetDays);
        $debitDay = $_coreHelper->formatDate($debitDayObj, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        // Replace placeholders
        $message = str_replace(
            array('{{amount}}', '{{mandate}}', '{{creditor_identification_number}}', '{{debit_day}}'),
            array($amount, $mandate, $creditorIdentNumber, $debitDay),
            $message
        );

        $transportObject = new Varien_Object();
        $transportObject->setData('message', $message);
        Mage::dispatchEvent('itabs_debit_pdf_message', array('message' => $message));

        return $transportObject->getData('message');
    }
}

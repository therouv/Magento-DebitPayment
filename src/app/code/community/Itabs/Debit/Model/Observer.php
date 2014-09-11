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
 * Class Itabs_Debit_Model_Observer
 */
class Itabs_Debit_Model_Observer
{
    /**
     * paymentMethodIsActive
     *
     * Checks if DebitPayment is allowed for specific customer groups and if a
     * registered customer has the required minimum amount of orders to be
     * allowed to order via DebitPayment.
     *
     * Event <payment_method_is_active>
     *
     * @param  Varien_Event_Observer $observer Observer
     * @return bool
     */
    public function paymentMethodIsActive($observer)
    {
        $methodInstance = $observer->getEvent()->getMethodInstance();

        // Check if method is DebitPayment
        if ($methodInstance->getCode() != 'debit') {
            return false;
        }

        // Check if payment method is active
        if (!Mage::getStoreConfigFlag('payment/debit/active')) {
            return false;
        }

        /* @var $validationModel Itabs_Debit_Model_Validation */
        $validationModel = Mage::getModel('debit/validation');
        $observer->getEvent()->getResult()->isAvailable = $validationModel->isValid();
    }

    /**
     * Saves the account data after a successful order in the specific
     * customer model.
     *
     * Event <sales_order_save_after>
     *
     * @param Varien_Event_Observer $observer Observer
     */
    public function saveAccountInfo($observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        /* @var $methodInstance Itabs_Debit_Model_Debit */
        $methodInstance = $order->getPayment()->getMethodInstance();

        if ($methodInstance->getCode() != 'debit') {
            return;
        }
        if (!$methodInstance->getConfigData('save_account_data')) {
            return;
        }

        if ($customer = $this->_getOrderCustomer($order)) {
            $customer->setData('debit_payment_acount_update', now())
                ->setData('debit_payment_acount_name', $methodInstance->getAccountName())
                ->setData('debit_payment_acount_number', $methodInstance->getAccountNumber())
                ->setData('debit_payment_acount_blz', $methodInstance->getAccountBLZ())
                ->setData('debit_payment_account_swift', $methodInstance->getAccountSwift())
                ->setData('debit_payment_account_iban', $methodInstance->getAccountIban())
                ->save();
        }
    }

    /**
     * Checks the current order and returns the customer model
     *
     * @param  Mage_Sales_Model_Order $order Current order
     * @return Mage_Customer_Model_Customer|null Customer model or null
     */
    protected function _getOrderCustomer($order)
    {
        if ($customer = $order->getCustomer()) {
            if ($customer->getId()) {
                return $customer;
            }
        }

        return false;
    }

    /**
     * Encrypt bank data in the adminhtml
     *
     * Event <encryptBankDataInAdminhtmlQuote>
     *
     * @param  Varien_Event_Observer $observer Observer
     * @return Itabs_Debit_Model_Observer
     */
    public function encryptBankDataInAdminhtmlQuote(Varien_Event_Observer $observer)
    {
        // Check if the payment data has already been processed
        if (!Mage::registry('debit_payment_quote_data_processed')) {
            /* @var $payment Mage_Sales_Model_Quote_Payment */
            $payment = $observer->getEvent()->getPayment();
            $this->_encryptPaymentData($payment);

            Mage::register('debit_payment_quote_data_processed', true, true);
        }
    }

    /**
     * Decrypt bank data in the adminhtml
     *
     * Event <encryptBankDataInAdminhtmlOrder>
     *
     * @param  Varien_Event_Observer $observer Observer
     * @return Itabs_Debit_Model_Observer
     */
    public function encryptBankDataInAdminhtmlOrder(Varien_Event_Observer $observer)
    {
        $request = Mage::app()->getRequest();
        if (!$request) {
            return $this;
        }

        // Skip all order save processes except the sales_order_create_save process in the backend
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        if ($controller != 'sales_order_create' || $action != 'save') {
            return $this;
        }

        // Check if the payment data has already been processed
        if (!Mage::registry('debit_payment_order_data_processed')) {
            /* @var $payment Mage_Sales_Model_Order_Payment */
            $payment = $observer->getEvent()->getPayment();
            $this->_encryptPaymentData($payment);

            Mage::register('debit_payment_order_data_processed', true, true);
        }
    }

    /**
     * Encrypt the payment data for the given payment model
     *
     * @param Mage_Sales_Model_Quote_Payment|Mage_Sales_Model_Order_Payment $payment Payment Model
     */
    protected function _encryptPaymentData($payment)
    {
        $method = $payment->getMethodInstance();
        if ($method instanceof Itabs_Debit_Model_Debit) {
            $info = $method->getInfoInstance();
            if ($payment->getData('debit_swift') != '') {
                $payment->setData('debit_swift', $info->encrypt($payment->getData('debit_swift')));
            }
            if ($payment->getData('debit_iban') != '') {
                $payment->setData('debit_iban', $info->encrypt($payment->getData('debit_iban')));
            }
        }
    }

    /**
     * Dynamically add layout handle if the customer calls the sepa page via customer account
     *
     * Event <controller_action_layout_load_before>
     *
     * @param Varien_Event_Observer $observer Observer
     */
    public function controllerActionLayoutLoadBefore(Varien_Event_Observer $observer)
    {
        /* @var $action Itabs_Debit_MandateController */
        $action = $observer->getEvent()->getAction();

        $fullActionName = $action->getFullActionName();
        if ($fullActionName == 'debit_mandate_index') {
            if ($action->getRequest()->getParam('account', false)) {
                /* @var $layout Mage_Core_Model_Layout */
                $layout = $observer->getEvent()->getLayout();
                $layout->getUpdate()->addHandle('debit_dynamic_layout_handle');
            }
        }
    }
}

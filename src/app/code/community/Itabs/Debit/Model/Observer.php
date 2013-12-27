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
 * Observer
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2014 ITABS GmbH (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
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
     * Event: <payment_method_is_active>
     *
     * @param  Varien_Event_Observer $observer Observer
     * @return void
     */
    public function paymentMethodIsActive($observer)
    {
        $methodInstance = $observer->getEvent()->getMethodInstance();

        // Check if method is DebitPayment
        if ($methodInstance->getCode() != 'debit') {
            return;
        }

        // Check if payment method is active
        if (!Mage::getStoreConfigFlag('payment/debit/active')) {
            return;
        }

        /* @var $validationModel Itabs_Debit_Model_Validation */
        $validationModel = Mage::getModel('debit/validation');
        $observer->getEvent()->getResult()->isAvailable = $validationModel->isValid();
    }

    /**
     * Saves the account data after a successful order in the specific
     * customer model.
     *
     * Event: <sales_order_save_after>
     *
     * @param  Varien_Event_Observer $observer Observer
     * @return void
     */
    public function saveAccountInfo($observer)
    {
        $order = $observer->getEvent()->getOrder();

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
                ->setData('debit_payment_account_swift', $methodInstance->getAccountSwift())
                ->setData('debit_payment_account_iban', $methodInstance->getAccountIban())
                ->save();
        }
    }

    /**
     * Checks the current order and returns the customer model
     *
     * @param  Mage_Sales_Model_Order            $order Current order
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
     * Stop save order process if customer didn't fill in the required sepa
     * information if debit payment is the selected payment method.
     *
     * @param  Varien_Event_Observer $observer
     * @return Itabs_Debit_Model_Observer
     */
    public function controllerActionPredispatchCheckoutOnepageSaveOrder(Varien_Event_Observer $observer)
    {
        $payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance();
        if ($payment->getCode() != 'debit') {
            return $this;
        }

        $request = Mage::app()->getRequest();
        $controller = $observer->getEvent()->getControllerAction();

        if ($request->getParam('mandate_city') == ''
            || !$request->getParam('mandate_accept', false)
            || $request->getParam('mandate_accept') != 1
        ) {
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = Mage::helper('debit')->__('Please agree to grant us the SEPA direct debit mandate or fill in the city of mandate signature. Thank you.');
            Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

            $controller->setFlag(
                $controller->getRequest()->getActionName(),
                Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,
                true
            );
        }

        return $this;
    }

    /**
     * Save the mandate reference in the database for further processing.
     *
     * @param  Varien_Event_Observer $observer
     * @return Itabs_Debit_Model_Observer
     */
    public function checkoutTypeOnepageSaveOrderAfter(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        $method = $order->getPayment()->getMethodInstance()->getCode();
        if ($method != 'debit') {
            return $this;
        }

        $customerId = $order->getCustomerId();
        if (null === $customerId) {
            $customerId = 0;
        }

        $data = array(
            'order_id' => $order->getId(),
            'website_id' => $order->getStore()->getWebsiteId(),
            'store_id' => $order->getStoreId(),
            'increment_id' => $order->getIncrementId(),
            'mandate_reference' => Mage::helper('debit')->getMandateReference($customerId, $order->getQuoteId()),
            'mandate_city' => Mage::app()->getRequest()->getParam('mandate_city'),
            'is_generated' => 0
        );

        try {
            /* @var $mandate Itabs_Debit_Model_Mandates */
            $mandate = Mage::getModel('debit/mandates');
            $mandate->addData($data);
            $mandate->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}

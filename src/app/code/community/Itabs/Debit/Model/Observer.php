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
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
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
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
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
     * @magentoEvent payment_method_is_active
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
     * @magentoEvent sales_order_save_after
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
}

<?php
/**
 * This file is part of the Mage_Debit module.
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
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
/**
 * Observer
 *
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
class Mage_Debit_Model_Observer
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
        $session = Mage::getSingleton('customer/session');

        // Check if method is DebitPayment
        if ($methodInstance->getCode() != 'debit') {
            return;
        }
        // Check if payment method is active
        if (!Mage::getStoreConfigFlag('payment/debit/active')) {
            return;
        }

        // Check if payment is allowed only for specific customer groups
        if (!Mage::getStoreConfigFlag('payment/debit/specificgroup_all')) {
            $customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
            if ($session->isLoggedIn()) {
                $customerGroupId = $session->getCustomerGroupId();
            }
            $allowedGroupIds = explode(',', Mage::getStoreConfig('payment/debit/specificgroup'));
            if (!in_array($customerGroupId, $allowedGroupIds)) {
                $observer->getEvent()->getResult()->isAvailable = false;

                return;
            }
        }

        // Check minimum orders count
        $minOrderCount = Mage::getStoreConfig('payment/debit/orderscount');
        if ($minOrderCount > 0) {
            $customerId = $session->getCustomerId();
            if (is_null($customerId)) { // not logged in
                $observer->getEvent()->getResult()->isAvailable = false;

                return;
            }
            // Load orders and check
            $orders = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $customerId)
                ->addAttributeToFilter('status', Mage_Sales_Model_Order::STATE_COMPLETE)
                ->addAttributeToFilter(
                    'state',
                    array(
                        'in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()
                    )
                )
                ->load();
            if (count($orders) < $minOrderCount) {
                $observer->getEvent()->getResult()->isAvailable = false;

                return;
            }
        }
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
                ->save();
        }
    }

    /**
     * Checks the current order and returns the customer model
     *
     * @param  Mage_Sales_Model_Order            $order Current order
     * @return Mage_Customer_Model_Customer|null Customer model or null
     */
    public function _getOrderCustomer($order)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            if ($customer = $order->getCustomer()) {
                return $customer;
            }
        } else {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer->getId()) {
                return $customer;
            }
        }

        return null;
    }
}

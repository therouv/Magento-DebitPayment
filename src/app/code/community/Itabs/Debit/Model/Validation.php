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
 * Validation model
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Model_Validation
{
    /**
     * @var null|Mage_Sales_Model_Resource_Order_Collection
     */
    protected $_customerOrders = null;

    /**
     * @var null|Mage_Sales_Model_Resource_Order_Collection
     */
    protected $_customerOrdersEmail = null;

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->hasSpecificCustomerGroup()
            && $this->hasMinimumOrderCount()
            && $this->hasMinimumOrderAmount()
            ;
    }

    /**
     * Check if the customer is in a specific customer group
     *
     * @return bool
     */
    public function hasSpecificCustomerGroup()
    {
        if (!Mage::getStoreConfigFlag('payment/debit/specificgroup_all')) {
            $allowedGroupIds = explode(',', Mage::getStoreConfig('payment/debit/specificgroup'));
            if (!in_array($this->_getCustomerGroupId(), $allowedGroupIds)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the customer has placed less complete orders than required..
     *
     * @return bool
     */
    public function hasMinimumOrderCount()
    {
        $minOrderCount = Mage::getStoreConfig('payment/debit/orderscount');
        if ($minOrderCount > 0) {
            $customerId = $this->_getCustomer()->getId();
            if (is_null($customerId)) {
                return false;
            }

            $orders = $this->_getCustomerOrders($customerId);
            if (count($orders) < $minOrderCount) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the order amount of all customer order are below the
     * required order amount
     *
     * @return bool
     */
    public function hasMinimumOrderAmount()
    {
        $minOrderSum = Mage::getStoreConfig('payment/debit/customer_order_amount');
        if ($minOrderSum > 0) {
            $customerId = $this->_getCustomer()->getId();
            if (is_null($customerId)) {
                return false;
            }

            $orders = $this->_getCustomerOrders($customerId);
            $orderTotal = 0;
            foreach ($orders as $order) {
                $orderTotal += $order->getData('grand_total');
            }

            if ($orderTotal < $minOrderSum) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieve the current session
     *
     * @return Mage_Adminhtml_Model_Session_Quote|Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            /* @var $session Mage_Adminhtml_Model_Session_Quote */
            $session = Mage::getSingleton('adminhtml/session_quote');
        } else {
            /* @var $session Mage_Customer_Model_Session */
            $session = Mage::getSingleton('customer/session');
        }

        return $session;
    }

    /**
     * Retrieve the current customer
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Retrieve the customer group id of the current customer
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        $customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
        if (Mage::app()->getStore()->isAdmin()) {
            $customerGroupId = $this->_getSession()->getQuote()->getCustomerGroupId();
        } else {
            if ($this->_getSession()->isLoggedIn()) {
                $customerGroupId = $this->_getSession()->getCustomerGroupId();
            }
        }

        return $customerGroupId;
    }

    /**
     * Retrieve the email address of the current customer
     *
     * @return string
     */
    protected function _getCustomerEmail()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $email = $this->_getCustomer()->getEmail();
        } else {
            if ($this->_getSession()->isLoggedIn()) {
                $email = $this->_getCustomer()->getEmail();
            } else {
                /* @var $quote Mage_Sales_Model_Quote */
                $quote = Mage::getSingleton('checkout/session')->getQuote();
                $email = $quote->getBillingAddress()->getEmail();
            }
        }

        return $email;
    }

    /**
     * Retrieve the order collection of a specific customer
     *
     * @param  int $customerId
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCustomerOrders($customerId)
    {
        if (null === $this->_customerOrders) {
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
            $this->_customerOrders = $orders;
        }

        return $this->_customerOrders;
    }
}

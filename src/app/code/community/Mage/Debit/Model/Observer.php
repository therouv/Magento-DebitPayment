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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Debit_Model_Observer
{
    /**
     * saveAccountInfo
     * 
     * Saves the account data after a successful order in the specific customer model.
     * Observer: sales_order_save_after
     * 
     * @param Varien_Event_Observer $observer Observer
     */
    public function saveAccountInfo($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $paymentMethodInstance = $order->getPayment()->getMethodInstance();
        if ($paymentMethodInstance->getCode() != 'debit') {
            return;
        }
        if (!$paymentMethodInstance->getConfigData('save_account_data')) {
            return;
        }
        if ($customer = $this->_getOrderCustomer($order)) {
            $customer->setData('debit_payment_acount_data_update', now())
                ->setData('debit_payment_acount_name', $paymentMethodInstance->getAccountName())
                ->setData('debit_payment_acount_number', $paymentMethodInstance->getAccountNumber())
                ->setData('debit_payment_acount_blz', $paymentMethodInstance->getAccountBLZ())
                ->save();
        }
    }

    /**
     * _getOrderCustoemr
     * 
     * Checks the current order and returns the customer model
     * 
     * @param Mage_Sales_Model_Order $order Current order
     * 
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
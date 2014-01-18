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
 * Helper class for helper functionalities especially in the adminhtml area..
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Helper_Adminhtml extends Itabs_Debit_Helper_Data
{
    const XML_PATH_BANKACCOUNT_ACCOUNTOWNER  = 'debitpayment/bankaccount/account_owner';
    const XML_PATH_BANKACCOUNT_SWIFT = 'debitpayment/bankaccount/account_swift';
    const XML_PATH_BANKACCOUNT_IBAN = 'debitpayment/bankaccount/account_iban';

    /**
     * Check if the export requirements are reached for export. Store owner
     * has to enter his bank account data.
     *
     * @return bool Result
     */
    public function hasExportRequirements()
    {
        if (Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ACCOUNTOWNER) == ''
            || Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_IBAN) == ''
            || Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_SWIFT) == ''
        ) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve a list of already synced orders, so that a single order is not
     * exported multiple times.
     *
     * @return array Orders
     */
    public function getSyncedOrders()
    {
        $entityIds = array();

        /* @var $collection Itabs_Debit_Model_Resource_Orders_Collection */
        $collection = Mage::getResourceModel('debit/orders_collection');
        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                $entityIds[] = $item->getData('entity_id');
            }
        }

        return $entityIds;
    }

    /**
     * Updates the status of an export order item to "exported"..
     *
     * @param  int $orderId Export Order ID
     * @return bool Result
     */
    public function setStatusAsExported($orderId)
    {
        $model = Mage::getModel('debit/orders')->load($orderId);
        $model->setData('status', 1);
        $model->save();

        return true;
    }

    /**
     * Retrieve the correct booking text with the configurable text
     *
     * @param  int    $storeId
     * @param  string $incrementId
     * @return string
     */
    public function getBookingText($storeId, $incrementId)
    {
        $bookingText = array(
            Mage::getStoreConfig('debitpayment/sepa/booking_text', $storeId),
            $incrementId
        );
        return implode(' ', $bookingText);
    }
}

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
 * Helper class for helper functionalities especially in the adminhtml area..
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Helper_Adminhtml extends Itabs_Debit_Helper_Data
{
    const XML_PATH_BANKACCOUNT_ACCOUNTOWNER  = 'debitpayment/bankaccount/account_owner';
    const XML_PATH_BANKACCOUNT_ROUTINGNUMBER = 'debitpayment/bankaccount/routing_number';
    const XML_PATH_BANKACCOUNT_ACCOUNTNUMBER = 'debitpayment/bankaccount/account_number';

    /**
     * Check if the export requirements are reached for export. Store owner
     * has to enter his bank account data.
     *
     * @return bool
     */
    public function hasExportRequirements()
    {
        if (Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ACCOUNTOWNER) == ''
            || Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ROUTINGNUMBER) == ''
            || Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ACCOUNTNUMBER) == ''
        ) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve the bank account data of the store owenr as array
     *
     * @return array Bank account
     */
    public function getBankAccount()
    {
        return array(
            'name'           => Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ACCOUNTOWNER),
            'bank_code'      => Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ROUTINGNUMBER),
            'account_number' => Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ACCOUNTNUMBER)
        );
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
        $collection = Mage::getModel('debit/orders')->getCollection();
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
     * @param  int  $orderId Export Order ID
     * @return bool
     */
    public function setStatusAsExported($orderId)
    {
        $model = Mage::getModel('debit/orders')->load($orderId);
        $model->setData('status', 1);
        $model->save();

        return true;
    }
}

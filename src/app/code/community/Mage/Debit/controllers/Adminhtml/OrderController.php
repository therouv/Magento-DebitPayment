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
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
require_once (Mage::getBaseDir().'/lib/DTA/DTA.php');
/**
 * Export Order Controller
 *
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
class Mage_Debit_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Loads the grid layout with the debit payment orders..
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sales/debitpayment');
        $this->renderLayout();
    }

    /**
     * Fetch all orders with the payment method "Debit Payment" and import them
     * into the export list (table: debit_order_grid)
     *
     * @return void
     */
    public function syncAction()
    {
        $syncedOrders = $this->_getDebitHelper()->getSyncedOrders();
        $syncedOrdersCount = 0;

        // Sync orders
        $collection = Mage::getResourceModel('sales/order_grid_collection');
        if (count($syncedOrders) > 0) {
            $collection->addFieldToFilter('entity_id', array('nin' => $syncedOrders));
        }

        /* @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $salesFlatOrderTable = $resource->getTableName('sales_flat_order');
        $salesFlatOrderPaymentTable = $resource->getTableName('sales_flat_order_payment');

        $collection->getSelect()->joinLeft(
            $salesFlatOrderTable,
            $salesFlatOrderTable.'.entity_id = main_table.entity_id',
            array('customer_id')
        );
        $collection->getSelect()->joinLeft(
            $salesFlatOrderPaymentTable,
            $salesFlatOrderPaymentTable.'.parent_id = main_table.entity_id',
            array('method')
        );
        $collection->getSelect()->where('method = ?', 'debit');

        foreach ($collection as $order) {
            /* @var $order Mage_Sales_Model_Order */

            // Remove some values from the data array
            $unsetData = array('status', 'base_grand_total', 'base_total_paid', 'total_paid', 'updated_at', 'method', 'shipping_name', 'base_currency_code', 'store_name');
            foreach ($unsetData as $key) {
                $order->unsetData($key);
            }

            /* @var $model Mage_Debit_Model_Orders */
            $model = Mage::getModel('debit/orders');
            $model->addData($order->getData());
            $model->save();

            $syncedOrdersCount++;
        }

        if ($syncedOrdersCount > 0) {
            $this->_getSession()->addSuccess(
                $this->_getDebitHelper()->__('Orders successfully synced for export.')
            );
        } else {
            $this->_getSession()->addError(
                $this->_getDebitHelper()->__('No orders available for sync.')
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Export the order list as DTA file
     *
     * @return void|Mage_Core_Controller_Varien_Action
     */
    public function exportdtausAction()
    {
        $collection = $this->_hasOrdersToExport();
        if (!$collection) {
            $this->_redirect('*/*');

            return;
        }

        // Create new object and set store owner bank account data
        $file = new DTA(DTA_DEBIT);
        $file->setAccountFileSender($this->_getDebitHelper()->getBankAccount());

        // Add orders
        foreach ($collection as $order) {
            /* @var $orderModel Mage_Sales_Model_Order */
            $orderModel = Mage::getModel('sales/order')->load($order->getData('entity_id'));
            /* @var $payment Mage_Debit_Model_Debit */
            $paymentMethod = $orderModel->getPayment()->getMethodInstance();

            $file->addExchange(
                array(
                    'name'           => $paymentMethod->getAccountName(),
                    'bank_code'      => $paymentMethod->getAccountBLZ(),
                    'account_number' => $paymentMethod->getAccountNumber(),
                ),
                round($order->getData('grand_total'), 2),
                array(
                    'Bestellung Nr. '.$order->getData('increment_id')
                )
            );

            $this->_getDebitHelper()->setStatusAsExported($order->getId());
        }

        return $this->_prepareDownloadResponse('EXPORT'.date('YmdHis'), $file->getFileContent());
    }

    /**
     * Export the order list as CSV
     *
     * @return void|Mage_Core_Controller_Varien_Action
     */
    public function exportcsvAction()
    {
        $collection = $this->_hasOrdersToExport();
        if (!$collection) {
            $this->_redirect('*/*');

            return;
        }

        $fileName = 'EXPORT'.date('YmdHis').'.csv';

        // Open file
        $io = new Varien_Io_File();
        $io->open(array('path' => Mage::getBaseDir('var')));
        $io->streamOpen($fileName);

        // Add headline
        $row = array(
            'Kundenname',
            'BLZ',
            'Kontonummer',
            'Betrag',
            'Verwendungszweck'
        );
        $io->streamWriteCsv($row);

        // Add rows
        foreach ($collection as $order) {
            /* @var $orderModel Mage_Sales_Model_Order */
            $orderModel = Mage::getModel('sales/order')->load($order->getData('entity_id'));
            /* @var $payment Mage_Debit_Model_Debit */
            $paymentMethod = $orderModel->getPayment()->getMethodInstance();

            $row = array(
                'name'           => $paymentMethod->getAccountName(),
                'bank_code'      => $paymentMethod->getAccountBLZ(),
                'account_number' => $paymentMethod->getAccountNumber(),
                'amount'         => number_format($order->getData('grand_total'), 2, ',', '.').' '.$order->getData('order_currency_code'),
                'purpose'        => 'Bestellung Nr. '.$order->getData('increment_id')
            );
            $io->streamWriteCsv($row);

            $this->_getDebitHelper()->setStatusAsExported($order->getId());
        }

        // Close file, get file contents and delete temporary file
        $io->close();
        $filePath = Mage::getBaseDir('var') . DS . $fileName;
        $fileContents = file_get_contents($filePath);
        $io->rm($fileName);

        return $this->_prepareDownloadResponse($fileName, $fileContents);
    }

    /**
     * Check if there are orders available for export..
     *
     * @return void
     */
    protected function _hasOrdersToExport()
    {
        $collection = Mage::getModel('debit/orders')->getCollection()->addFieldToFilter('status', 0);
        if ($collection->count() == 0) {
            $this->_getSession()->addError($this->_getDebitHelper()->__('No orders to export.'));

            return false;
        }

        return $collection;
    }

    /**
     * Retrieve the helper class
     *
     * @return Mage_Debit_Helper_Adminhtml Helper
     */
    protected function _getDebitHelper()
    {
        return Mage::helper('debit/adminhtml');
    }
}

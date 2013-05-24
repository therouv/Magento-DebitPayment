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
 * Export Order Controller
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Loads the grid layout with the debit payment orders..
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_getSession()->addNotice(
            $this->_getDebitHelper()->__('Please note: SEPA Debit Payment orders can only be exported as CSV for now.')
        );

        $this->loadLayout();
        $this->_setActiveMenu('sales/debitpayment')
            ->_addBreadcrumb(
                $this->_getHelper()->__('Sales'),
                $this->_getHelper()->__('Sales')
            )
            ->_addBreadcrumb(
                $this->_getDebitHelper()->__('Debit Payment Orders'),
                $this->_getDebitHelper()->__('Debit Payment Orders')
            )
            ->_title($this->_getHelper()->__('Sales'))
            ->_title($this->_getDebitHelper()->__('Debit Payment Orders'));

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
        $collection->addFieldToFilter('main_table.status', array('nin' => array('canceled', 'fraud', 'holded')));
        if (count($syncedOrders) > 0) {
            $collection->addFieldToFilter('main_table.entity_id', array('nin' => $syncedOrders));
        }

        /* @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $orderTable = $resource->getTableName('sales_flat_order');
        $orderPaymentTable = $resource->getTableName('sales_flat_order_payment');

        $collection->getSelect()->joinLeft(
            $orderTable,
            $orderTable.'.entity_id = main_table.entity_id',
            array('customer_id')
        );
        $collection->getSelect()->joinLeft(
            $orderPaymentTable,
            $orderPaymentTable.'.parent_id = main_table.entity_id',
            array('method', 'debit_type')
        );
        $collection->getSelect()->where('method = ?', 'debit');

        foreach ($collection as $order) {
            /* @var $order Mage_Sales_Model_Order */

            // Remove some values from the data array
            $unsetData = array(
                'status', 'base_grand_total', 'base_total_paid', 'total_paid',
                'updated_at', 'method', 'shipping_name', 'base_currency_code',
                'store_name'
            );
            foreach ($unsetData as $key) {
                $order->unsetData($key);
            }

            /* @var $model Itabs_Debit_Model_Orders */
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
     * Update order status action
     *
     * @return void
     */
    public function massStatusAction()
    {
        $orderIds = (array) $this->getRequest()->getParam('orders');
        $status   = (int) $this->getRequest()->getParam('status');

        try {
            foreach ($orderIds as $orderId) {
                $order = Mage::getModel('debit/orders')->load($orderId);
                $order->setData('status', $status);
                $order->save();
            }

            $this->_getSession()->addSuccess(
                $this->_getDebitHelper()->__(
                    'Total of %d record(s) have been updated.',
                    count($orderIds)
                )
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException(
                    $e,
                    $this->__('An error occurred while updating the status.')
                );
        }

        $this->_redirect('*/*/');
    }

    /**
     * Export the order list as DTA file
     *
     * @return void|Mage_Core_Controller_Varien_Action
     */
    public function exportdtausAction()
    {
        return $this->_export('dtaus');
    }

    /**
     * Export the order list as CSV
     *
     * @return void|Mage_Core_Controller_Varien_Action
     */
    public function exportcsvAction()
    {
        return $this->_export('csv');
    }

    /**
     * @param string $type
     * @return Mage_Core_Controller_Varien_Action
     */
    protected function _export($type)
    {
        $response = Mage::getModel('debit/export_'.$type)->export();
        if (!$response) {
            $this->_redirect('*/*');
            return;
        }

        return $this->_prepareDownloadResponse(
            $response['file_name'],
            $response['file_content']
        );
    }

    /**
     * Retrieve the helper class
     *
     * @return Itabs_Debit_Helper_Adminhtml Helper
     */
    protected function _getDebitHelper()
    {
        return Mage::helper('debit/adminhtml');
    }
}

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
 * Order Export Grid
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('debitpayment_order_grid');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare the grid collection
     *
     * @return Itabs_Debit_Block_Adminhtml_Order_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('debit/orders')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare the grid columns
     *
     * @return Itabs_Debit_Block_Adminhtml_Order_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            array(
                'header' => $this->_getHelper()->__('Order #'),
                'width' => '90px',
                'index' => 'increment_id'
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('sales')->__('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view'=> true,
                'display_deleted' => true,
                'width' => 125
            ));
        }
        $this->addColumn(
            'created_at',
            array(
                'header' => $this->_getHelper()->__('Purchased On'),
                'index' => 'created_at',
                'type' => 'datetime',
                'width' => '100px',
            )
        );
        $this->addColumn(
            'billing_name',
            array(
                'header' => $this->_getHelper()->__('Bill to Name'),
                'index' => 'billing_name',
            )
        );
        $this->addColumn(
            'grand_total',
            array(
                'header' => $this->_getHelper()->__('Grand Total'),
                'index' => 'grand_total',
                'type' => 'currency',
                'currency' => 'order_currency_code',
            )
        );

        $types = Mage::getModel('debit/system_config_source_debit_type')->toOptionHash();
        $this->addColumn(
            'debit_type',
            array(
                'header' => $this->_getHelper()->__('Debit Type'),
                'index' => 'debit_type',
                'type' => 'options',
                'options' => $types,
                'width' => '100px',
            )
        );

        $statuses = Mage::getSingleton('debit/system_config_source_debit_status')->toOptionHash();
        $this->addColumn(
            'status',
            array(
                'header' => $this->_getHelper()->__('Status'),
                'index' => 'status',
                'type' => 'options',
                'width' => '150px',
                'options' => $statuses
            )
        );

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header' => Mage::helper('sales')->__('Action'),
                    'width' => '50px',
                    'type' => 'action',
                    'getter' => 'getEntityId',
                    'actions' => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url' => array('base' => 'adminhtml/sales_order/view'),
                            'field' => 'order_id'
                        )
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'is_system' => true,
                )
            );
        }

        $this->addExportType('*/*/exportcsv', 'CSV');
        $this->addExportType('*/*/exportxml', 'SEPA-XML');

        return parent::_prepareColumns();
    }

    /**
     * Prepare the massaction fields
     *
     * @return Itabs_Debit_Block_Adminhtml_Order_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('orders');

        $values = Mage::getSingleton('debit/system_config_source_debit_status')
            ->toOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => Mage::helper('catalog')->__('Change status'),
                'url'   => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'visibility' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => $this->_getHelper()->__('Status'),
                        'values' => $values
                    )
                )
            )
        );

        return $this;
    }

    /**
     * Retrieve the row url
     *
     * @param  Varien_Object $row Database Entry
     * @return bool|string Row URL
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Retrieve the grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Retrieve the helper class
     *
     * @return Itabs_Debit_Helper_Adminhtml Helper Class
     */
    protected function _getHelper()
    {
        return Mage::helper('debit/adminhtml');
    }
}

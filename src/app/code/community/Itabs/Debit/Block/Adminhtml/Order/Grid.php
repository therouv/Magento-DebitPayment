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
 * Order Export Grid
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('debitpayment_order_grid');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * (non-PHPdoc)
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('debit/orders')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * (non-PHPdoc)
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareColumns()
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            array(
                'header' => $this->_getHelper()->__('Order #'),
                'width'  => '90px',
                'index'  => 'increment_id'
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => $this->_getHelper()->__('Purchased On'),
                'index'  => 'created_at',
                'type'   => 'datetime',
                'width'  => '100px',
            )
        );
        $this->addColumn(
            'billing_name',
            array(
                'header' => $this->_getHelper()->__('Bill to Name'),
                'index'  => 'billing_name',
            )
        );
        $this->addColumn(
            'grand_total',
            array(
                'header'   => $this->_getHelper()->__('Grand Total'),
                'index'    => 'grand_total',
                'type'     => 'currency',
                'currency' => 'order_currency_code',
            )
        );

        $types = Mage::getModel('debit/system_config_source_debit_type')
            ->toOptionHash();

        $this->addColumn(
            'debit_type',
            array(
                'header' => $this->_getHelper()->__('Debit Type'),
                'index'  => 'debit_type',
                'type'   => 'options',
                'options' => $types,
                'width'   => '100px',
            )
        );

        $statuses = Mage::getSingleton('debit/system_config_source_debit_status')
            ->toOptionHash();

        $this->addColumn(
            'status',
            array(
                'header'  => $this->_getHelper()->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'width'   => '150px',
                'options' => $statuses
            )
        );

        return parent::_prepareColumns();
    }

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
     * (non-PHPdoc)
     * @see Mage_Adminhtml_Block_Widget_Grid::getRowUrl()
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Retrieve the helper class
     *
     * @return Itabs_Debit_Helper_Adminhtml Helper
     */
    protected function _getHelper()
    {
        return Mage::helper('debit/adminhtml');
    }
}

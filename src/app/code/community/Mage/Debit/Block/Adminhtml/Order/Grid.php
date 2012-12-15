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
/**
 * Order Export Grid
 *
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
class Mage_Debit_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->addColumn(
            'status',
            array(
                'header'  => $this->_getHelper()->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'width'   => '150px',
                'options' => array(
                    0 => $this->_getHelper()->__('Not exported'),
                    1 => $this->_getHelper()->__('Exported')
                ),
            )
        );

        return parent::_prepareColumns();
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
     * @return Mage_Debit_Helper_Adminhtml Helper
     */
    protected function _getHelper()
    {
        return Mage::helper('debit/adminhtml');
    }
}

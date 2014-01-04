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
 * Mandates Grid
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Block_Adminhtml_Mandates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('debitpayment_mandates_grid');
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
        $collection = Mage::getModel('debit/mandates')->getCollection();
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
                'width'  => '90px',
                'index'  => 'increment_id'
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
            'mandate_reference',
            array(
                'header' => $this->_getHelper()->__('Mandate Reference'),
                'index' => 'mandate_reference'
            )
        );
        $this->addColumn(
            'mandate_city',
            array(
                'header' => $this->_getHelper()->__('Mandate City'),
                'index' => 'mandate_city'
            )
        );
        $this->addColumn(
            'is_generated',
            array(
                'header'  => $this->_getHelper()->__('Is Generated?'),
                'index'   => 'is_generated',
                'type'    => 'options',
                'width'   => '150px',
                'options' => array(
                    0 => 'Not generated',
                    1 => 'Generated'
                )
            )
        );
        $this->addColumn(
            'mandate_pdf',
            array(
                'header' => $this->_getHelper()->__('Mandate PDF'),
                'index' => 'mandate_pdf',
                'sortable' => false,
                'filter' => false,
                'renderer' => 'debit/adminhtml_mandates_renderer_pdf'
            )
        );

        return parent::_prepareColumns();
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

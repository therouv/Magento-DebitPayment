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
 * @author    ITABS GmbH <info@itabs.de>
 * @copyright 2008-2014 ITABS GmbH (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   1.1.3
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
/**
 * Bank Data Grid
 */
class Itabs_Debit_Block_Adminhtml_Bankdata_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('debitpayment_backdata_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare the grid collection
     *
     * @return Itabs_Debit_Block_Adminhtml_Bankdata_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('debit/bankdata_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare the grid columns
     *
     * @return Itabs_Debit_Block_Adminhtml_Bankdata_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => $this->_getHelper()->__('ID'),
                'width'  => '60px',
                'index'  => 'id'
            )
        );

        $options = $this->helper('debit/adminhtml')->getCountryOptionsHash();
        $this->addColumn(
            'country_id',
            array(
                'header' => $this->_getHelper()->__('Country'),
                'width'  => '150px',
                'index'  => 'country_id',
                'type' => 'options',
                'options' => $options
            )
        );

        $this->addColumn(
            'routing_number',
            array(
                'header' => $this->_getHelper()->__('Routing Number'),
                'width'  => '150px',
                'index'  => 'routing_number'
            )
        );

        $this->addColumn(
            'swift_code',
            array(
                'header' => $this->_getHelper()->__('BIC/SWIFT-Code'),
                'width'  => '150px',
                'index'  => 'swift_code'
            )
        );

        $this->addColumn(
            'bank_name',
            array(
                'header' => $this->_getHelper()->__('Bank Name'),
                'index'  => 'bank_name'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve the row url
     *
     * @param  Varien_Object $row Model
     * @return bool|string
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
        return $this->getUrl('debit/bankdata/grid', array('_current' => true));
    }

    /**
     * Retrieve the helper class
     *
     * @return Itabs_Debit_Helper_Adminhtml
     */
    protected function _getHelper()
    {
        return Mage::helper('debit/adminhtml');
    }
}

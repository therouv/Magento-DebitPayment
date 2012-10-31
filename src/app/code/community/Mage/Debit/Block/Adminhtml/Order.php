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
 * Backend View for Order Export list
 *
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
class Mage_Debit_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_order';
        $this->_blockGroup = 'debit';
        $this->_headerText     = Mage::helper('debit')->__('Debit Payment Orders');
        parent::__construct();
        $this->_removeButton('add');

        if (Mage::helper('debit/adminhtml')->hasExportRequirements()) {
            $this->_addButton('sync', array(
                'label'     => Mage::helper('debit')->__('Sync Orders'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/sync') .'\')',
                'class'     => 'add',
            ));
            $this->_addButton('export_dtaus', array(
                'label'     => Mage::helper('debit')->__('Export as DTAUS'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/exportdtaus') .'\')',
                'class'     => '',
            ));
            $this->_addButton('export_csv', array(
                'label'     => Mage::helper('debit')->__('Export as CSV'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/exportcsv') .'\')',
                'class'     => '',
            ));
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('debit')->__('Please enter bankaccount credentials of the store owner in the system configuration. Otherwise you will not be able to generate a valid export file.')
            );
        }
    }
}

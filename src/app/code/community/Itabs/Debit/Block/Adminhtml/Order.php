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
 * Backend View for Order Export list
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        /* @var $helper Itabs_Debit_Helper_Data */
        $helper = Mage::helper('debit');

        $this->_controller = 'adminhtml_order';
        $this->_blockGroup = 'debit';
        $this->_headerText = $helper->__('Debit Payment Orders');
        parent::__construct();
        $this->_removeButton('add');

        if (Mage::helper('debit/adminhtml')->hasExportRequirements()) {
            $this->_addButton('sync', array(
                'label'     => $helper->__('Sync Orders'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/sync') .'\')',
                'class'     => 'add',
            ));
            $this->_addButton('export_dtaus', array(
                'label'     => $helper->__('Export as DTAUS'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/exportdtaus') .'\')',
                'class'     => '',
            ));
            $this->_addButton('export_csv', array(
                'label'     => $helper->__('Export as CSV'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/exportcsv') .'\')',
                'class'     => '',
            ));
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                $helper->__('Please enter bankaccount credentials of the store owner in the system configuration.')
            );
        }
    }
}

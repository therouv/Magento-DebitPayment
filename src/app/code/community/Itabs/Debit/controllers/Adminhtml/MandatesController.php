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
 * Export Order Controller
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Adminhtml_MandatesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Loads the grid layout with the debit payment orders..
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sales/debitpayment/mandates')
            ->_addBreadcrumb(
                $this->_getHelper()->__('Sales'),
                $this->_getHelper()->__('Sales')
            )
            ->_addBreadcrumb(
                $this->getDebitHelper()->__('Debit Payment Mandates'),
                $this->getDebitHelper()->__('Debit Payment Mandates')
            )
            ->_title($this->_getHelper()->__('Sales'))
            ->_title($this->getDebitHelper()->__('Debit Payment Mandates'));

        $this->renderLayout();
    }

    /**
     * Loads the grid for grid ajax
     */
    public function gridAction()
    {
        /* @var $block Itabs_Debit_Block_Adminhtml_Mandates_Grid */
        $block = $this->getLayout()->createBlock('debit/adminhtml_mandates_grid');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Downloads the given mandate
     */
    public function downloadAction()
    {
        if ($mandateId = $this->getRequest()->getParam('mandate', false)) {
            $mandate = Mage::getModel('debit/mandates')->load($mandateId);
            if ($mandate->getId()) {
                $fileName = $mandate->getData('mandate_reference') . '.pdf';
                $filePath = Mage::getBaseDir('var') . DS . 'debit' . DS . $fileName;
                return $this->_prepareDownloadResponse($fileName, file_get_contents($filePath), 'application/pdf');
            }
        }

        return $this->_redirect('*/*');
    }

    /**
     * Retrieve the helper class
     *
     * @return Itabs_Debit_Helper_Adminhtml Helper Class
     */
    public function getDebitHelper()
    {
        return Mage::helper('debit/adminhtml');
    }
}

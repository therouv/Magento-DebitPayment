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
 * Mandate Controller
 */
class Itabs_Debit_MandateController extends Mage_Core_Controller_Front_Action
{
    /**
     * Controller Pre-Dispatch
     *
     * @return Mage_Core_Controller_Front_Action|void
     */
    public function preDispatch()
    {
        // Do not dispatch if not active
        if (!Mage::getStoreConfigFlag('debitpayment/sepa/mandate_form')) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }

        // Disable the full page cache for this pages
        $cache = Mage::app()->getCacheInstance();
        if ($cache && $cache->canUse('full_page')) {
            $cache->banUse('full_page');
        }

        return parent::preDispatch();
    }

    /**
     * Shows the sepa mandate generation form
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('SEPA Mandate'));
        }
        $this->renderLayout();
    }

    /**
     * Generate the pdf by given post data and trigger the download
     */
    public function printAction()
    {
        try {
            $post = $this->getRequest()->getPost();

            $date = Mage::getSingleton('core/date')->date('d-m-Y_H-i-s');
            $filename = Mage::helper('debit')->__('SEPA_Mandat') . '_' . $date . '.pdf';

            $pdf = Mage::getModel('debit/pdf_mandate')->getPdf($post);

            $this->_prepareDownloadResponse($filename, $pdf->render(), 'application/pdf');
        } catch (Exception $e) {
            $this->_getSession()->addError('Error while generating the mandate pdf. Please try again.');
            return $this->_redirect('*/*/index');
        }
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}

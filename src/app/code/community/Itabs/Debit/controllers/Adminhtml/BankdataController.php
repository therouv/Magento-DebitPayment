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
 * Bankdata Controller
 */
class Itabs_Debit_Adminhtml_BankdataController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init the layout
     *
     * @return Itabs_Debit_Adminhtml_BankdataController
     */
    protected function _initLayout()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/convert/debitpayment')
            ->_addBreadcrumb(
                $this->_getHelper()->__('System'),
                $this->_getHelper()->__('System')
            )
            ->_addBreadcrumb(
                $this->_getHelper()->__('Import/Export'),
                $this->_getHelper()->__('Import/Export')
            )
            ->_addBreadcrumb(
                $this->_getDebitHelper()->__('Bank Data'),
                $this->_getDebitHelper()->__('Bank Data')
            )
            ->_title($this->_getHelper()->__('System'))
            ->_title($this->_getHelper()->__('Import/Export'))
            ->_title($this->_getDebitHelper()->__('Bank Data'));

        return $this;
    }

    /**
     * Show the bankdata grid
     */
    public function indexAction()
    {
        $this->_initLayout();
        $this->renderLayout();
    }

    /**
     * Retrieve the bankdata grid e.g. for ajax calls
     */
    public function gridAction()
    {
        $block = $this->getLayout()->createBlock('debit/adminhtml_bankdata_grid')->toHtml();
        $this->getResponse()->setBody($block);
    }

    /**
     * Show the bankdata upload form
     */
    public function uploadAction()
    {
        $this->_initLayout();
        $this->renderLayout();
    }

    /**
     * Upload and import the given bankdata file
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            $country = $this->getRequest()->getPost('country_id', false);
            if (!$country
                || !isset($_FILES['upload_file']['name'])
                || !file_exists($_FILES['upload_file']['tmp_name'])
            ) {
                $this->_getSession()->addError($this->_getDebitHelper()->__('Please fill in all required fields.'));
                $this->_redirect('*/*/upload');
                return;
            }


            try {
                $path = Mage::getBaseDir('var') . DS;
                $filename = 'debitpayment_upload_file.csv';

                $uploader = new Varien_File_Uploader('upload_file');
                $uploader->setAllowedExtensions(array('csv'));
                $uploader->setAllowCreateFolders(true);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $uploader->save($path, $filename);

                $file = new Varien_Io_File();
                $file->open(array('path' => $path));
                $file->streamOpen($filename, 'r');

                $i = 1;
                $import = array();
                while (($line = $file->streamReadCsv()) !== false) {
                    if ($i == 1) {
                        $i++;
                        continue;
                    }

                    // Check if routing number already exists
                    $swiftCode = trim($line[2]);
                    if (array_key_exists($swiftCode, $import) || empty($swiftCode)) {
                        continue;
                    }

                    // Add bank to array
                    $import[$swiftCode] = array(
                        'routing_number' => trim($line[0]),
                        'swift_code' => $swiftCode,
                        'bank_name' => trim($line[1])
                    );
                }
                $file->streamClose();

                $importData = array($country => $import);

                /* @var $model Itabs_Debit_Model_Import_Bankdata */
                $model = Mage::getModel('debit/import_bankdata');
                $model->importData($importData);

                unlink($path.$filename);
                $this->_getSession()->addSuccess($this->_getDebitHelper()->__('Upload successful!'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/upload');
                return;
            }
        }

        $this->_redirect('*/*');
    }

    /**
     * Retrieve the helper class
     *
     * @return Itabs_Debit_Helper_Adminhtml
     */
    protected function _getDebitHelper()
    {
        return Mage::helper('debit/adminhtml');
    }
}

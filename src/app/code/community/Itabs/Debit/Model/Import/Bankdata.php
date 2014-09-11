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
 * Class Itabs_Debit_Model_Import_Bankdata
 */
class Itabs_Debit_Model_Import_Bankdata
{
    /**
     * Import the bank data
     */
    public function run()
    {
        $data = $this->loadData();
        $this->importData($data);
    }

    /**
     * Load the import data from the csv files
     *
     * @return array
     */
    public function loadData()
    {
        $ioHandler = new Varien_Io_File();
        $ioHandler->open(array('path' => $this->getImportDir()));
        $debitFiles = $ioHandler->ls(Varien_Io_File::GREP_FILES);

        $import = array();
        foreach ($debitFiles as $debitFile) {
            if ($debitFile['filetype'] != 'csv') {
                continue;
            }

            $country = str_replace('.csv', '', $debitFile['text']);
            $country = strtoupper($country);
            $import[$country] = array();

            $i = 1;
            $ioHandler->streamOpen($debitFile['text'], 'r');
            while (($line = $ioHandler->streamReadCsv()) !== false) {
                if ($i == 1) {
                    $i++;
                    continue;
                }

                // Check if routing number already exists
                $swiftCode = trim($line[2]);
                if (array_key_exists($swiftCode, $import[$country]) || empty($swiftCode)) {
                    continue;
                }

                // Add bank to array
                $import[$country][$swiftCode] = array(
                    'routing_number' => trim($line[0]),
                    'swift_code' => $swiftCode,
                    'bank_name' => trim($line[1])
                );
            }
            $ioHandler->streamClose();
        }

        return $import;
    }

    /**
     * Import the given bank data
     *
     * @param array $data Import Data
     */
    public function importData($data)
    {
        foreach ($data as $country => $importData) {
            /* @var $model Itabs_Debit_Model_Bankdata */
            $model = Mage::getModel('debit/bankdata');
            $model->deleteByCountryId($country);

            foreach ($importData as $data) {
                /* @var $model Itabs_Debit_Model_Bankdata */
                $model = Mage::getModel('debit/bankdata');
                $model->addData($data);
                $model->setData('country_id', $country);
                $model->save();
            }
        }
    }

    /**
     * Retrieve the import dir
     *
     * @return string
     */
    public function getImportDir()
    {
        return Mage::getConfig()->getModuleDir('etc', 'Itabs_Debit') . DS . 'bankdata' . DS;
    }
}

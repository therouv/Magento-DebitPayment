<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package    Mage_Debit
 * @copyright  Copyright (c) 2010 ITABS GbR - Rouven Alexander Rieker
 * @copyright  Copyright (c) 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Debit_Helper_Data extends Mage_Payment_Helper_Data
{
    public function getBankByBlz($blz)
    {
        $data = $this->_loadBlzCache();
        if (!$data) {
            // open blz file handle
            $io = new Varien_Io_File();
            $io->open(array('path'=>Mage::getModuleDir('etc', 'Mage_Debit')));
            $io->streamOpen('bankleitzahlen.csv', 'r');

            // read csv stream
            while (($line = $io->streamReadCsv(';')) !== false) {
                $data[$line[0]] = $line[1];
            }
            $this->_saveBlzCache(serialize($data));
        } else {
            $data = unserialize($data);
        }
        return empty($data[$blz]) ? null : $data[$blz];
    }

    protected function _loadBlzCache()
    {
        if (!Mage::app()->useCache('config')) {
            return false;
        }
        return Mage::app()->loadCache($this->_getCacheKey());
    }

    protected function _saveBlzCache($data)
    {
        if (!Mage::app()->useCache('config')) {
            return false;
        }
        Mage::app()->saveCache($data, $this->_getCacheKey(), $this->_getCacheTags(), $this->_getCacheLifetime());
        return $this;
    }

    protected function _getCacheLifetime()
    {
        return 3600*24;
    }

    protected function _getCacheKey()
    {
        return 'debit_blz_bank_mapping';
    }

    protected function _getCacheTags()
    {
        return array(Mage_Core_Model_Config::CACHE_TAG);
    }
}
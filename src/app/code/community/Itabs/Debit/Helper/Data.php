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
 * Helper class for different helper functionalities..
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Helper_Data extends Mage_Payment_Helper_Data
{
    const DEBIT_TYPE_BANK = 'bank';
    const DEBIT_TYPE_SEPA = 'sepa';

    /**
     * Retrieve the cucrent debit type
     * @return string
     */
    public function getDebitType()
    {
        $type = Mage::getStoreConfig('payment/debit/debit_type');

        /*
         * Check if we are on a specific page in the backend view,
         * then overwrite $type with the value of the order/..
         */

        if ($order = Mage::registry('current_order')) {
            /* @var $order Mage_Sales_Model_Order */
            $method = $order->getPayment()->getMethodInstance()->getInfoInstance();
            $type = $method->getData('debit_type');
        } elseif ($invoice = Mage::registry('current_invoice')) {
            /* @var $invoice Mage_Sales_Model_Order_Invoice */
            $method = $invoice->getOrder()->getPayment()->getMethodInstance()->getInfoInstance();
            $type = $method->getData('debit_type');
        } elseif ($shipment = Mage::registry('current_shipment')) {
            /* @var $shipment Mage_Sales_Model_Order_Shipment */
            $method = $shipment->getOrder()->getPayment()->getMethodInstance()->getInfoInstance();
            $type = $method->getData('debit_type');
        } elseif ($creditmemo = Mage::registry('current_creditmemo')) {
            /* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
            $method = $creditmemo->getOrder()->getPayment()->getMethodInstance()->getInfoInstance();
            $type = $method->getData('debit_type');
        }

        return $type;
    }

    /**
     * Returns the bankname by given blz
     *
     * @param  string      $blz BLZ
     * @return null|string Bank Name
     */
    public function getBankByBlz($blz)
    {
        $data = $this->_loadBlzCache();
        if (!$data) {
            // open blz file handle
            $file = new Varien_Io_File();
            $file->open(
                array(
                    'path' => Mage::getModuleDir('etc', 'Itabs_Debit')
                )
            );
            $file->streamOpen('bankleitzahlen.csv', 'r');
            // read csv stream
            while (($line = $file->streamReadCsv(';')) !== false) {
                $data[$line[0]] = $line[1];
            }
            $this->_saveBlzCache(serialize($data));
        } else {
            $data = unserialize($data);
        }

        return empty($data[$blz]) ? null : $data[$blz];
    }

    /**
     * Loads the blz data from cache
     *
     * @return mixed|false Cache data
     */
    protected function _loadBlzCache()
    {
        if (!Mage::app()->useCache('config')) {
            return false;
        }

        return Mage::app()->loadCache($this->_getCacheKey());
    }

    /**
     * Saves the blz data in the cache
     *
     * @param  array                  $data Blz data
     * @return Itabs_Debit_Helper_Data Self.
     */
    protected function _saveBlzCache($data)
    {
        if (!Mage::app()->useCache('config')) {
            return false;
        }
        Mage::app()->saveCache($data, $this->_getCacheKey(), $this->_getCacheTags(), $this->_getCacheLifetime());

        return $this;
    }

    /**
     * Returns the cache lifetime for the blz data.
     *
     * @return int Lifetime
     */
    protected function _getCacheLifetime()
    {
        return 3600*24;
    }

    /**
     * Returns the cache key for the blz data.
     *
     * @return string Cache key
     */
    protected function _getCacheKey()
    {
        return 'debit_blz_bank_mapping';
    }

    /**
     * Returns the CONFIG cache tag
     *
     * @return array Cache tags
     */
    protected function _getCacheTags()
    {
        return array(Mage_Core_Model_Config::CACHE_TAG);
    }

    /**
     * sanitizeData
     *
     * @param  string $data Data
     * @return string The sanitized string
     */
    public function sanitizeData($data)
    {
        $bad = array(' ', '-', '_', '.', ';', '/', '|');
        $sanitized = str_replace($bad, '', $data);

        return $sanitized;
    }
}

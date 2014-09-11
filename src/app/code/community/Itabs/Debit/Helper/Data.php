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
 * Helper class for different helper functionalities..
 */
class Itabs_Debit_Helper_Data extends Mage_Payment_Helper_Data
{
    const DEBIT_TYPE_BANK = 'bank';
    const DEBIT_TYPE_SEPA = 'sepa';

    /**
     * Retrieve the cucrent debit type
     *
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
     * Loads the blz data from cache
     *
     * @param  string      $identifier (Routing or Swift)
     * @param  string      $value
     * @param  null|string $country
     * @return null|string
     */
    public function getBankByIdentifier($identifier, $value, $country=null)
    {
        /* @var $model Itabs_Debit_Model_Bankdata */
        $model = Mage::getModel('debit/bankdata');
        $bankName = $model->loadByIdentifier($identifier, $value, $country);
        if (!$bankName) {
            return null;
        }

        return $bankName;
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

    /**
     * normalizeString
     *
     * @param  string $string String to normalize
     * @return string The normalized string
     */
    public function normalizeString($string)
    {
        // Replace german accents
        $search = array('Ä', 'ä', 'Ü', 'ü', 'Ö', 'ö', 'ß', '&');
        $replace = array('Ae', 'ae', 'Ue', 'ue', 'Oe', 'oe', 'ss', '+');
        $normalized =  str_replace($search, $replace, $string);

        // Replace all other chars
        $normalized = Mage::helper('catalog/product_url')->format($normalized);

        // Strip out every char which is not allowed in sepa xml charset
        $normalized = preg_replace('/[^a-zA-Z0-9\/\?\:\(\)\.\,\'\+\- ]/', '', $normalized);

        return $normalized;
    }

    /**
     * Retrieve the creditor identification number
     *
     * @param  null|int $storeId Store ID
     * @return string
     */
    public function getCreditorIdentificationNumber($storeId = null)
    {
        return Mage::getStoreConfig('debitpayment/sepa/creditor_identification_number', $storeId);
    }

    /**
     * Retrieve the hint for the IBAN field
     *
     * @param  null|int $storeId Store ID
     * @return string|bool
     */
    public function getHintForIbanField($storeId = null)
    {
        $field = Mage::getStoreConfig('debitpayment/sepa/hint_iban_field', $storeId);
        if (null === $field || $field == '') {
            return false;
        }

        return $field;
    }

    /**
     * Retrieve the hint for the BIC field
     *
     * @param  null|int $storeId Store ID
     * @return string|bool
     */
    public function getHintForBicField($storeId = null)
    {
        $field = Mage::getStoreConfig('debitpayment/sepa/hint_bic_field', $storeId);
        if (null === $field || $field == '') {
            return false;
        }

        return $field;
    }

    /**
     * Get the offset in days
     *
     * @param  null|int $storeId Store ID
     * @return int
     */
    public function getOffset($storeId = null)
    {
        $offset = (int) Mage::getStoreConfig('debitpayment/sepa/offset_days', $storeId);

        return max($offset, 2);
    }
}

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
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
/**
 * Helper class for different helper functionalities..
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2014 ITABS GmbH (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Helper_Data extends Mage_Payment_Helper_Data
{
    const DEBIT_TYPE_SEPA = 'sepa';

    /**
     * Retrieve the cucrent debit type
     * @return string
     */
    public function getDebitType()
    {
        $type = self::DEBIT_TYPE_SEPA;

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
     * Generate a unique mandate reference
     *
     * @param  int $customerId
     * @param  int $quoteId
     * @return string
     */
    public function getMandateReference($customerId, $quoteId)
    {
        $customer = str_pad($customerId, 12, '0', STR_PAD_RIGHT);
        $quote    = str_pad($quoteId, 12, '0', STR_PAD_RIGHT);

        return 'DP'.$customer.$quote;
    }
}

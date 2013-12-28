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
 * SEPA XML Export Model
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Model_Export_Xml
    extends Itabs_Debit_Model_Export_Abstract
    implements Itabs_Debit_Model_Export_Interface
{
    /**
     * @var array
     */
    protected $_orderFilter = array(
        'debit_type' => 'sepa'
    );

    /**
     * @var string File Extension
     */
    protected $_fileExt = '.xml';

    /**
     * Export function:
     * - Returns false, if an error occured or if there are no orders to export
     * - Returns array, containing the filename and the file contents
     *
     * @return bool|array
     */
    public function export()
    {
        $collection = $this->_hasOrdersToExport();
        if (!$collection) {
            return false;
        }

        $creditorId = '';
        $creditorName = '';
        $creditorIban = '';
        $creditorSwift = '';

        $xml = new Itabs_Debit_Model_Xml_XmlCreator($creditorId, $creditorName, $creditorIban, $creditorSwift);
        $xml->setOffset($this->getOffset());

        foreach ($collection as $order) {
            /* @var $order Itabs_Debit_Model_Orders */

            /* @var $mandate Itabs_Debit_Model_Mandates */
            $mandate = Mage::getModel('debit/mandates')->loadByOrder($order->getData('entity_id'));
            if (!$mandate) {
                continue;
            }

            /* @var $orderModel Mage_Sales_Model_Order */
            $orderModel = Mage::getModel('sales/order')->load($order->getData('entity_id'));
            /* @var $paymentMethod Itabs_Debit_Model_Debit */
            $paymentMethod = $orderModel->getPayment()->getMethodInstance();

            $booking = new Itabs_Debit_Model_Xml_Booking();
            $booking->setAccountOwner($paymentMethod->getAccountName());
            $booking->setIban($paymentMethod->getAccountIban());
            $booking->setSwift($paymentMethod->getAccountSwift());
            $booking->setAmount($order->getData('grand_total'));
            $booking->setBookingText('Order '.$mandate->getData('increment_id'));
            $booking->setMandateId($mandate->getData('mandate_reference')); // @todo: join table

            $xml->addBooking($booking);

            $this->_getDebitHelper()->setStatusAsExported($order->getId());
        }

        $response = array(
            'file_name' => $this->getFileName(),
            'file_content' => $xml->generateXml()
        );
        return $response;
    }

    /**
     * Get the offset in days
     *
     * @return int
     */
    public function getOffset()
    {
        $offset = (int) Mage::getStoreConfig('debitpayment/sepa/offset_days');
        if (!$offset || $offset < 2) {
            $offset = 2;
        }

        return $offset;
    }
}

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

        $fileName = $this->getFileName();
        $filePath = Mage::getBaseDir('var') . DS . $fileName . '.zip';

        $zip = new ZipArchive;
        $zipRes = $zip->open($filePath, ZipArchive::CREATE);
        if (true !== $zipRes) {
            Mage::getSingleton('adminhtml/session')->addError('An error occured.');
            return false;
        }

        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            /* @var $store Mage_Core_Model_Store */

            $creditorId    = Mage::getStoreConfig('debitpayment/sepa/creditor_identification_number', $store->getId());
            $creditorName  = Mage::getStoreConfig('debitpayment/bankaccount/account_owner', $store->getId());
            $creditorIban  = Mage::getStoreConfig('debitpayment/bankaccount/account_iban', $store->getId());
            $creditorSwift = Mage::getStoreConfig('debitpayment/bankaccount/swift_bic', $store->getId());

            $xml = new Itabs_Debit_Model_Xml_XmlCreator($creditorName);

            $payment = new Itabs_Debit_Model_Xml_Payment($creditorId, $creditorName, $creditorIban, $creditorSwift);
            $payment->setOffset($this->_getDebitHelper()->getOffset());
            $payment->setOneTimePayment(true);

            foreach ($collection as $order) {
                /* @var $order Itabs_Debit_Model_Orders */

                // Check if the store id matches
                if ($order->getStoreId() != $store->getId()) {
                    continue;
                }

                /* @var $orderModel Mage_Sales_Model_Order */
                $orderModel = Mage::getModel('sales/order')->load($order->getData('entity_id'));
                /* @var $paymentMethod Itabs_Debit_Model_Debit */
                $paymentMethod = $orderModel->getPayment()->getMethodInstance();

                // Get the booking text
                $bookingText = $this->_getDebitHelper()->getBookingText(
                    $orderModel->getStoreId(),
                    $order->getData('increment_id')
                );

                $booking = new Itabs_Debit_Model_Xml_Booking();
                $booking->setAccountOwner($paymentMethod->getAccountName());
                $booking->setIban($paymentMethod->getAccountIban());
                $booking->setSwift($paymentMethod->getAccountSwift());
                $booking->setAmount($order->getData('grand_total'));
                $booking->setBookingText($bookingText);
                $booking->setMandateId($order->getData('increment_id'));

                $payment->addBooking($booking);

                //$this->_getDebitHelper()->setStatusAsExported($order->getId());
            }

            // Check if the payment contains bookings
            if (count($payment->getBookings()) == 0) {
                continue;
            }

            // Add the payment info
            $xml->addPayment($payment);

            // Generate the sepa xml
            $sepaXml = $xml->generateXml();

            // Validate the generated file
            $xmlValidation = new Itabs_Debit_Model_Xml_Validation();
            $xmlValidation->setXml($sepaXml);
            $result = $xmlValidation->validate();
            if (!$result) {
                $errors = $xmlValidation->getErrors();
                foreach ($errors as $error) {
                    Mage::getSingleton('adminhtml/session')->addError(nl2br($error));
                }

                return false;
            }

            $zip->addFromString($fileName.'-'.$store->getId().'.xml', $sepaXml);
        }

        $zip->close();
        $fileContent = file_get_contents($filePath);
        unlink($filePath);

        $response = array(
            'file_name' => $fileName . '.zip',
            'file_content' => $fileContent
        );

        return $response;
    }
}

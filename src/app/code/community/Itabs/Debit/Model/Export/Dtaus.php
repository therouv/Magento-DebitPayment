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
require_once (Mage::getBaseDir().'/lib/DTA/DTA.php');
/**
 * DTAUS Export Model
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Model_Export_Dtaus
    extends Itabs_Debit_Model_Export_Abstract
    implements Itabs_Debit_Model_Export_Interface
{
    /**
     * @var array
     */
    protected $_orderFilter = array(
        'debit_type' => 'bank'
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

        // Create new object and set store owner bank account data
        $file = new DTA(DTA_DEBIT);
        $file->setAccountFileSender($this->_getDebitHelper()->getBankAccount());

        // Add orders
        foreach ($collection as $order) {
            /* @var $orderModel Mage_Sales_Model_Order */
            $orderModel = Mage::getModel('sales/order')->load($order->getData('entity_id'));
            /* @var $payment Itabs_Debit_Model_Debit */
            $paymentMethod = $orderModel->getPayment()->getMethodInstance();

            $file->addExchange(
                array(
                    'name'           => $paymentMethod->getAccountName(),
                    'bank_code'      => $paymentMethod->getAccountBLZ(),
                    'account_number' => $paymentMethod->getAccountNumber(),
                ),
                round($order->getData('grand_total'), 2),
                array(
                    'Bestellung Nr. '.$order->getData('increment_id')
                )
            );

            $this->_getDebitHelper()->setStatusAsExported($order->getId());
        }

        $response = array(
            'file_name' => $this->getFileName(),
            'file_content' => $file->getFileContent()
        );
        return $response;
    }
}

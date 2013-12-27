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
 * Cron Model
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2014 ITABS GmbH (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.7
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Model_Cron
{
    /**
     * @return Itabs_Debit_Model_Cron
     */
    public function generate()
    {
        $mandates = $this->_getMandates();
        if (!$mandates->count()) {
            return $this;
        }

        foreach ($mandates as $mandate) {
            $order = Mage::getModel('sales/order')->load($mandate->getData('order_id'));

            // Check if order exists, if not delete the mandate
            if (!$order->getId()) {
                $mandate->delete();
                continue;
            }

            $filePath = $this->_generateMandatePdf($mandate, $order);
            $this->_sendCustomerEmail($mandate, $order, $filePath);
        }
    }

    /**
     * Retrieve all mandates
     *
     * @return Itabs_Debit_Model_Resource_Mandates_Collection
     */
    protected function _getMandates()
    {
        /* @var $collection Itabs_Debit_Model_Resource_Mandates_Collection */
        $collection = Mage::getResourceModel('debit/mandates_collection');
        $collection->addNotGeneratedFilter();

        return $collection;
    }

    /**
     * @param Itabs_Debit_Model_Mandates $mandate
     * @param Mage_Sales_Model_Order     $order
     * @param string                     $filePath
     */
    protected function _sendCustomerEmail($mandate, $order, $filePath)
    {
        // @todo: Implement email sending process
    }

    /**
     * @param  Itabs_Debit_Model_Mandates $mandate
     * @param  Mage_Sales_Model_Order     $order
     * @return string
     */
    protected function _generateMandatePdf($mandate, $order)
    {
        /* @var $helper Itabs_Debit_Helper_Data */
        $helper = Mage::helper('debit');

        $fileDir = Mage::getBaseDir('var') . DS . 'debit' . DS;
        $fileName = $mandate->getData('mandate_reference').'.pdf';
        $filePath = $fileDir.$fileName;

        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $pdf->pages[] = $page;

        $y = 780;
        $x = 50;
        $x2 = $x + 185;

        if ($order->getStoreId()) {
            Mage::app()->getLocale()->emulate($order->getStoreId());
            Mage::app()->setCurrentStore($order->getStoreId());
        }

        $this->_setFontBold($page, 16);
        $page->drawText($helper->__('SEPA direct debit mandate', 'UTF-8'), $x, $y);

        $y -= 30;
        $this->_setFontRegular($page, 12);

        $page->drawText($helper->__('Payee'), $x, $y, 'UTF-8');
        $page->drawText(Mage::getStoreConfig('debitpayment/bankaccount/account_owner', $order->getStoreId()), $x2, $y, 'UTF-8');
        $y -= 15;
        $page->drawText($helper->__('Creditor Identification Number'), $x, $y, 'UTF-8');
        $page->drawText(Mage::getStoreConfig('debitpayment/sepa/creditor_identification_number', $order->getStoreId()), $x2, $y, 'UTF-8');
        $y -= 15;
        $page->drawText($helper->__('Mandate Reference'), $x, $y, 'UTF-8');
        $page->drawText($mandate->getData('mandate_reference'), $x2, $y, 'UTF-8');
        $y -= 40;

        $mandateText = Mage::getStoreConfig('debitpayment/sepa/mandate_text', $order->getStoreId());
        $lines = explode("\n", $mandateText);
        foreach ($lines as $line) {
            $text = array();
            foreach (Mage::helper('core/string')->str_split($line, 90, true, true) as $_value) {
                $text[] = $_value;
            }
            foreach ($text as $part) {
                $page->drawText(strip_tags(ltrim($part)), $x, $y, 'UTF-8');
                $y -= 15;
            }
            $y -= 5;
        }

        $y -= 30;

        /* @var $payment Itabs_Debit_Model_Debit */
        $payment = $order->getPayment()->getMethodInstance();

        $paymentData = array(
            array(
                'label' => $helper->__('Name of the Payer'),
                'value' => $payment->getAccountName()
            ),
            array(
                'label' => Mage::helper('customer')->__('Company'),
                'value' => $payment->getAccountCompany()
            ),
            array(
                'label' => $helper->__('Street and Housenumber'),
                'value' => $payment->getAccountStreet()
            ),
            array(
                'label' => $helper->__('Postcode/City'),
                'value' => $payment->getAccountCity()
            ),
            array(
                'label' => Mage::helper('customer')->__('Country'),
                'value' => $payment->getAccountCountry()
            ),
            array(
                'label' => Mage::helper('customer')->__('Email Address'),
                'value' => $payment->getAccountEmail()
            ),
            array(
                'label' => $helper->__('SWIFT Code'),
                'value' => $payment->getAccountSwift()
            ),
            array(
                'label' => $helper->__('IBAN'),
                'value' => $payment->getAccountIban()
            )
        );

        foreach ($paymentData as $data) {
            if ($data['value'] == '') {
                continue;
            }

            $page->drawText($data['label'], $x, $y, 'UTF-8');
            $page->drawText($data['value'], $x2, $y, 'UTF-8');
            $y -= 15;
        }

        $y -= 25;

        $signature = $mandate->getData('mandate_city').', ';
        $signature .= Mage::helper('core')->formatDate($order->getCreatedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        $page->drawText($signature, $x, $y, 'UTF-8');
        $y -= 30;
        $page->drawText($payment->getAccountName(), $x, $y, 'UTF-8');


        if ($order->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }

        $pdf->save($filePath);

        return $filePath;
    }

    /**
     * Set font as regular
     *
     * @param  Zend_Pdf_Page $object
     * @param  int           $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int           $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $object->setFont($font, $size);
        return $font;
    }
}

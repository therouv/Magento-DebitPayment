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
 * PDF Mandate Model
 */
class Itabs_Debit_Model_Pdf_Mandate extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Return PDF document
     *
     * @param  array $data PDF Data
     * @return Zend_Pdf
     */
    public function getPdf($data = array())
    {
        /* @var $helper Itabs_Debit_Helper_Data */
        $helper = Mage::helper('debit');

        $this->_beforeGetPdf();

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $page = $this->newPage();

        $x1 = 50;        // General margin left
        $x2 = $x1 + 175; // Margin left for information
        $x3 = $x1 + 10;  // Margin left for form values and labels

        $storeId = Mage::app()->getStore()->getStoreId();

        $font     = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $fontBold = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $page->setFont($font, 11);

        // Creditor address
        $creditorAddress = $this->getCreditorAddress($storeId);
        foreach ($creditorAddress as $addressItem) {
            if ($addressItem !== '') {
                $page->drawText($addressItem, $x1, $this->y, 'UTF-8');
                $this->ln();
            }
        }

        $this->ln(22);

        // Creditors identifier and mandete reference
        $page->drawText($helper->__('Creditor Identification Number'), $x1, $this->y, 'UTF-8');
        $page->drawText($this->getCreditorIdentificationNumber($storeId), $x2, $this->y, 'UTF-8');

        $this->ln();

        $page->drawText($helper->__('SEPA Mandate Reference'), $x1, $this->y, 'UTF-8');
        $page->drawLine($x1 + 175, $this->y - 3, 400, $this->y - 3);
        $this->ln(12);
        $page->setFont($font, 7);
        $page->drawText($helper->__('(Please do not fill)'), $x2, $this->y, 'UTF-8');
        $this->ln(40);

        // Headline
        $page->setFont($fontBold, 11);
        $page->drawText($this->getMandatePdfHeadline($storeId), $x1, $this->y, 'UTF-8');
        $this->ln(22);

        // Message
        $page->setFont($font, 11);
        $mandateText = $this->getMandatePdfText($storeId);
        foreach ($mandateText as $mandateTextPart) {
            $message = Mage::helper('core/string')->str_split($mandateTextPart, 90, true, true);
            foreach ($message as $messageItem) {
                $page->drawText($messageItem, $x1, $this->y, 'UTF-8');
                $this->ln();
            }
        }

        $this->ln(50);

        // Firstname and lastname
        if ($data['account_holder'] != '') {
            $page->drawText($data['account_holder'], $x3, $this->y + 5, 'UTF-8');
        }
        $page->drawLine($x1, $this->y, 400, $this->y);
        $page->setFont($font, 9);
        $page->drawText($helper->__('Account owner'), $x3, $this->y - 11, 'UTF-8');
        $page->setFont($font, 11);
        $this->ln(50);

        // Street and number
        if ($data['street'] != '') {
            $page->drawText($data['street'], $x3, $this->y + 5, 'UTF-8');
        }
        $page->drawLine($x1, $this->y, 400, $this->y);
        $page->setFont($font, 9);
        $page->drawText($helper->__('Street and Housenumber'), $x3, $this->y - 11, 'UTF-8');
        $page->setFont($font, 11);
        $this->ln(50);

        // Postcode and city
        if ($data['city'] != '') {
            $page->drawText($data['city'], $x3, $this->y + 5, 'UTF-8');
        }
        $page->drawLine($x1, $this->y, 400, $this->y);
        $page->setFont($font, 9);
        $page->drawText($helper->__('Postcode and City'), $x3, $this->y - 11, 'UTF-8');
        $page->setFont($font, 11);
        $this->ln(50);

        // IBAN
        if ($data['iban'] != '') {
            $page->drawText($data['iban'], $x3, $this->y + 5, 'UTF-8');
            $page->drawLine($x1, $this->y, 400, $this->y);
        } else {
            $page->setFont($font, 14);
            $page->drawText('_ _ _ _ / _ _ _ _ / _ _ _ _ / _ _ _ _ / _ _ _ _ / _ _', $x1, $this->y + 5, 'UTF-8');
            $page->setFont($font, 11);
        }

        $page->setFont($font, 9);
        $page->drawText($helper->__('IBAN'), $x3, $this->y - 11, 'UTF-8');
        $page->setFont($font, 11);
        $this->ln(50);

        // SWIFT-Code
        if ($data['swiftcode'] != '') {
            $page->drawText($data['swiftcode'], $x3, $this->y + 5, 'UTF-8');
            $page->drawLine($x1, $this->y, 400, $this->y);
        } else {
            $page->setFont($font, 14);
            $page->drawText('_ _ _ _ _ _ _ _ / _ _ _', $x1, $this->y + 5, 'UTF-8');
            $page->setFont($font, 11);
        }

        $page->setFont($font, 9);
        $page->drawText($helper->__('SWIFT Code'), $x3, $this->y - 11, 'UTF-8');
        $page->setFont($font, 11);
        $this->ln(50);

        // Bank name
        if ($data['bank_name'] != '') {
            $page->drawText($data['bank_name'], $x3, $this->y + 5, 'UTF-8');
        }
        $page->drawLine($x1, $this->y, 400, $this->y);
        $page->setFont($font, 9);
        $page->drawText($helper->__('Bank name'), $x3, $this->y - 11, 'UTF-8');
        $page->setFont($font, 11);
        $this->ln(50);

        // Signature
        $this->ln(50);
        $page->drawLine($x1, $this->y, 400, $this->y);
        $page->drawText($helper->__('Date, City and Signature'), $x3, $this->y - 11, 'UTF-8');

        $this->_afterGetPdf();

        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings Page Settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;

        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $page->setFont($font, 11);

        $this->y = 800;

        return $page;
    }

    /**
     * Reduce page height
     *
     * @param int $ln Line Spacing
     */
    public function ln($ln = 16)
    {
        $this->y -= $ln;
    }

    /**
     * Retrieve the creditor identification number
     *
     * @param  null|int $storeId Store ID
     * @return string
     */
    public function getCreditorIdentificationNumber($storeId=null)
    {
        return Mage::helper('debit')->getCreditorIdentificationNumber($storeId);
    }

    /**
     * Retrieve the creditor address
     *
     * @param  null|int $storeId Store ID
     * @return array
     */
    public function getCreditorAddress($storeId=null)
    {
        $creditorAddress = Mage::getStoreConfig('debitpayment/sepa/creditor_address', $storeId);
        return explode("\n", $creditorAddress);
    }

    /**
     * Retrieve the mandate pdf headline
     *
     * @param  null|int $storeId Store ID
     * @return string
     */
    public function getMandatePdfHeadline($storeId = null)
    {
        return Mage::getStoreConfig('debitpayment/sepa/mandate_pdf_headline', $storeId);
    }

    /**
     * Retrieve the mandate pdf text
     *
     * @param  null|int $storeId Store ID
     * @return array
     */
    public function getMandatePdfText($storeId = null)
    {
        $text = Mage::getStoreConfig('debitpayment/sepa/mandate_pdf_text', $storeId);
        return explode("\n", $text);
    }
}

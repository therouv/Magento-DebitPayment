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
 * Class Itabs_Debit_Model_Xml_Booking
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Model_Xml_Booking
{
    /**
     * @var string
     */
    protected $endToEnd;

    /**
     * @var string
     */
    protected $iban;

    /**
     * @var string
     */
    protected $swift;

    /**
     * @var string
     */
    protected $accountOwner;

    /**
     * @var string
     */
    protected $bookingText;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $mandateId;

    /**
     * @var string
     */
    protected $mandateDate;

    /**
     * @var bool
     */
    protected $mandateChange;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->setEndToEnd('NOTPROVIDED');
        $this->setMandateDate(date('Y-m-d', time()));
        $this->setMandateChange(false);
    }

    /**
     * Set the account owner
     *
     * @param  string $accountOwner
     * @return Itabs_Debit_Model_Xml_Payment Self.
     */
    public function setAccountOwner($accountOwner)
    {
        $this->accountOwner = $this->_helper()->removeAccents($accountOwner);
        return $this;
    }

    /**
     * Get the account owner
     *
     * @return string
     */
    public function getAccountOwner()
    {
        return $this->accountOwner;
    }

    /**
     * Set the amount
     *
     * @param  float $amount
     * @return Itabs_Debit_Model_Xml_Payment Self.
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get the amount
     *
     * @return float
     */
    public function getAmount()
    {
        return number_format($this->amount, 2, '.', '');
    }

    /**
     * Set the booking text
     *
     * @param  string $bookingText
     * @return Itabs_Debit_Model_Xml_Payment Self.
     */
    public function setBookingText($bookingText)
    {
        $this->bookingText = $this->_helper()->removeAccents($bookingText);
        return $this;
    }

    /**
     * Get the booking text
     *
     * @return string
     */
    public function getBookingText()
    {
        return $this->bookingText;
    }

    /**
     * Set the end2end value
     *
     * @param  string $endToEnd
     * @return Itabs_Debit_Model_Xml_Payment Self.
     */
    public function setEndToEnd($endToEnd)
    {
        $this->endToEnd = $this->_helper()->removeAccents($endToEnd);
        return $this;
    }

    /**
     * Get the end2end value
     *
     * @return string
     */
    public function getEndToEnd()
    {
        return $this->endToEnd;
    }

    /**
     * Set the payment iban
     *
     * @param  string $iban
     * @return Itabs_Debit_Model_Xml_Payment Self.
     */
    public function setIban($iban)
    {
        $iban = strtoupper($iban);
        $iban = str_replace(' ', '', $iban);

        $this->iban = $iban;
        return $this;
    }

    /**
     * Get the payment iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set the mandate change
     *
     * @param  bool $mandateChange
     * @return Itabs_Debit_Model_Xml_Payment Self.
     */
    public function setMandateChange($mandateChange)
    {
        $this->mandateChange = $mandateChange;
        return $this;
    }

    /**
     * Get the mandate change
     *
     * @return bool
     */
    public function getMandateChange()
    {
        return $this->mandateChange;
    }

    /**
     * Set the mandate date
     *
     * @param  string $mandateDate
     * @return Itabs_Debit_Model_Xml_Payment Self.
     */
    public function setMandateDate($mandateDate)
    {
        $this->mandateDate = $mandateDate;
        return $this;
    }

    /**
     * Get the mandate date
     *
     * @return string
     */
    public function getMandateDate()
    {
        return $this->mandateDate;
    }

    /**
     * Set the mandate id
     *
     * @param  string $mandateId
     * @return Itabs_Debit_Model_Xml_Payment Self.
     */
    public function setMandateId($mandateId)
    {
        $this->mandateId = $mandateId;
        return $this;
    }

    /**
     * Get the mandate id
     *
     * @return string
     */
    public function getMandateId()
    {
        return $this->mandateId;
    }

    /**
     * Get the payment SWIFT code
     *
     * @param  string $swift
     * @return Itabs_Debit_Model_Xml_Payment Self.
     */
    public function setSwift($swift)
    {
        $this->swift = $swift;
        return $this;
    }

    /**
     * Set the payment SWIFT code
     *
     * @return string
     */
    public function getSwift()
    {
        return $this->swift;
    }

    /**
     * Retrieve the helper class
     *
     * @return Mage_Core_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('core');
    }
}

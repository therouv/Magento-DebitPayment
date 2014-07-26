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
 * Class Itabs_Debit_Model_Xml_Payment
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Model_Xml_Payment
{
    /**
     * @var array
     */
    protected $bookings = array();

    /**
     * @var string
     */
    protected $creditorId;

    /**
     * @var string
     */
    protected $creditorName;

    /**
     * @var string
     */
    protected $creditorIban;

    /**
     * @var string
     */
    protected $creditorSwift;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var string|null
     */
    protected $transactionDate = null;

    /**
     * @var string
     */
    protected $currency = 'EUR';

    /**
     * @var bool
     */
    protected $recurringSequence = false;

    /**
     * @var bool
     */
    protected $oneTimePayment = true;

    /**
     * @var bool
     */
    protected $singleSequence = false;

    /**
     * @param string $creditorId
     * @param string $creditorName
     * @param string $creditorIban
     * @param string $creditorSwift
     */
    public function __construct($creditorId, $creditorName, $creditorIban, $creditorSwift)
    {
        $this->setCreditorId($creditorId);
        $this->setCreditorName($creditorName);
        $this->setCreditorIban($creditorIban);
        $this->setCreditorSwift($creditorSwift);
    }

    /**
     * Retrieve the control sum
     *
     * @return float
     */
    public function getControlSum()
    {
        $controlSum = 0;

        $bookings = $this->getBookings();
        foreach ($bookings as $booking) {
            /* @var $booking Itabs_Debit_Model_Xml_Booking */
            $controlSum += $booking->getAmount();
        }

        return number_format($controlSum, 2, '.', '');
    }

    /**
     * Add a booking
     *
     * @param  Itabs_Debit_Model_Xml_Booking $booking
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function addBooking(Itabs_Debit_Model_Xml_Booking $booking)
    {
        $this->bookings[] = $booking;
        return $this;
    }

    /**
     * Get the bookings
     *
     * @return array
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * Set the creditor iban
     *
     * @param  string $creditorIban
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function setCreditorIban($creditorIban)
    {
        $creditorIban = strtoupper($creditorIban);
        $creditorIban = str_replace(' ', '', $creditorIban);

        $this->creditorIban = $creditorIban;
        return $this;
    }

    /**
     * Get the creditor iban
     *
     * @return string
     */
    public function getCreditorIban()
    {
        return $this->creditorIban;
    }

    /**
     * Set the creditor id
     *
     * @param  string $creditorId
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function setCreditorId($creditorId)
    {
        $this->creditorId = $creditorId;
        return $this;
    }

    /**
     * Get the creditor id
     *
     * @return string
     */
    public function getCreditorId()
    {
        return $this->creditorId;
    }

    /**
     * Set the creditor name
     *
     * @param  string $creditorName
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function setCreditorName($creditorName)
    {
        $this->creditorName = $creditorName;
        return $this;
    }

    /**
     * Get the creditor name
     *
     * @return string
     */
    public function getCreditorName()
    {
        return $this->creditorName;
    }

    /**
     * Set the creditor swift
     *
     * @param  string $creditorSwift
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function setCreditorSwift($creditorSwift)
    {
        $this->creditorSwift = $creditorSwift;
        return $this;
    }

    /**
     * Get the creditor swift
     *
     * @return string
     */
    public function getCreditorSwift()
    {
        return $this->creditorSwift;
    }

    /**
     * Set the currency code
     *
     * @param  string $currency
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get the currency code
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the time offset
     *
     * @param int $offset
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get the time offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set the recurring sequence flag
     *
     * @param  bool $recurringSequence
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function setRecurringSequence($recurringSequence)
    {
        $this->recurringSequence = $recurringSequence;
        return $this;
    }

    /**
     * Get the recurring sequence flag
     *
     * @return bool
     */
    public function getRecurringSequence()
    {
        return $this->recurringSequence;
    }

    /**
     * Set the one time payment flag
     *
     * @param  bool $oneTimePayment
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function setOneTimePayment($oneTimePayment)
    {
        $this->oneTimePayment = $oneTimePayment;
        return $this;
    }

    /**
     * Get the one time payment flag
     *
     * @return bool
     */
    public function getOneTimePayment()
    {
        return $this->oneTimePayment;
    }

    /**
     * Set the transaction date
     *
     * @param  string $transactionDate
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
        return $this;
    }

    /**
     * Get the transaction date
     *
     * @return string
     */
    public function getTransactionDate()
    {
        if (null !== $this->transactionDate) {
            $transactionDate = $this->transactionDate;
        } else {
            $transactionDate = time();
            if ($this->offset > 0) {
                $transactionDate = $transactionDate + (24 * 3600 * $this->getOffset());
            }
            $transactionDate = date('Y-m-d', $transactionDate);
        }

        return $transactionDate;
    }
}

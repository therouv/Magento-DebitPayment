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
 * Class Itabs_Debit_Model_Xml_XmlCreator
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Model_Xml_XmlCreator
{
    /**
     * @var array
     */
    protected $bookings;

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
     * @var string
     */
    protected $transactionDate;

    /**
     * @var string
     */
    protected $currency = 'EUR';

    /**
     * @var bool
     */
    protected $recurringSequence = false;

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
     * Generate the Direct Debit SEPA XML file
     *
     * @return string
     */
    public function generateXml()
    {
        $dom = new DOMDocument('1.0', 'utf-8');

        // Build Document-Root
        $document = $dom->createElement('Document');
        $document->setAttribute('xmlns', 'urn:iso:std:iso:20022:tech:xsd:pain.008.002.02');
        $document->setAttribute('xsi:schemaLocation', 'urn:iso:std:iso:20022:tech:xsd:pain.008.002.02 pain.008.002.02.xsd');
        $document->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $dom->appendChild($document);

        // Add root xml element
        $content = $dom->createElement('CstmrDrctDbtInitn');
        $document->appendChild($content);

        // Add group header element
        $header = $dom->createElement('GrpHdr');
        $content->appendChild($header);

        $creationTime = time();

        // Add the message id
        $header->appendChild($dom->createElement('MsgId', $this->accountBic . '00' . date('YmdHis', $creationTime)));
        $header->appendChild($dom->createElement('CreDtTm', date('Y-m-d', $creationTime) . 'T' . date('H:i:s', $creationTime) . '.000Z'));
        $header->appendChild($dom->createElement('NbOfTxs', count($this->getBookings())));
        $header->appendChild($initatorName = $dom->createElement('InitgPty'));
        $initatorName->appendChild($dom->createElement('Nm', $this->getCreditorName()));

        // Add the payment info
        $paymentInfo = $dom->createElement('PmtInf');
        $content->appendChild($paymentInfo);

        $paymentInfo->appendChild($dom->createElement('PmtInfId', 'PMT-ID0-' . date('YmdHis', $creationTime)));
        $paymentInfo->appendChild($dom->createElement('PmtMtd', 'DD'));
        $paymentInfo->appendChild($dom->createElement('BtchBookg', 'true'));
        $paymentInfo->appendChild($dom->createElement('NbOfTxs', count($this->getBookings())));
        $paymentInfo->appendChild($dom->createElement('CtrlSum', $this->getControlSum()));

        // Add the payment transaction info
        $pmtTpInf = $dom->createElement('PmtTpInf');
        $paymentInfo->appendChild($pmtTpInf);

        // Add the service level
        $svcLvl = $dom->createElement('SvcLvl');
        $svcLvl->appendChild($dom->createElement('Cd', 'SEPA'));
        $pmtTpInf->appendChild($svcLvl);

        // Add the local instrument
        $lclInstrm = $dom->createElement('LclInstrm');
        $lclInstrm->appendChild($dom->createElement('Cd', 'CORE'));
        $pmtTpInf->appendChild($lclInstrm);

        // Add the recurring sequence
        if ($this->getRecurringSequence()) {
            $pmtTpInf->appendChild($dom->createElement('SeqTp', 'RCUR'));
        } else {
            $pmtTpInf->appendChild($dom->createElement('SeqTp', 'FRST'));
        }

        // Add the transaction date
        $paymentInfo->appendChild($dom->createElement('ReqdColltnDt', $this->getTransactionDate()));

        // Add the creditor name
        $cdtr = $dom->createElement('Cdtr');
        $cdtr->appendChild($dom->createElement('Nm', $this->getCreditorName()));

        // Add the creditor iban
        $cdtrAcct = $dom->createElement('CdtrAcct');
        $cdtrAcctId = $dom->createElement('Id');
        $cdtrAcctId->appendChild($dom->createElement('IBAN', $this->getCreditorIban()));
        $cdtrAcct->appendChild($cdtrAcctId);

        // Add the creditor swift code
        $cdtrAgt = $dom->createElement('CdtrAgt');
        $finInstnId = $dom->createElement('FinInstnId');
        $finInstnId->appendChild($dom->createElement('BIC', $this->getCreditorSwift()));
        $cdtrAgt->appendChild($cdtrAgt);

        // Add the ...
        $paymentInfo->appendChild($dom->createElement('ChrgBr', 'SLEV'));

        // Add the creditor scheme id
        $cdtrSchmeId = $dom->createElement('CdtrSchmeId');

        $cdtrSchmeIdId = $dom->createElement('Id');
        $cdtrSchmeIdIdPrvtId = $dom->createElement('PrvtId');
        $cdtrSchmeIdIdPrvtIdOthr = $dom->createElement('Othr');

        $cdtrSchmeIdIdPrvtIdOthr->appendChild($dom->createElement('Id', $this->getCreditorId()));

        $cdtrSchmeIdIdPrvtIdOthrSchmeNm = $dom->createElement('SchmeNm');
        $cdtrSchmeIdIdPrvtIdOthrSchmeNm->appendChild($dom->createElement('Prtry', 'SEPA'));
        $cdtrSchmeIdIdPrvtIdOthr->appendChild($cdtrSchmeIdIdPrvtIdOthrSchmeNm);

        $cdtrSchmeIdIdPrvtId->appendChild($cdtrSchmeIdIdPrvtIdOthr);
        $cdtrSchmeIdId->appendChild($cdtrSchmeIdIdPrvtId);
        $cdtrSchmeId->appendChild($cdtrSchmeIdId);

        $paymentInfo->appendChild($cdtrSchmeId);


        $bookings = $this->getBookings();
        foreach ($bookings as $booking) {
            /* @var $booking Itabs_Debit_Model_Xml_Payment */

            $transaction = $dom->createElement('DrctDbtTxInf');

            // Add end2end
            if ($endToEnd = $booking->getEndToEnd()) {
                $txPmtId = $dom->createElement('PmtId');
                $txPmtId->appendChild($dom->createElement('EndToEndId', $endToEnd));
                $transaction->appendChild($txPmtId);
            }

            // Add the amount
            $instdAmt = $dom->createElement('InstdAmt', $booking->getAmount());
            $instdAmt->setAttribute('Ccy', $this->getCurrency());
            $transaction->appendChild($instdAmt);

            // Add the mandate information
            $drctDbtTx = $dom->createElement('DrctDbtTx');
            $drctDbtTxMndtRltdInf = $dom->createElement('MndtRltdInf');
            $drctDbtTxMndtRltdInf->appendChild($dom->createElement('MndtId', $booking->getMandateId()));
            $drctDbtTxMndtRltdInf->appendChild($dom->createElement('DtOfSgntr', $booking->getMandateDate()));
            if ($booking->getMandateChange()) {
                $drctDbtTxMndtRltdInf->appendChild($dom->createElement('AmdmntInd', 'true'));
            } else {
                $drctDbtTxMndtRltdInf->appendChild($dom->createElement('AmdmntInd', 'false'));
            }
            $drctDbtTx->appendChild($drctDbtTxMndtRltdInf);
            $transaction->appendChild($drctDbtTx);

            // Add the debitor swift code
            $dbtrAgt = $dom->createElement('DbtrAgt');
            $dbtrAgtFinInstnId = $dom->createElement('FinInstnId');
            $dbtrAgtFinInstnId->appendChild($dom->createElement('BIC', $booking->getSwift()));
            $dbtrAgt->appendChild($dbtrAgtFinInstnId);
            $transaction->appendChild($dbtrAgt);

            // Add the debitor name
            $dbtr = $dom->createElement('Dbtr');
            $dbtr->appendChild($dom->createElement('Nm', $booking->getAccountOwner()));
            $transaction->appendChild($dbtr);

            // Add the debitor iban
            $dbtrAcct = $dom->createElement('DbtrAcct');
            $dbtrAcctId = $dom->createElement('Id');
            $dbtrAcctId->appendChild($dom->createElement('IBAN', $booking->getIban()));
            $dbtrAcct->appendChild($dbtrAcctId);
            $transaction->appendChild($dbtrAcct);

            // Add the account owner
            $ultmtDbtr = $dom->createElement('UltmtDbtr');
            $ultmtDbtr->appendChild($dom->createElement('Nm', $booking->getAccountOwner()));
            $transaction->appendChild($ultmtDbtr);

            // Add the booking text
            if ($bookingText = $booking->getBookingText()) {
                $rmtInf = $dom->createElement('RmtInf');
                $rmtInf->appendChild($dom->createElement('Ustrd', $bookingText));
                $transaction->appendChild($rmtInf);
            }

            // Add the transaction xml to the payment info
            $paymentInfo->appendChild($transaction);
        }

        return $dom->saveXML();
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
            /* @var $booking Itabs_Debit_Model_Xml_Payment */
            $controlSum += $booking->getAmount();
        }

        return number_format($controlSum, 2, '.', '');
    }

    /**
     * Add a booking
     *
     * @param  Itabs_Debit_Model_Xml_Payment $booking
     * @return Itabs_Debit_Model_Xml_XmlCreator Self.
     */
    public function addBooking(Itabs_Debit_Model_Xml_Payment $booking)
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
        if (isset($this->transactionDate)) {
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

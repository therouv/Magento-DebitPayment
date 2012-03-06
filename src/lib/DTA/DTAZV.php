<?php
/**
 * DTAZV
 *
 * DTAZV is a class that provides functions to create DTAZV files used
 * in Germany to exchange informations about european money transactions
 * with banks or online banking programs.
 *
 * Disclaimer: this only implements a subset of DTAZV as used for a
 * "EU-Standardüberweisung" and is only tested against locally used
 * accounting software.
 * If you use this class commercially and/or for large transfer amounts
 * then you might have to implement additional record types (V or W)
 * and fill additional data fields for notification requirements.
 *
 * Implemented using the specification from 2007.
 * Current specification from 2009 (german/english):
 * http://www.bundesbank.de/download/meldewesen/aussenwirtschaft/vordrucke/pdf/dtazv_kunde_bank_neu.pdf
 * http://www.bundesbank.de/download/meldewesen/aussenwirtschaft/vordrucke/pdf/dtazv_financial_inst_bbk.pdf
 *
 * PHP version 5
 *
 * This LICENSE is in the BSD license style.
 *
 * Copyright (c) 2008-2010 Martin Schütte
 * derived from class DTA
 * Copyright (c) 2003-2005 Hermann Stainer, Web-Gear
 * http://www.web-gear.com/
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *
 * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in the
 * documentation and/or other materials provided with the distribution.
 *
 * Neither the name of Hermann Stainer, Web-Gear nor the names of his
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE
 * REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
 * OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Payment
 * @package   Payment_DTA
 * @author    Martin Schütte <info@mschuette.name>
 * @author    Hermann Stainer <hs@web-gear.com>
 * @copyright 2008-2010 Martin Schütte
 * @copyright 2003-2005 Hermann Stainer, Web-Gear
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id: DTAZV.php 310332 2011-04-18 21:31:18Z mschuett $
 * @link      http://pear.php.net/package/Payment_DTA
 */

/**
 * needs base class
 */
require_once 'DTA/DTABase.php';

/**
 * DTAZV class provides functions to create and handle with DTAZV
 * files used in Germany to exchange informations about european
 * money transactions with banks or online banking programs.
 *
 * @category  Payment
 * @package   Payment_DTA
 * @author    Martin Schütte <info@mschuette.name>
 * @author    Hermann Stainer <hs@web-gear.com>
 * @copyright 2008 Martin Schütte
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: 1.4.2
 * @link      http://pear.php.net/package/Payment_DTA
 */
class DTAZV extends DTABase
{
    /**
    * The maximum allowed amount per transfer (in cents).
    *
    * By default set to the maximum amount for a "EU-Standardüberweisung"
    * that does not have to be reported.
    *
    * @see setMaxAmount()
    * @var integer $max_amount
    * @access protected
    */
    protected $max_amount;

    /**
    * Constructor.
    *
    * @param string $input Optional, a string with DTAZV data to import.
    *
    * @access public
    */
    function __construct($input = null)
    {
        parent::__construct();
        $this->max_amount = 12500*100;

        if (is_string($input)) {
            try {
                $this->parse($input);
            } catch (Payment_DTA_FatalParseException $e) {
                // cannot construct this object, reset everything
                parent::__construct();
                $this->max_amount = 12500*100;
                $this->allerrors[] = $e;
            } catch (Payment_DTA_Exception $e) {
                // object is valid, but save the error
                $this->allerrors[] = $e;
            }
        }
    }

    /**
    * Set the sender of the DTAZV file. Must be set for valid DTAZV file.
    * The given account data is also used as default sender's account.
    * Account data contains
    *  name            Sender's name. Maximally 35 chars are allowed.
    *  additional_name Sender's additional name (max. 35 chars)
    *  street          Sender's street/PO Box (max. 35 chars)
    *  city            Sender's city (max. 35 chars)
    *  bank_code       Sender's bank code (BLZ, 8-digit)
    *  account_number  Sender's account number (10-digit)
    *
    * @param array $account Account data fot file sender.
    *
    * @access public
    * @return boolean
    */
    function setAccountFileSender($account)
    {
        $account['account_number']
            = strval($account['account_number']);
        $account['bank_code']
            = strval($account['bank_code']);

        if (strlen($account['name']) > 0
            && strlen($account['bank_code']) > 0
            && strlen($account['bank_code']) <= 8
            && ctype_digit($account['bank_code'])
            && strlen($account['account_number']) > 0
            && strlen($account['account_number']) <= 10
            && ctype_digit($account['account_number'])
        ) {
            if (empty($account['additional_name'])) {
                $account['additional_name'] = "";
            }
            if (empty($account['street'])) {
                $account['street'] = "";
            }
            if (empty($account['city'])) {
                $account['city'] = "";
            }

            $this->account_file_sender = array(
                "name"            => $this->filter($account['name'], 35),
                "additional_name" => $this->filter($account['additional_name'], 35),
                "street"          => $this->filter($account['street'], 35),
                "city"            => $this->filter($account['city'], 35),
                "bank_code"       => $account['bank_code'],
                "account_number"  => $account['account_number']
            );

            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
    * Auxillary method to fill and normalize the account sender array.
    *
    * @param array $account_sender Sender's account data.
    *
    * @access private
    * @return array
    */
    private function _exchangeFillSender($account_sender)
    {
        if (empty($account_sender['name'])) {
            $account_sender['name']
                = $this->account_file_sender['name'];
        }
        if (empty($account_sender['additional_name'])) {
            $account_sender['additional_name']
                = $this->account_file_sender['additional_name'];
        }
        if (empty($account_sender['street'])) {
            $account_sender['street']
                = $this->account_file_sender['street'];
        }
        if (empty($account_sender['city'])) {
            $account_sender['city']
                = $this->account_file_sender['city'];
        }
        if (empty($account_sender['bank_code'])) {
            $account_sender['bank_code']
                = $this->account_file_sender['bank_code'];
        }
        if (empty($account_sender['account_number'])) {
            $account_sender['account_number']
                = $this->account_file_sender['account_number'];
        }
        $account_sender['account_number']
            = strval($account_sender['account_number']);
        $account_sender['bank_code']
            = strval($account_sender['bank_code']);

        return $account_sender;
    }

    /**
    * Auxillary method to fill and normalize the account receiver array.
    *
    * @param array $account_receiver Receiver's account data.
    *
    * @access private
    * @return array
    */
    private function _exchangeFillReceiver($account_receiver)
    {
        if (empty($account_receiver['additional_name'])) {
            $account_receiver['additional_name'] = "";
        }
        if (empty($account_receiver['street'])) {
            $account_receiver['street'] = "";
        }
        if (empty($account_receiver['city'])) {
            $account_receiver['city'] = "";
        }

        if (strlen($account_receiver['bank_code']) == 8) {
            if (is_numeric($account_receiver['bank_code'])) {
                // german BLZ -> allowed with special format
                $account_receiver['bank_code']
                    = '///' . $account_receiver['bank_code'];
            } else {
                // short BIC -> fill to 11 chars
                $account_receiver['bank_code']
                    = $account_receiver['bank_code'] . 'XXX';
            }
        }

        return $account_receiver;
    }

    /**
    * Adds an exchange.
    *
    * First the account data for the receiver of the exchange is set.
    * In the case the DTA file contains credits, this is the payment receiver.
    * In the other case (the DTA file contains debits), this is the account,
    * from which money is taken away.
    *
    * If the sender is not specified, values of the file sender are used by default.
    * Account data for sender contain
    *  name            Sender's name. Maximally 35 chars are allowed.
    *  additional_name Sender's additional name (max. 35 chars)
    *  street          Sender's street/PO Box (max. 35 chars)
    *  city            Sender's city (max. 35 chars)
    *  bank_code       Sender's bank code (8-digit BLZ)
    *  account_number  Sender's account number (10-digit)
    *
    * Account data for receiver contain
    *  name            Receiver's name. Maximally 35 chars are allowed.
    *  additional_name Receiver's additional name (max. 35 chars)
    *  street          Receiver's street/PO Box (max. 35 chars)
    *  city            Receiver's city (max. 35 chars)
    *  bank_code       Receiver's bank code (8 or 11 char BIC)
    *  account_number  Receiver's account number (up to 34 char IBAN)
    *
    * @param array  $account_receiver Receiver's account data.
    * @param double $amount           Amount of money (Euro) in this exchange.
    * @param array  $purposes         Array of up to 4 lines (max. 35 chars each)
    *                                 for description of the exchange.
    * @param array  $account_sender   Sender's account data.
    *
    * @access public
    * @return boolean
    */
    function addExchange($account_receiver, $amount, $purposes, $account_sender = array())
    {
        $account_receiver = $this->_exchangeFillReceiver($account_receiver);
        $account_sender   = $this->_exchangeFillSender($account_sender);

        /*
         * notes for IBAN: currently only checked for length;
         *   we can use PEAR::Validate_Finance_IBAN once it
         *   gets a 'beta' or 'stable' status
         * the minimum length of 12 is chosen arbitrarily as
         *   an additional plausibility check; currently the
         *   shortest real IBANs have 15 chars
         */
        $cents = (int)(round($amount * 100));
        if (strlen($account_receiver['name']) > 0
            && strlen($account_receiver['bank_code']) == 11
            && strlen($account_receiver['account_number']) > 12
            && strlen($account_receiver['account_number']) <= 34
            && strlen($account_sender['name']) > 0
            && strlen($account_sender['bank_code']) > 0
            && strlen($account_sender['bank_code']) <= 8
            && ctype_digit($account_sender['bank_code'])
            && strlen($account_sender['account_number']) > 0
            && strlen($account_sender['account_number']) <= 10
            && ctype_digit($account_sender['account_number'])
            && is_numeric($amount) && $cents > 0
            && $cents <= $this->max_amount
            && $this->sum_amounts <= PHP_INT_MAX - $cents
            && ((is_array($purposes) && count($purposes) >= 1 && count($purposes) <= 4)
                || (is_string($purposes) && strlen($purposes) > 0))
        ) {

            $this->sum_amounts += $cents;

            if (is_string($purposes)) {
                $filtered_purposes = str_split(
                    $this->makeValidString($purposes), 35
                );
                $filtered_purposes = array_slice($filtered_purposes, 0, 14);
            } else {
                $filtered_purposes = array();
                array_slice($purposes, 0, 4);
                foreach ($purposes as $purposeline) {
                    $filtered_purposes[] = $this->filter($purposeline, 35);
                }
            }
            // ensure four lines
            $filtered_purposes = array_slice(
                array_pad($filtered_purposes, 4, ""), 0, 4
            );

            $this->exchanges[] = array(
                "sender_name"              => $this->filter($account_sender['name'], 35),
                "sender_additional_name"   => $this->filter($account_sender['additional_name'], 35),
                "sender_street"            => $this->filter($account_sender['street'], 35),
                "sender_city"              => $this->filter($account_sender['city'], 35),
                "sender_bank_code"         => $account_sender['bank_code'],
                "sender_account_number"    => $account_sender['account_number'],
                "receiver_name"            => $this->filter($account_receiver['name'], 35),
                "receiver_additional_name" => $this->filter($account_receiver['additional_name'], 35),
                "receiver_street"          => $this->filter($account_receiver['street'], 35),
                "receiver_city"            => $this->filter($account_receiver['city'], 35),
                "receiver_bank_code"       => $account_receiver['bank_code'],
                "receiver_account_number"  => $account_receiver['account_number'],
                "amount"                   => $cents,
                "purposes"                 => $filtered_purposes
            );

            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
    * Returns the full content of the generated DTAZV file.
    * All added exchanges are processed.
    *
    * @access public
    * @return string
    */
    function getFileContent()
    {
        $content = "";

        /* The checksum in DTAZV adds only the integer parts of all
         * transfered amounts and is different from the sum of amounts.
         */
        $checksum_amounts = 0;
        $sum_amounts      = 0;

        /**
         * data record Q
         */

        // Q01 record length (256 Bytes)
        $content .= "0256";
        // Q02 record type
        $content .= "Q";
        // Q03 BLZ receiving this file (usually the sender's bank)
        $content .= str_pad(
            $this->account_file_sender['bank_code'], 8, "0", STR_PAD_LEFT
        );
        // Q04 customer number (usually the sender's account)
        $content .= str_pad(
            $this->account_file_sender['account_number'], 10, "0", STR_PAD_LEFT
        );
        // Q05 sender's address
        $content .= str_pad(
            $this->account_file_sender['name'], 35, " ", STR_PAD_RIGHT
        );
        $content .= str_pad(
            $this->account_file_sender['additional_name'], 35, " ", STR_PAD_RIGHT
        );
        $content .= str_pad(
            $this->account_file_sender['street'], 35, " ", STR_PAD_RIGHT
        );
        $content .= str_pad(
            $this->account_file_sender['city'], 35, " ", STR_PAD_RIGHT
        );
        // Q06 date of file creation
        $content .= strftime("%y%m%d", $this->timestamp);
        // Q07 daily counter
        // UNSURE if necessary
        $content .= "00";
        // Q08 execution date
        $content .= strftime("%y%m%d", $this->timestamp);
        // Q09 notification to federal bank
        // according to specification (see above)
        // transfers <= 12500 Euro do not have to be reported
        $content .= "N";
        // Q10 notification data
        $content .= "00";
        // Q11 notification BLZ
        $content .= str_repeat("0", 8);
        // Q12 reserve
        $content .= str_repeat(" ", 68);

        assert(strlen($content) == 256);

        /**
         * data record(s) T
         */

        foreach ($this->exchanges as $exchange) {
            $sum_amounts      += intval($exchange['amount']);
            $checksum_amounts += intval($exchange['amount']/100);

            // T01 record length
            $content .= "0768";
            // T02 record type
            $content .= "T";
            // T03 sender's bank
            $content .= str_pad(
                $exchange['sender_bank_code'], 8, "0", STR_PAD_LEFT
            );
            // T04a currency (fixed)
            $content .= "EUR";
            // T04b sender's account
            $content .= str_pad(
                $exchange['sender_account_number'], 10, "0", STR_PAD_LEFT
            );
            // T05 execution date (optional, if != Q6)
            $content .= str_repeat("0", 6);
            // T06 BLZ, empty for Standardüberweisung
            $content .= str_repeat("0", 8);
            // T07a currency, empty for Standardüberweisung
            $content .= str_repeat(" ", 3);
            // T07b account, empty for Standardüberweisung
            $content .= str_repeat("0", 10);
            // T08 receiver's BIC
            $content .= str_pad(
                $exchange['receiver_bank_code'], 11, "X", STR_PAD_RIGHT
            );
            // T09a country code, empty for Standardüberweisung
            $content .= str_repeat(" ", 3);
            // T09b receiver's bank address, empty for Standardüberweisung
            $content .= str_repeat(" ", 4*35);
            // T10a receiver's country code --> use cc from IBAN
            $content .= substr($exchange['receiver_account_number'], 0, 2) . ' ';
            // T10b receiver's address
            $content .= str_pad(
                $exchange['receiver_name'], 35, " ", STR_PAD_RIGHT
            );
            $content .= str_pad(
                $exchange['receiver_additional_name'], 35, " ", STR_PAD_RIGHT
            );
            $content .= str_pad(
                $exchange['receiver_street'], 35, " ", STR_PAD_RIGHT
            );
            $content .= str_pad(
                $exchange['receiver_city'], 35, " ", STR_PAD_RIGHT
            );
            // T11 empty for Standardüberweisung
            $content .= str_repeat(" ", 2*35);
            // T12 receiver's IBAN
            $content .= '/' . str_pad(
                $exchange['receiver_account_number'], 34, " ", STR_PAD_RIGHT
            );
            // T13 currency
            $content .= "EUR";
            // T14a amount (integer)
            $content .= str_pad(
                intval($exchange['amount']/100), 14, "0", STR_PAD_LEFT
            );
            // T14b amount (decimal places)
            $content .= str_pad(($exchange['amount']%100)*10, 3, "0", STR_PAD_LEFT);
            // T15 purpose
            $content .= str_pad($exchange['purposes'][0], 35, " ", STR_PAD_RIGHT);
            $content .= str_pad($exchange['purposes'][1], 35, " ", STR_PAD_RIGHT);
            $content .= str_pad($exchange['purposes'][2], 35, " ", STR_PAD_RIGHT);
            $content .= str_pad($exchange['purposes'][3], 35, " ", STR_PAD_RIGHT);
            // T16--T20 instruction code, empty for Standardüberweisung
            $content .= str_repeat("0", 4*2);
            $content .= str_repeat(" ", 25);
            // T21 fees
            $content .= "00";
            // T22 payment type
            $content .= "13";
            // T23 free text for accounting
            $content .= str_repeat(" ", 27);
            // T24 contact details
            $content .= str_repeat(" ", 35);
            // T25 reporting key
            $content .= "0";
            // T26 reserve
            $content .= str_repeat(" ", 51);
            // T26 following report extension
            $content .= "00";
        }

        assert((strlen($content) - 256) % 768 == 0);

        /**
         * data record Z
         */

        // Z01 record length
        $content .= "0256";
        // Z02 record type
        $content .= "Z";
        // Z03 sum of amounts (integer parts in T14a)
        assert($sum_amounts == $this->sum_amounts);
        $content .= str_pad(intval($checksum_amounts), 15, "0", STR_PAD_LEFT);
        // Z04 number of records type T
        $content .= str_pad(count($this->exchanges), 15, "0", STR_PAD_LEFT);
        // Z05 reserve
        $content .= str_repeat(" ", 221);

        assert(strlen($content) >= 512);
        assert((strlen($content) - 512) % 768 == 0);

        return $content;
    }

    /**
    * Set the maximum allowed amount per transfer.
    * Pass 0 to disable the check (will set to maximum integer value).
    *
    * <b>Warning</b>: Use at your own risk.
    *
    * Amounts > 12500 Euro usually have notification requirements.
    *
    * Amounts > 50000 Euro are not allowed in a "EU-Standardüberweisung",
    *   thus yielding a malformed DTAZV.
    *
    * @param integer $newmax New maximum allowed amount in Euro
    *                        or 0 to disable check.
    *
    * @access public
    * @since 1.3.2
    * @link http://www.bundesbank.de/meldewesen/mw_aussenwirtschaft.en.php
    *      info on notification requirements
    * @return void
    */
    function setMaxAmount($newmax)
    {
        if ((int)$newmax == 0 || $newmax > PHP_INT_MAX/100) {
            $this->max_amount = PHP_INT_MAX;
        } else {
            $this->max_amount = (int)(round($newmax * 100));
        }
    }

    /**
    * Returns an array with information about the transactions.
    *
    * @access public
    * @return array Returns an array with keys: "sender_name",
    *   "sender_bank_code", "sender_account", "sum_amounts",
    *   "type", "sum_bankcodes", "sum_accounts", "count", "date"
    */
    function getMetaData()
    {
        $meta = parent::getMetaData();

        $meta["type"] = "CREDIT";

        return $meta;
    }

    /**
    * Auxillary parser to consume Q records.
    *
    * @param string  $input   content of DTAZV file
    * @param integer &$offset read offset into $input
    *
    * @throws Payment_DTA_Exception on unrecognized input
    * @access private
    * @return void
    */
    private function _parseQrecord($input, &$offset)
    {
        $Q = array();

        /* field Q01+Q02 record length and type */
        $this->checkStr($input, $offset, "0256Q");
        /* field Q03 BLZ receiving this file */
        $Q['bank_code']       = $this->getNum($input, $offset, 8);
        /* field Q04 customer number */
        $Q['account_number']  = $this->getNum($input, $offset, 10);
        /* field Q05 sender's address */
        $Q['name']            = rtrim($this->getStr($input, $offset, 35, true));
        $Q['additional_name'] = rtrim($this->getStr($input, $offset, 35, true));
        $Q['street']          = rtrim($this->getStr($input, $offset, 35, true));
        $Q['city']            = rtrim($this->getStr($input, $offset, 35, true));
        /* field Q06 date of file creation -- use to set timestamp */
        $Qdate_year  = $this->getNum($input, $offset, 2);
        $Qdate_month = $this->getNum($input, $offset, 2);
        $Qdate_day   = $this->getNum($input, $offset, 2);
        $this->timestamp = mktime(
            0, 0, 0,
            intval($Qdate_month), intval($Qdate_day), intval($Qdate_year)
        );
        /* field Q07 daily counter -- ignored */
        $this->getNum($input, $offset, 2);
        /* field Q08 execution date -- ignored */
        $this->getNum($input, $offset, 6);
        /* field Q09 notification to federal bank */
        $this->checkStr($input, $offset, "N");
        /* field Q10 notification data */
        $this->checkStr($input, $offset, "00");
        /* field Q11 notification BLZ */
        $this->checkStr($input, $offset, str_repeat("0", 8));
        /* field Q12 reserve */
        $this->checkStr($input, $offset, str_repeat(" ", 68));

        $rc = $this->setAccountFileSender(
            array(
            "name"            => $Q['name'],
            "bank_code"       => $Q['bank_code'],
            "account_number"  => $Q['account_number'],
            "additional_name" => $Q['additional_name'],
            )
        );

        $rc = $this->setAccountFileSender($Q);
        if (!$rc) {
            // should never happen
            throw new Payment_DTA_FatalParseException(
                "Cannot setAccountFileSender(), please file a bug report");
        }
    }

    /**
    * Auxillary parser to consume T records.
    *
    * @param string  $input   content of DTAZV file
    * @param integer &$offset read offset into $input
    * @param array   &$checks holds checksums for validation in Z record
    *
    * @throws Payment_DTA_Exception on unrecognized input
    * @access private
    * @return void
    */
    private function _parseTrecord($input, &$offset, &$checks)
    {
        $Tsend = array();
        $Trecv = array();

        /* field T01+02 record length and type */
        $this->checkStr($input, $offset, "0768T");
        /* field T03 sender's bank */
        $Tsend['bank_code'] = $this->getNum($input, $offset, 8);
        /* field T04a currency */
        $this->checkStr($input, $offset, "EUR");
        /* field T04b sender's account */
        $Tsend['account_number'] = $this->getNum($input, $offset, 10);
        /* field T05 execution date -- ignored */
        $this->getNum($input, $offset, 6);
        /* field T06+T07a+T07b empty for Standardüberweisung */
        $this->checkStr($input, $offset, str_repeat("0", 8));
        $this->checkStr($input, $offset, str_repeat(" ", 3));
        $this->checkStr($input, $offset, str_repeat("0", 10));
        /* field T08 receiver's BIC */
        $Trecv['bank_code'] = $this->getStr($input, $offset, 11);
        /* field T09a+T09b empty for Standardüberweisung */
        $this->checkStr($input, $offset, str_repeat(" ", 3+4*35));
        /* field T10a receiver's country code -- ignored */
        $this->getStr($input, $offset, 3);
        /* field T10b receiver's address */
        $Trecv['name']            = $this->getStr($input, $offset, 35);
        $Trecv['additional_name'] = $this->getStr($input, $offset, 35);
        $Trecv['street']          = $this->getStr($input, $offset, 35);
        $Trecv['city']            = $this->getStr($input, $offset, 35);
        /* field T11 empty for Standardüberweisung */
        $this->checkStr($input, $offset, str_repeat(" ", 2*35));
        /* field T12 receiver's IBAN */
        $this->checkStr($input, $offset, '/');
        $Trecv['account_number'] = $this->getStr($input, $offset, 34);
        /* field T13 currency */
        $this->checkStr($input, $offset, "EUR");
        /* field T14a amount (integer) */
        $amount_int = $this->getNum($input, $offset, 14);
        /* field T14b amount (decimal places) */
        $amount_dec = $this->getNum($input, $offset, 3);
        $amount = $amount_int + $amount_dec/1000.0;
        /* field T15 purpose */
        $purposes = array();
        $purposes[0] = $this->getStr($input, $offset, 35);
        $purposes[1] = $this->getStr($input, $offset, 35);
        $purposes[2] = $this->getStr($input, $offset, 35);
        $purposes[3] = $this->getStr($input, $offset, 35);
        /* field T16--T20 instruction code, empty for Standardüberweisung */
        $this->checkStr($input, $offset, str_repeat("0", 4*2));
        $this->checkStr($input, $offset, str_repeat(" ", 25));
        /* field T21 fees */
        $this->checkStr($input, $offset, "00");
        /* field T22 payment type */
        $this->checkStr($input, $offset, "13");
        /* field T23 free text for accounting -- ignored */
        $this->getStr($input, $offset, 27);
        /* field T24 contact details -- ignored */
        $this->getStr($input, $offset, 35);
        /* field T25 reporting key */
        $this->checkStr($input, $offset, "0");
        /* field T26 reserve */
        $this->checkStr($input, $offset, str_repeat(" ", 51));
        /* field T26 following report extension */
        $this->checkStr($input, $offset, "00");

        /* we read the fields, now add an exchange */
        $rc = $this->addExchange(
            $Trecv,
            $amount,
            $purposes,
            $Tsend
        );
        if (!$rc) {
            // should never happen
            throw new Payment_DTA_ParseException("Cannot addExchange() ".
                "for transaction number ".strval($this->count()+1).
                ", please file a bug report");
        }
        $checks['amount'] += $amount_int;
    }

    /**
    * Auxillary parser to consume Z records.
    *
    * @param string  $input   content of DTAZV file
    * @param integer &$offset read offset into $input
    * @param array   $checks  holds checksums for validation
    *
    * @throws Payment_DTA_Exception on unrecognized input
    * @access private
    * @return void
    */
    private function _parseZrecord($input, &$offset, $checks)
    {
        /* field Z01+Z02 record length and type */
        $this->checkStr($input, $offset, "0256Z");
        /* field Z03 sum of amounts (integer parts in T14a) */
        $Z_check_amount = $this->getNum($input, $offset, 15);
        /* field Z04 number of records type T */
        $Z_check_count = $this->getNum($input, $offset, 15);
        /* field Z05 reserve */
        $this->checkStr($input, $offset, str_repeat(" ", 221));

        if ($Z_check_count != $this->count()) {
                    throw new Payment_DTA_ChecksumException(
                        "Z record checksum mismatch for transaction count: ".
                        "reads $Z_check_count, expected ".$this->count());
        }
        if ($Z_check_amount != $checks['amount']) {
                    throw new Payment_DTA_ChecksumException(
                        "Z record checksum mismatch for transfer amount: ".
                        "reads $Z_check_amount, expected ".$checks['amount']);
        }
    }

    /**
    * Parser. Read data from an existing DTAZV file content.
    *
    * @param string $input content of DTAZV file
    *
    * @throws Payment_DTA_Exception on unrecognized input
    * @access protected
    * @return void
    */
    protected function parse($input)
    {
        if (strlen($input) % 128) {
            throw new Payment_DTA_FatalParseException("invalid length");
        }

        $checks = array('amount' => 0);
        $offset = 0;

        /* Q record */
        try {
            $this->_parseQrecord($input, $offset);
        } catch (Payment_DTA_Exception $e) {
            throw new Payment_DTA_FatalParseException("Exception in Q record", $e);
        }

        //do not consume input by using getStr()/getNum() here
        while ($input[$offset + 4] == 'T') {
            /* T record */
            $t_start = $offset;
            try {
                $this->_parseTrecord($input, $offset, $checks);
            } catch (Payment_DTA_Exception $e) {
                // preserve error
                $this->allerrors[] = new Payment_DTA_ParseException(
                    "Error in T record, in transaction number ".
                    strval($this->count()+1), $e);
                // skip to next record
                $offset = $t_start + 768;
            }
        } // while

        /* Z record */
        try {
            $this->_parseZrecord($input, $offset, $checks);
        } catch (Payment_DTA_ChecksumException $e) {
            throw $e;
        } catch (Payment_DTA_Exception $e) {
            throw new Payment_DTA_ParseException("Error in Z record", $e);
        }
    }

}

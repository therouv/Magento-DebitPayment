<?php
/**
 * DTA
 *
 * DTA is a class that provides functions to create DTA files used in
 * Germany to exchange informations about money transactions with banks
 * or online banking programs.
 *
 * PHP version 5
 *
 * This LICENSE is in the BSD license style.
 *
 * Copyright (c) 2003-2005 Hermann Stainer, Web-Gear
 * http://www.web-gear.com/
 * Copyright (c) 2008-2010 Martin Schütte
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
 * @author    Hermann Stainer <hs@web-gear.com>
 * @author    Martin Schütte <info@mschuette.name>
 * @copyright 2003-2005 Hermann Stainer, Web-Gear
 * @copyright 2008-2010 Martin Schütte
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id: DTA.php 316502 2011-09-11 19:51:04Z mschuett $
 * @link      http://pear.php.net/package/Payment_DTA
 */

/**
 * needs base class
 */
require_once 'DTA/DTABase.php';

/**
* Determines the type of the DTA file:
* DTA file contains credit payments.
*
* @const DTA_CREDIT
*/
define("DTA_CREDIT", 0);

/**
* Determines the type of the DTA file:
* DTA file contains debit payments (default).
*
* @const DTA_DEBIT
*/
define("DTA_DEBIT", 1);


/**
* Dta class provides functions to create and handle with DTA files
* used in Germany to exchange informations about money transactions with
* banks or online banking programs.
*
* Specifications:
* - http://www.ebics-zka.de/dokument/pdf/Anlage%203-Spezifikation%20der%20Datenformate%20-%20Version%202.3%20Endfassung%20vom%2005.11.2008.pdf,
*   part 1.1 DTAUS0, p. 4ff
* - http://www.bundesbank.de/download/zahlungsverkehr/zv_spezifikationen_v1_5.pdf
* - http://www.hbci-zka.de/dokumente/aenderungen/DTAUS_2002.pdf
*
* @category Payment
* @package  Payment_DTA
* @author   Hermann Stainer <hs@web-gear.com>
* @license  http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
* @version  Release: 1.4.2
* @link     http://pear.php.net/package/Payment_DTA
*/
class DTA extends DTABase
{
    /**
    * Type of DTA file, DTA_CREDIT or DTA_DEBIT.
    *
    * @var integer $type
    */
    protected $type;

    /**
    * Sum of bank codes in exchanges; used for control fields.
    *
    * @var integer $sum_bankcodes
    */
    protected $sum_bankcodes;

    /**
    * Sum of account numbers in exchanges; used for control fields.
    *
    * @var integer $sum_accounts
    */
    protected $sum_accounts;

    /**
    * Constructor. Creates an empty DTA object or imports one.
    *
    * If the parameter is a string, then it is expected to be in DTA format
    * an its content (sender and transactions) is imported. If the string cannot
    * be parsed at all then an empty DTA object with type DTA_CREDIT is returned.
    * If only parts of the string can be parsed, then all transactions before the
    * error are included into the object.
    * The user should use getParsingError() to check whether a parsing error occured.
    *
    * Otherwise the parameter has to be the type of the new DTA object,
    * either DTA_CREDIT or DTA_DEBIT. In this case exceptions are never
    * thrown to ensure compatibility.
    *
    * @param integer|string $type Either a string with DTA data or the type of the
    *                       new DTA file (DTA_CREDIT or DTA_DEBIT). Must be set.
    *
    * @access public
    */
    function __construct($type)
    {
        parent::__construct();
        $this->sum_bankcodes = 0;
        $this->sum_accounts  = 0;

        if (is_int($type)) {
            $this->type = $type;
        } else {
            try {
                $this->parse($type);
            } catch (Payment_DTA_FatalParseException $e) {
                // cannot construct this object, reset everything
                parent::__construct();
                $this->sum_bankcodes = 0;
                $this->sum_accounts  = 0;
                $this->type = DTA_CREDIT;
                $this->allerrors[] = $e;
            } catch (Payment_DTA_Exception $e) {
                // object is valid, but save the error
                $this->allerrors[] = $e;
            }
        }
    }

    /**
    * Set the sender of the DTA file. Must be set for valid DTA file.
    * The given account data is also used as default sender's account.
    * Account data contains
    *  name            Sender's name. Maximally 27 chars are allowed.
    *  bank_code       Sender's bank code.
    *  account_number  Sender's account number.
    *  additional_name If necessary, additional line for sender's name
    *                  (maximally 27 chars).
    *  exec_date       Optional execution date for the DTA file in format DDMMYYYY.
    *
    * @param array $account Account data for file sender.
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

            if (empty($account['exec_date'])
                || !ctype_digit($account['exec_date'])) {
            	$account['exec_date'] = str_repeat(" ", 8);
            }

            $this->account_file_sender = array(
                "name"            => $this->filter($account['name'], 27),
                "bank_code"       => $account['bank_code'],
                "account_number"  => $account['account_number'],
                "additional_name" => $this->filter($account['additional_name'], 27),
                "exec_date"       => $account['exec_date']
            );

            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
    * Auxillary method to fill and normalize the receiver and sender arrays.
    *
    * @param array $account_receiver Receiver's account data.
    * @param array $account_sender   Sender's account data.
    *
    * @access private
    * @return array
    */
    private function _exchangeFillArrays($account_receiver, $account_sender)
    {
        if (empty($account_receiver['additional_name'])) {
            $account_receiver['additional_name'] = "";
        }
        if (empty($account_sender['name'])) {
            $account_sender['name'] = $this->account_file_sender['name'];
        }
        if (empty($account_sender['bank_code'])) {
            $account_sender['bank_code'] = $this->account_file_sender['bank_code'];
        }
        if (empty($account_sender['account_number'])) {
            $account_sender['account_number']
                = $this->account_file_sender['account_number'];
        }
        if (empty($account_sender['additional_name'])) {
            $account_sender['additional_name']
                = $this->account_file_sender['additional_name'];
        }

        $account_receiver['account_number']
            = strval($account_receiver['account_number']);
        $account_receiver['bank_code']
            = strval($account_receiver['bank_code']);
        $account_sender['account_number']
            = strval($account_sender['account_number']);
        $account_sender['bank_code']
            = strval($account_sender['bank_code']);

        return array($account_receiver, $account_sender);
    }

    /**
    * Adds an exchange. First the account data for the receiver of the exchange is
    * set. In the case the DTA file contains credits, this is the payment receiver.
    * In the other case (the DTA file contains debits), this is the account, from
    * which money is taken away. If the sender is not specified, values of the
    * file sender are used by default.
    *
    * Account data for receiver and sender contain
    *  name            Name. Maximally 27 chars are allowed.
    *  bank_code       Bank code.
    *  account_number  Account number.
    *  additional_name If necessary, additional line for name (maximally 27 chars).
    *
    * @param array  $account_receiver Receiver's account data.
    * @param double $amount           Amount of money in this exchange.
    *                                 Currency: EURO
    * @param array  $purposes         Array of up to 14 lines
    *                                 (maximally 27 chars each) for
    *                                 description of the exchange.
    *                                 A string is accepted as well.
    * @param array  $account_sender   Sender's account data.
    *
    * @access public
    * @return boolean
    */
    function addExchange(
        $account_receiver,
        $amount,
        $purposes,
        $account_sender = array()
    ) {
        list($account_receiver, $account_sender)
            = $this->_exchangeFillArrays($account_receiver, $account_sender);

        $cents = (int)(round($amount * 100));
        if (strlen($account_sender['name']) > 0
            && strlen($account_sender['bank_code']) > 0
            && strlen($account_sender['bank_code']) <= 8
            && ctype_digit($account_sender['bank_code'])
            && strlen($account_sender['account_number']) > 0
            && strlen($account_sender['account_number']) <= 10
            && ctype_digit($account_sender['account_number'])
            && strlen($account_receiver['name']) > 0
            && strlen($account_receiver['bank_code']) <= 8
            && ctype_digit($account_receiver['bank_code'])
            && strlen($account_receiver['account_number']) <= 10
            && ctype_digit($account_receiver['account_number'])
            && is_numeric($amount)
            && $cents > 0
            && $cents <= PHP_INT_MAX
            && $this->sum_amounts <= (PHP_INT_MAX - $cents)
            && ( (is_string($purposes)
                   && strlen($purposes) > 0)
                || (is_array($purposes)
                   && count($purposes) >= 1
                   && count($purposes) <= 14))
        ) {
            $this->sum_amounts   += $cents;
            $this->sum_bankcodes += $account_receiver['bank_code'];
            $this->sum_accounts  += $account_receiver['account_number'];

            if (is_string($purposes)) {
                $filtered_purposes = str_split(
                    $this->makeValidString($purposes), 27
                );
                $filtered_purposes = array_slice($filtered_purposes, 0, 14);
            } else {
                $filtered_purposes = array();
                foreach ($purposes as $purposeline) {
                    $filtered_purposes[] = $this->filter($purposeline, 27);
                }
            }

            $this->exchanges[] = array(
                "sender_name"              => $this->filter(
                    $account_sender['name'], 27
                ),
                "sender_bank_code"         => $account_sender['bank_code'],
                "sender_account_number"    => $account_sender['account_number'],
                "sender_additional_name"   => $this->filter(
                    $account_sender['additional_name'], 27
                ),
                "receiver_name"            => $this->filter(
                    $account_receiver['name'], 27
                ),
                "receiver_bank_code"       => $account_receiver['bank_code'],
                "receiver_account_number"  => $account_receiver['account_number'],
                "receiver_additional_name" => $this->filter(
                    $account_receiver['additional_name'], 27
                ),
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
    * Auxillary method to write the A record.
    *
    * @access private
    * @return string
    */
    private function _generateArecord()
    {
        $content = "";

        // (field numbers according to ebics-zka.de specification)
        // A1 record length (128 Bytes)
        $content .= str_pad("128", 4, "0", STR_PAD_LEFT);
        // A2 record type
        $content .= "A";
        // A3 file mode (credit or debit)
        // and Customer File ("K") / Bank File ("B")
        $content .= ($this->type == DTA_CREDIT) ? "G" : "L";
        $content .= "K";
        // A4 sender's bank code
        $content .= str_pad(
            $this->account_file_sender['bank_code'], 8, "0", STR_PAD_LEFT
        );
        // A5 only used if Bank File, otherwise NULL
        $content .= str_repeat("0", 8);
        // A6 sender's name
        $content .= str_pad(
            $this->account_file_sender['name'], 27, " ", STR_PAD_RIGHT
        );
        // A7 date of file creation
        $content .= strftime("%d%m%y", $this->timestamp);
        // A8 free (bank internal)
        $content .= str_repeat(" ", 4);
        // A9 sender's account number
        $content .= str_pad(
            $this->account_file_sender['account_number'], 10, "0", STR_PAD_LEFT
        );
        // A10 sender's reference number (optional)
        $content .= str_repeat("0", 10);
        // A11a free (reserve)
        $content .= str_repeat(" ", 15);
        // A11b execution date ("DDMMYYYY", optional)
        $content .= $this->account_file_sender['exec_date'];
        // A11c free (reserve)
        $content .= str_repeat(" ", 24);
        // A12 currency (1 = Euro)
        $content .= "1";

        assert(strlen($content) == 128);
        return $content;
    }

    /**
    * Auxillary method to write C records.
    *
    * @param array $exchange The transaction to serialize.
    *
    * @access private
    * @return string
    */
    private function _generateCrecord($exchange)
    {
        // preparation of additional parts for record extensions
        $additional_parts    = array();
        $additional_purposes = $exchange['purposes'];
        $first_purpose       = array_shift($additional_purposes);

        if (strlen($exchange['receiver_additional_name']) > 0) {
            $additional_parts[] = array("type" => "01",
                "content" => $exchange['receiver_additional_name']
                );
        }

        foreach ($additional_purposes as $additional_purpose) {
            $additional_parts[] = array("type" => "02",
                "content" => $additional_purpose
                );
        }

        if (strlen($exchange['sender_additional_name']) > 0) {
            $additional_parts[] = array("type" => "03",
                "content" => $exchange['sender_additional_name']
                );
        }
        assert(count($additional_parts) <= 15);

        $content = "";

        // (field numbers according to ebics-zka.de specification)
        // C1 record length (187 Bytes + 29 Bytes for each additional part)
        $content .= str_pad(
            187 + count($additional_parts) * 29, 4, "0", STR_PAD_LEFT
        );
        // C2 record type
        $content .= "C";
        // C3 first involved bank
        $content .= str_pad(
            $exchange['sender_bank_code'], 8, "0", STR_PAD_LEFT
        );
        // C4 receiver's bank code
        $content .= str_pad(
            $exchange['receiver_bank_code'], 8, "0", STR_PAD_LEFT
        );
        // C5 receiver's account number
        $content .= str_pad(
            $exchange['receiver_account_number'], 10, "0", STR_PAD_LEFT
        );
        // C6 internal customer number (11 chars) or NULL
        $content .= "0" . str_repeat("0", 11) . "0";
        // C7a payment mode (text key)
        $content .= ($this->type == DTA_CREDIT) ? "51" : "05";
        // C7b additional text key
        $content .= "000";
        // C8 bank internal
        $content .= " ";
        // C9 free (reserve)
        $content .= str_repeat("0", 11);
        // C10 sender's bank code
        $content .= str_pad(
            $exchange['sender_bank_code'], 8, "0", STR_PAD_LEFT
        );
        // C11 sender's account number
        $content .= str_pad(
            $exchange['sender_account_number'], 10, "0", STR_PAD_LEFT
        );
        // C12 amount
        $content .= str_pad(
            $exchange['amount'], 11, "0", STR_PAD_LEFT
        );
        // C13 free (reserve)
        $content .= str_repeat(" ", 3);
        // C14a receiver's name
        $content .= str_pad(
            $exchange['receiver_name'], 27, " ", STR_PAD_RIGHT
        );
        // C14b delimitation
        $content .= str_repeat(" ", 8);
        /* first part/128 chars full */
        // C15 sender's name
        $content .= str_pad(
            $exchange['sender_name'], 27, " ", STR_PAD_RIGHT
        );
        // C16 first line of purposes
        $content .= str_pad($first_purpose, 27, " ", STR_PAD_RIGHT);
        // C17a currency (1 = Euro)
        $content .= "1";
        // C17b free (reserve)
        $content .= str_repeat(" ", 2);
        // C18 number of additional parts (00-15)
        $content .= str_pad(count($additional_parts), 2, "0", STR_PAD_LEFT);

        /*
         * End of the constant part (187 chars),
         * now up to 15 extensions with 29 chars each might follow.
         */

        if (count($additional_parts) == 0) {
            // no extension, pad to fill the part to 2*128 chars
            $content .= str_repeat(" ", 256-187);
        } else {
            // The first two extensions fit into the current part:
            for ($index = 1;$index <= 2;$index++) {
                if (count($additional_parts) > 0) {
                    $additional_part = array_shift($additional_parts);
                } else {
                    $additional_part = array("type" => "  ",
                        "content" => ""
                        );
                }
                // C19/21 type of addional part
                $content .= $additional_part['type'];
                // C20/22 additional part content
                $content .= str_pad(
                    $additional_part['content'], 27, " ", STR_PAD_RIGHT
                );
            }
            // delimitation
            $content .= str_repeat(" ", 11);
        }

        // For more extensions add up to 4 more parts:
        for ($part = 3;$part <= 5;$part++) {
            if (count($additional_parts) > 0) {
                for ($index = 1;$index <= 4;$index++) {
                    if (count($additional_parts) > 0) {
                        $additional_part = array_shift($additional_parts);
                    } else {
                        $additional_part = array("type" => "  ",
                            "content" => ""
                            );
                    }
                    // C24/26/28/30 type of addional part
                    $content .= $additional_part['type'];
                    // C25/27/29/31 additional part content
                    $content .= str_pad(
                        $additional_part['content'], 27, " ", STR_PAD_RIGHT
                    );
                }
                // C32 delimitation
                $content .= str_repeat(" ", 12);
            }
        }
        // with 15 extensions there may be a 6th part
        if (count($additional_parts) > 0) {
            $additional_part = array_shift($additional_parts);
            // C24 type of addional part
            $content .= $additional_part['type'];
            // C25 additional part content
            $content .= str_pad(
                $additional_part['content'], 27, " ", STR_PAD_RIGHT
            );
            // padding to fill the part
            $content .= str_repeat(" ", 128-27-2);
        }
        assert(count($additional_parts) == 0);
        assert(strlen($content) % 128 == 0);
        return $content;
    }

    /**
    * Auxillary method to write the E record.
    *
    * @access private
    * @return string
    */
    private function _generateErecord()
    {
        $content = "";

        // (field numbers according to ebics-zka.de specification)
        // E1 record length (128 bytes)
        $content .= str_pad("128", 4, "0", STR_PAD_LEFT);
        // E2 record type
        $content .= "E";
        // E3 free (reserve)
        $content .= str_repeat(" ", 5);
        // E4 number of records type C
        $content .= str_pad(count($this->exchanges), 7, "0", STR_PAD_LEFT);
        // E5 free (reserve)
        $content .= str_repeat("0", 13);
        // use number_format() to ensure proper integer formatting
        // E6 sum of account numbers
        $content .= str_pad(
            number_format($this->sum_accounts, 0, "", ""), 17, "0", STR_PAD_LEFT
        );
        // E7 sum of bank codes
        $content .= str_pad(
            number_format($this->sum_bankcodes, 0, "", ""), 17, "0", STR_PAD_LEFT
        );
        // E8 sum of amounts
        $content .= str_pad(
            number_format($this->sum_amounts, 0, "", ""), 13, "0", STR_PAD_LEFT
        );
        // E9 delimitation
        $content .= str_repeat(" ", 51);

        assert(strlen($content) % 128 == 0);
        return $content;
    }

    /**
    * Returns the full content of the generated DTA file.
    * All added exchanges are processed.
    *
    * @access public
    * @return string
    */
    function getFileContent()
    {
        $content = "";

        /**
         * data record A
         */
        $content .= $this->_generateArecord();

        /**
         * data record(s) C
         */
        $sum_account_numbers = 0;
        $sum_bank_codes      = 0;
        $sum_amounts         = 0;

        foreach ($this->exchanges as $exchange) {
            $sum_account_numbers += $exchange['receiver_account_number'];
            $sum_bank_codes      += (int) $exchange['receiver_bank_code'];
            $sum_amounts         += (int) $exchange['amount'];

            $content .= $this->_generateCrecord($exchange);
            assert(strlen($content) % 128 == 0);
        }

        assert($this->sum_amounts   === $sum_amounts);
        assert($this->sum_bankcodes === $sum_bank_codes);
        assert($this->sum_accounts  === $sum_account_numbers);

        /**
         * data record E
         */
        $content .= $this->_generateErecord();

        return $content;
    }

    /**
    * Returns an array with information about the transactions.
    * Can be used to print an accompanying document (Begleitzettel) for disks.
    *
    * @access public
    * @return array Returns an array with keys: "sender_name",
    *   "sender_bank_code", "sender_account", "sum_amounts",
    *   "type", "sum_bankcodes", "sum_accounts", "count", "date", "exec_date"
    */
    function getMetaData()
    {
        $meta = parent::getMetaData();

        $meta["sum_bankcodes"] = floatval($this->sum_bankcodes);
        $meta["sum_accounts"]  = floatval($this->sum_accounts);
        $meta["type"] = strval(($this->type == DTA_CREDIT) ? "CREDIT" : "DEBIT");

        $meta["exec_date"] = $meta["date"];
        // use timestamp to be consistent with $meta["date"]
        if ($this->account_file_sender["exec_date"] !== "") {
            $ftime = strptime($this->account_file_sender["exec_date"], '%d%m%Y');
            if ($ftime) {
                $meta["exec_date"] = mktime(0, 0, 0,
                            $ftime['tm_mon'] + 1,
                            $ftime['tm_mday'],
                            $ftime['tm_year'] + 1900
                         );
            }
        }
        return $meta;
    }

    /**
    * Auxillary parser to consume A records.
    *
    * @param string  $input   content of DTA file
    * @param integer &$offset read offset into $input
    *
    * @throws Payment_DTA_Exception on unrecognized input
    * @access private
    * @return void
    */
    private function _parseArecord($input, &$offset)
    {
        /* field 1+2 */
        $this->checkStr($input, $offset, "0128A");
        /* field  3 */
        $type = $this->getStr($input, $offset, 2);
        /* field  4 */
        $Asender_blz = $this->getNum($input, $offset, 8);
        /* field  5 */
        $this->checkStr($input, $offset, "00000000");
        /* field  6 */
        $Asender_name = rtrim($this->getStr($input, $offset, 27, true));
        /* field  7 */
        $Adate_day   = $this->getNum($input, $offset, 2);
        $Adate_month = $this->getNum($input, $offset, 2);
        $Adate_year  = $this->getNum($input, $offset, 2);
        $this->timestamp = mktime(
            0, 0, 0,
            intval($Adate_month), intval($Adate_day), intval($Adate_year)
        );
        /* field  8 */
        $this->checkStr($input, $offset, "    ");
        /* field  9 */
        $Asender_account = $this->getNum($input, $offset, 10);
        /* field 10 */
        $this->checkStr($input, $offset, "0000000000");
        /* field 11a */
        $this->checkStr($input, $offset, str_repeat(" ", 15));
        /* field 11b */
        $Aexec_date = $this->getStr($input, $offset, 8);
        /* field 11c */
        $this->checkStr($input, $offset, str_repeat(" ", 24));
        /* field 12 */
        $this->checkStr($input, $offset, "1");

        /* the first char G/L indicates credit and debit exchanges
         * the second char K/B indicates a customer or bank file
         * (I do not know if bank files should be treated different)
        */
        if ($type === "GK" || $type === "GB") {
            $this->type = DTA_CREDIT;
        } elseif ($type === "LK" || $type === "LB") {
            $this->type = DTA_DEBIT;
        } else {
            throw new Payment_DTA_FatalParseException(
                "Invalid type indicator: '$type', expected ".
                "either 'GK'/'GB' or 'LK'/'LB' (@offset 6).");
        }

        /*
         * additional_name is problematic and cannot be parsed & reproduced.
         * it is set as part of the AccountFileSender, but appears as part
         * of every transaction.
         */
        $rc = $this->setAccountFileSender(
            array(
            "name"            => $Asender_name,
            "bank_code"       => $Asender_blz,
            "account_number"  => $Asender_account,
            "additional_name" => '',
            "exec_date"       => $Aexec_date
            )
        );
        if (!$rc) {
            // should never happen
            throw new Payment_DTA_FatalParseException(
                "Cannot setAccountFileSender(), please file a bug report");
        }
        // currently not a TODO:
        // does anyone have to preserve the creation date or execution date?
    }

    /**
    * Auxillary method to parse C record extensions.
    *
    * Reads the variable number of extensions at the end of a C record.
    *
    * @param string  $input      content of DTA file
    * @param integer &$offset    read offset into $input
    * @param integer $extensions expected number of extensions
    * @param integer $c_start    C record offset (for exceptions)
    *
    * @throws Payment_DTA_ParseException on invalid extensions
    * @access private
    * @return array of $Cpurpose, 2nd sender line, 2nd receiver line
    */
    private function _parseCextension($input, &$offset, $extensions, $c_start)
    {
        $extensions_read = array();

        // first handle the up to 2 extensions inside the 2nd part
        if ($extensions == 0) { // only padding
            $this->checkStr($input, $offset, str_repeat(" ", 69));
        } elseif ($extensions == 1) {
            /* field 19 */
            $ext_type = $this->getNum($input, $offset, 2);
            /* field 20 */
            $ext_content = $this->getStr($input, $offset, 27, true);
            array_push($extensions_read, array($ext_type, $ext_content));
            /* fields 21,22,23 */
            $this->checkStr($input, $offset, str_repeat(" ", 2+27+11));
        } else {
            /* field 19 */
            $ext_type = $this->getNum($input, $offset, 2);
            /* field 20 */
            $ext_content = $this->getStr($input, $offset, 27, true);
            array_push($extensions_read, array($ext_type, $ext_content));
            /* field 21 */
            $ext_type = $this->getNum($input, $offset, 2);
            /* field 22 */
            $ext_content = $this->getStr($input, $offset, 27, true);
            array_push($extensions_read, array($ext_type, $ext_content));
            /* fields 23 */
            $this->checkStr($input, $offset, str_repeat(" ", 11));
        }
        // end 2nd part of C record
        assert($offset % 128 === 0);

        // up to 4 more parts, each with 128 bytes & up to 4 extensions
        while (count($extensions_read) < $extensions) {
            $ext_in_part = $extensions - count($extensions_read);
            // one switch to read the content
            switch($ext_in_part) {
            default: // =4
            case 4: /* fallthrough */
                $ext_type = $this->getNum($input, $offset, 2);
                $ext_content = $this->getStr($input, $offset, 27, true);
                array_push($extensions_read, array($ext_type, $ext_content));
            case 3: /* fallthrough */
                $ext_type = $this->getNum($input, $offset, 2);
                $ext_content = $this->getStr($input, $offset, 27, true);
                array_push($extensions_read, array($ext_type, $ext_content));
            case 2: /* fallthrough */
                $ext_type = $this->getNum($input, $offset, 2);
                $ext_content = $this->getStr($input, $offset, 27, true);
                array_push($extensions_read, array($ext_type, $ext_content));
            case 1: /* fallthrough */
                $ext_type = $this->getNum($input, $offset, 2);
                $ext_content = $this->getStr($input, $offset, 27, true);
                array_push($extensions_read, array($ext_type, $ext_content));
                break;
            case 0:
                // should never happen
                throw new Payment_DTA_ParseException('confused about '.
                    'number of extensions in transaction number '.
                    strval($this->count()+1) .' @ offset '. strval($c_start) .
                    ', please file a bug report');
            }

            // and one switch for the padding
            switch($ext_in_part) {
            case 1:
                $this->checkStr($input, $offset, str_repeat(" ", 29));
            case 2: /* fallthrough */
                $this->checkStr($input, $offset, str_repeat(" ", 29));
            case 3: /* fallthrough */
                $this->checkStr($input, $offset, str_repeat(" ", 29));
            case 4: /* fallthrough */
            default: /* fallthrough */
                $this->checkStr($input, $offset, str_repeat(" ", 12));
                break;
            }
            // end n-th part of C record
            assert($offset % 128 === 0);
        }
        return $extensions_read;
    }

    /**
    * Auxillary method to combine C record extensions.
    *
    * Takes the parsed extensions to check the allowed number of them per type
    * and to collect all purpose lines into one array.
    *
    * @param array   $extensions_read read extensions as arrays
    * @param array   $Cpurpose        existing array of purpose lines
    * @param integer $c_start         C record offset (for exceptions)
    *
    * @throws Payment_DTA_ParseException on invalid extensions
    * @access private
    * @return array of $Cpurpose, 2nd sender line, 2nd receiver line
    */
    private function _processCextension($extensions_read, $Cpurpose, $c_start)
    {
        $Csender_name2 = "";
        $Creceiver_name2 = "";

        foreach ($extensions_read as $ext) {
            $ext_type = $ext[0];
            $ext_content = $ext[1];

            switch($ext_type) {
            case 1:
                if (!empty($Creceiver_name2)) {
                    throw new Payment_DTA_ParseException('multiple '.
                        'receiver name extensions in transaction number '.
                        strval($this->count()+1) .' @ offset '. strval($c_start));
                } else {
                    $Creceiver_name2 = $ext_content;
                }
                break;
            case 2:
                if (count($Cpurpose) >= 14) {
                    // allowed: 1 line in fixed part + 13 in extensions
                    throw new Payment_DTA_ParseException('too many '.
                        'purpose extensions in transaction number '.
                        strval($this->count()+1) .' @ offset '. strval($c_start));
                } else {
                    array_push($Cpurpose, $ext_content);
                }
                break;
            case 3:
                if (!empty($Csender_name2)) {
                    throw new Payment_DTA_ParseException('multiple '.
                        'receiver name extensions in transaction number '.
                        strval($this->count()+1) .' @ offset '. strval($c_start));
                } else {
                    $Csender_name2 = $ext_content;
                }
                break;
            default:
                throw new Payment_DTA_ParseException('invalid '.
                    'extension type in transaction number '.
                    strval($this->count()+1) .' @ offset '. strval($c_start));
            }
        }

        return array($Cpurpose, $Csender_name2, $Creceiver_name2);
    }

    /**
    * Auxillary parser to consume C records.
    *
    * @param string  $input   content of DTA file
    * @param integer &$offset read offset into $input
    * @param array   &$checks holds checksums for validation in E record
    *
    * @throws Payment_DTA_Exception on unrecognized input
    * @access private
    * @return void
    */
    private function _parseCrecord($input, &$offset, &$checks)
    {
        // save for possible exceptions
        $c_start = $offset;

        /* field 1 */
        $record_length = $this->getNum($input, $offset, 4);
        /* field 2 */
        $this->checkStr($input, $offset, "C");

        // check the record length
        if (($record_length-187)%29) {
            throw new Payment_DTA_ParseException('invalid C record length');
        }
        $extensions_length = ($record_length-187)/29;

        /* field  3 */
        $Cbank_blz = $this->getNum($input, $offset, 8); // usually 0, ignored
        /* field  4 */
        $Creceiver_blz = $this->getNum($input, $offset, 8);
        /* field  5 */
        $Creceiver_account = $this->getNum($input, $offset, 10);
        /* field  6 */
        $this->checkStr($input, $offset, "0");
        // either 0s or aninternal customer number:
        $this->getNum($input, $offset, 11);
        $this->checkStr($input, $offset, "0");
        /* field  7 */
        // may hold about a dozen values with details about the type of transaction
        $Ctype = $this->getStr($input, $offset, 5);
        if ( (($this->type == DTA_DEBIT) && (!preg_match('/^0[45]\d{3}$/', $Ctype)))
            || (($this->type == DTA_CREDIT) && (!preg_match('/^5\d{4}$/', $Ctype)))
        ) {
            throw new Payment_DTA_ParseException('C record type of payment '.
                '(' . $Ctype . ') '.
                'does not match A record type indicator '.
                '(' . (($this->type == DTA_CREDIT) ? "CREDIT" : "DEBIT") . ') '.
                'in transaction number '. strval($this->count()+1) .
                ' @ offset '. strval($c_start));
        }
        /* field  8 */
        $this->checkStr($input, $offset, " ");
        /* field  9 */
        $this->checkStr($input, $offset, "00000000000");
        /* field 10 */
        $Csender_blz = $this->getNum($input, $offset, 8);
        /* field 11 */
        $Csender_account = $this->getNum($input, $offset, 10);
        /* field 12 */
        $Camount = $this->getNum($input, $offset, 11);
        /* field 13 */
        $this->checkStr($input, $offset, "   ");
        /* field 14a */
        $Creceiver_name = rtrim($this->getStr($input, $offset, 27, true));
        /* field 14b */
        $this->checkStr($input, $offset, "        ");
        // end 1st part of C record
        assert($offset % 128 === 0);
        /* field 15 */
        $Csender_name = rtrim($this->getStr($input, $offset, 27, true));
        /* field 16 */
        $Cpurpose = array(rtrim($this->getStr($input, $offset, 27, true)));
        /* field 17a */
        $this->checkStr($input, $offset, "1");
        /* field 17b */
        $this->checkStr($input, $offset, "  ");
        /* field 18 */
        $extensions = $this->getNum($input, $offset, 2);
        if ($extensions != $extensions_length) {
            throw new Payment_DTA_ParseException('number of extensions '.
                'does not match record length in transaction number '.
                strval($this->count()+1) .' @ offset '. strval($c_start));
        }

        // extensions to C record, read into array & processed later
        $extensions_read
            = $this->_parseCextension($input, $offset, $extensions, $c_start);

        // process read extension content
        list($Cpurpose, $Csender_name2, $Creceiver_name2)
            = $this->_processCextension($extensions_read, $Cpurpose, $c_start);

        /* we read the fields, now add an exchange */
        $rc = $this->addExchange(
            array(
                'name' => $Creceiver_name,
                'bank_code' => $Creceiver_blz,
                'account_number' => $Creceiver_account,
                'additional_name' => $Creceiver_name2
            ),
            $Camount/100.0,
            $Cpurpose,
            array(
                'name' => $Csender_name,
                'bank_code' => $Csender_blz,
                'account_number' => $Csender_account,
                'additional_name' => $Csender_name2
            )
        );
        if (!$rc) {
            // should never happen
            throw new Payment_DTA_ParseException('Cannot addExchange() '.
                'for transaction number '.strval($this->count()+1) .
                ' @ offset '. strval($c_start). ', please file a bug report');
        }
        $checks['account'] += $Creceiver_account;
        $checks['blz']     += $Creceiver_blz;
        $checks['amount']  += $Camount;
    }

    /**
    * Auxillary parser to consume E records.
    *
    * @param string  $input   content of DTA file
    * @param integer &$offset read offset into $input
    * @param array   $checks  holds checksums for validation
    *
    * @throws Payment_DTA_Exception on unrecognized input
    * @access private
    * @return void
    */
    private function _parseErecord($input, &$offset, $checks)
    {
        /* field 1+2 */
        $this->checkStr($input, $offset, "0128E");
        /* field 3 */
        $this->checkStr($input, $offset, "     ");
        /* field 4 */
        $E_check_count = $this->getNum($input, $offset, 7);
        /* field 5 */
        $this->checkStr($input, $offset, str_repeat("0", 13));
        /* field 6 */
        $E_check_account = $this->getNum($input, $offset, 17);
        /* field 7 */
        $E_check_blz = $this->getNum($input, $offset, 17);
        /* field 8 */
        $E_check_amount = $this->getNum($input, $offset, 13);
        /* field 9 */
        $this->checkStr($input, $offset, str_repeat(" ", 51));
        // end of E record
        assert($offset % 128 === 0);

        // check checksums

        /*
         * NB: because errors are indicated by exceptions, the user/caller never
         * sees more than one checksum error. Only the first mismatch is reported,
         * the other checks are skipped by throwing the exception.
         */
        if ($E_check_count != $this->count()) {
                    throw new Payment_DTA_ChecksumException(
                        "E record checksum mismatch for transaction count: ".
                        "reads $E_check_count, expected ".$this->count());
        }
        if ($E_check_account != $checks['account']) {
                    throw new Payment_DTA_ChecksumException(
                        "E record checksum mismatch for account numbers: ".
                        "reads $E_check_account, expected ".$checks['account']);
        }
        if ($E_check_blz != $checks['blz']) {
                    throw new Payment_DTA_ChecksumException(
                        "E record checksum mismatch for bank codes: ".
                        "reads $E_check_blz, expected ".$checks['blz']);
        }
        if ($E_check_amount != $checks['amount']) {
                    throw new Payment_DTA_ChecksumException(
                        "E record checksum mismatch for transfer amount: ".
                        "reads $E_check_amount, expected ".$checks['amount']);
        }
    }

    /**
    * Parser. Read data from an existing DTA file content.
    *
    * Parsing can leave us with four situations:
    * - the input is parsed correctly => valid DTA object.
    * - the input is parsed but a checksum does not match the data read
    *       => valid DTA object.
    *       throws a Payment_DTA_ChecksumException.
    * - the n-th transaction cannot be parsed => parsing stops there, yielding
    *       a valid DTA object, but with only the first n-1 transactions
    *       and without checksum verification.
    *       throws a Payment_DTA_ParseException.
    * - a parsing error occurs in the A record => the DTA object is invalid
    *       throws a Payment_DTA_FatalParseException.
    *
    * @param string $input content of DTA file
    *
    * @throws Payment_DTA_Exception on unrecognized input
    * @access protected
    * @return void
    */
    protected function parse($input)
    {
        /*
         * Open Questions/TODOs for the parsing code:
         * - Are the provided exceptions adequate? (Or are they too verbose for
         *   practical use or OTOH not detailed enough to really handle errors?)
         * - Should we try to parse truncated files, i.e. ones with a wrong length?
         * - Should we try to find records with a wrong offset, e.g. when an
         *   encoding error shifts all following records 4 bytes backwards?
         * - Should we abort on any error or rather skip the exchange and continue?
         *   In the later case we need a way to preserve/indicate the problem
         *   because any simple ParseException in a C record will be masked by
         *   a resulting ChecksumException in the E record.
         * - TODO: We should read non-ASCII chars in A/C records. Some programs
         *   write 8-bit chars into the fields.
         */
        if (strlen($input) % 128) {
            throw new Payment_DTA_FatalParseException("invalid length");
        }

        $checks = array(
            'account' => 0,
            'blz' => 0,
            'amount' => 0);
        $offset = 0;

        /* A record */
        try {
            $this->_parseArecord($input, $offset);
        } catch (Payment_DTA_Exception $e) {
            throw new Payment_DTA_FatalParseException("Exception in A record", $e);
        }

        //do not consume input by using getStr()/getNum() here
        while ($input[$offset + 4] == 'C') {
            /* C record */
            $c_start = $offset;
            $c_length = intval(substr($input, $offset, 4));
            try {
                $this->_parseCrecord($input, $offset, $checks);
            } catch (Payment_DTA_Exception $e) {
                // preserve error
                $this->allerrors[] = new Payment_DTA_ParseException(
                    "Error in C record, in transaction number ".
                    strval($this->count()+1) ." @ offset ". strval($c_start), $e);
                // skip to next 128-byte aligned record
                $offset = $c_start + 128 * (1 + intval($c_length/128));
            }
        } // while

        /* E record */
        try {
            $this->_parseErecord($input, $offset, $checks);
        } catch (Payment_DTA_ChecksumException $e) {
            throw $e;
        } catch (Payment_DTA_Exception $e) {
            throw new Payment_DTA_ParseException("Error in E record", $e);
        }
    }
}

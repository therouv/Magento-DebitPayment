<?php
/**
 * DTABase, base class for DTA and DTAZV
 *
 * DTA and DTAVZ provide functions to create DTA/DTAZV files used in
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
 * @version   SVN: $Id: DTABase.php 304207 2010-10-08 15:52:18Z mschuett $
 * @link      http://pear.php.net/package/Payment_DTA
 */

/**
* Payment_DTA_Exception is this packages' basic exception class.
*
* @category Payment
* @package  Payment_DTA
* @author   Martin Schütte <info@mschuette.name>
* @license  http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
* @version  Release: 1.4.2
* @link     http://pear.php.net/package/Payment_DTA
*/
class Payment_DTA_Exception extends Exception
{
}

/**
* Payment_DTA_ParseException indicates parsing problems.
*
* @category Payment
* @package  Payment_DTA
* @author   Martin Schütte <info@mschuette.name>
* @license  http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
* @version  Release: 1.4.2
* @link     http://pear.php.net/package/Payment_DTA
*/
class Payment_DTA_ParseException extends Payment_DTA_Exception
{
}

/**
* Payment_DTA_FatalParseException indicates a non-recoverable parsing problem,
* that makes it impossible to build a usable object.
*
* @category Payment
* @package  Payment_DTA
* @author   Martin Schütte <info@mschuette.name>
* @license  http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
* @version  Release: 1.4.2
* @link     http://pear.php.net/package/Payment_DTA
*/
class Payment_DTA_FatalParseException extends Payment_DTA_ParseException
{
}

/**
* Payment_DTA_ChecksumException indicates a wrong checksum in a DTA file.
*
* @category Payment
* @package  Payment_DTA
* @author   Martin Schütte <info@mschuette.name>
* @license  http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
* @version  Release: 1.4.2
* @link     http://pear.php.net/package/Payment_DTA
*/
class Payment_DTA_ChecksumException extends Payment_DTA_Exception
{
}

/**
* DTABase class provides common functions to classes DTA and DTAZV.
*
* @category Payment
* @package  Payment_DTA
* @author   Hermann Stainer <hs@web-gear.com>
* @license  http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
* @version  Release: 1.4.2
* @link     http://pear.php.net/package/Payment_DTA
*/
abstract class DTABase implements Countable, Iterator
{
    /**
    * Account data for the file sender.
    *
    * @var array $account_file_sender
    * @access protected
    */
    protected $account_file_sender;

    /**
    * Current timestamp.
    *
    * @var integer $timestamp
    * @access protected
    */
    protected $timestamp;

    /**
    * Array of exchanges that the DTA file should contain.
    *
    * @var array $exchanges
    * @access protected
    */
    protected $exchanges;

    /**
    * Sum of amounts in exchanges (in Cents); for control total fields.
    *
    * @var integer $sum_amounts
    * @access protected
    */
    protected $sum_amounts;

    /**
    * Array of all parsing problems.
    *
    * @var array $allerrors
    */
    protected $allerrors;

    /**
    * Return number of exchanges
    *
    * @access public
    * @return integer
    */
    function count()
    {
        return count($this->exchanges);
    }

    /**
    * Constructor.
    */
    function __construct()
    {
        $this->invalidString_regexp = '/[^A-Z0-9 \.,&\-\/\+\*\$%]/';
        $this->account_file_sender  = array();
        $this->exchanges            = array();
        $this->timestamp            = time();
        $this->sum_amounts          = 0;
        $this->allerrors            = array();
    }

    /**
    * Get parsing errors.
    *
    * Returns an array with all exceptions thrown when parsing DTA data;
    * possible elements are:
    * - None: if no errors occured this array is empty,
    * - Payment_DTA_ChecksumException indicates that the complete DTA file
    *   was read into the object but the file's internal checksums were incorrect,
    * - Payment_DTA_ParseException indicates an error in the input, but all
    *   transactions up to the unexpected field were read into the new object,
    * - Payment_DTA_FatalParseException indicates a fatal error, thus the
    *   constructed object is empty.
    *
    * @access public
    * @return array
    */
    function getParsingErrors()
    {
        return $this->allerrors;
    }

    /**
    * Checks if string $input contains the expected value at an offset.
    * After the check the offset is increased.
    *
    * @param string  $input    string to check
    * @param integer &$offset  current offset into input
    * @param string  $expected expected string at the offset
    *
    * @return boolean true if input is as expected, otherwise an exception is thrown
    * @throws Payment_DTA_Exception if input differs from expected string
    * @access protected
    */
    protected function checkStr($input, &$offset, $expected)
    {
        $len   = strlen($expected);
        $found = substr($input, $offset, $len);

        if ($found !== $expected) {
            throw new Payment_DTA_Exception("input string at position $offset ".
                "('$found') does not match expected value '$expected'");
        } else {
            $offset += $len;
            return true;
        }
    }

    /**
    * Read string of given length from input at offset.
    * Afterwards the offset is increased.
    * By default only a subset of ASCII is allowed (as specified by DTA),
    * with $liberal = true apply makeValidString() first in order to accept
    * lower case and some 8-bit chars.
    * (NB: in this case the returned string may be up to twice as long.)
    *
    * @param string  $input   string to check
    * @param integer &$offset current offset into input
    * @param integer $length  chars to read
    * @param bool    $liberal allow 8-bit chars
    *
    * @return string the read string
    * @throws Payment_DTA_Exception if input is too short or contains invalid chars
    * @access protected
    */
    protected function getStr($input, &$offset, $length, $liberal = false)
    {
        $rc = substr($input, $offset, $length);
        if (!$rc) {
            throw new Payment_DTA_Exception("input string not long enough to ".
                "read $length bytes at position $offset");
        }
        if ($liberal) {
            $rc = $this->makeValidString($rc);
        }
        if (!$this->validString($rc)) {
            throw new Payment_DTA_Exception("invalid String '$rc' ".
                "at position $offset");
        } else {
            $offset += $length;
            return $rc;
        }
    }

    /**
    * Read integer number of given length from input at offset.
    * Afterwards the offset is increased.
    *
    * @param string  $input   string to check
    * @param integer &$offset current offset into input
    * @param integer $length  chars to read
    *
    * @return int the read number
    * @throws Payment_DTA_Exception if input is too short or contains invalid chars
    * @access protected
    */
    protected function getNum($input, &$offset, $length)
    {
        $rc = substr($input, $offset, $length);
        if (!$rc) {
            throw new Payment_DTA_Exception("input string not long enough to ".
                "read $length bytes at position $offset");
        } elseif (!ctype_digit($rc)) {
            throw new Payment_DTA_Exception("invalid Number '$rc' ".
                "at position $offset");
        } else {
            $offset += $length;
            return $rc;
        }
    }

    /**
    * Checks if the given string contains only chars valid for fields
    * in DTA files.
    *
    * @param string $string String that is checked.
    *
    * @access public
    * @return boolean
    */
    function validString($string)
    {
        return !preg_match($this->invalidString_regexp, $string);
    }

    /**
    * Makes the given string valid for DTA files.
    * Some diacritics, especially German umlauts become uppercase,
    * all other chars not allowed are replaced with space.
    *
    * @param string $string String that should made valid.
    *
    * @access public
    * @return string
    */
    function makeValidString($string)
    {
        $special_chars = array(
            'á' => 'a',
            'à' => 'a',
            'ä' => 'ae',
            'â' => 'a',
            'ã' => 'a',
            'å' => 'a',
            'æ' => 'ae',
            'ā' => 'a',
            'ă' => 'a',
            'ą' => 'a',
            'ȁ' => 'a',
            'ȃ' => 'a',
            'Á' => 'A',
            'À' => 'A',
            'Ä' => 'Ae',
            'Â' => 'A',
            'Ã' => 'A',
            'Å' => 'A',
            'Æ' => 'AE',
            'Ā' => 'A',
            'Ă' => 'A',
            'Ą' => 'A',
            'Ȁ' => 'A',
            'Ȃ' => 'A',
            'ç' => 'c',
            'ć' => 'c',
            'ĉ' => 'c',
            'ċ' => 'c',
            'č' => 'c',
            'Ç' => 'C',
            'Ć' => 'C',
            'Ĉ' => 'C',
            'Ċ' => 'C',
            'Č' => 'C',
            'ď' => 'd',
            'đ' => 'd',
            'Ď' => 'D',
            'Đ' => 'D',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ē' => 'e',
            'ĕ' => 'e',
            'ė' => 'e',
            'ę' => 'e',
            'ě' => 'e',
            'ȅ' => 'e',
            'ȇ' => 'e',
            'É' => 'E',
            'È' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ē' => 'E',
            'Ĕ' => 'E',
            'Ė' => 'E',
            'Ę' => 'E',
            'Ě' => 'E',
            'Ȅ' => 'E',
            'Ȇ' => 'E',
            'ĝ' => 'g',
            'ğ' => 'g',
            'ġ' => 'g',
            'ģ' => 'g',
            'Ĝ' => 'G',
            'Ğ' => 'G',
            'Ġ' => 'G',
            'Ģ' => 'G',
            'ĥ' => 'h',
            'ħ' => 'h',
            'Ĥ' => 'H',
            'Ħ' => 'H',
            'ì' => 'i',
            'ì' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ĩ' => 'i',
            'ī' => 'i',
            'ĭ' => 'i',
            'į' => 'i',
            'ı' => 'i',
            'ĳ' => 'ij',
            'ȉ' => 'i',
            'ȋ' => 'i',
            'Í' => 'I',
            'Ì' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ĩ' => 'I',
            'Ī' => 'I',
            'Ĭ' => 'I',
            'Į' => 'I',
            'İ' => 'I',
            'Ĳ' => 'IJ',
            'Ȉ' => 'I',
            'Ȋ' => 'I',
            'ĵ' => 'j',
            'Ĵ' => 'J',
            'ķ' => 'k',
            'Ķ' => 'K',
            'ĺ' => 'l',
            'ļ' => 'l',
            'ľ' => 'l',
            'ŀ' => 'l',
            'ł' => 'l',
            'Ĺ' => 'L',
            'Ļ' => 'L',
            'Ľ' => 'L',
            'Ŀ' => 'L',
            'Ł' => 'L',
            'ñ' => 'n',
            'ń' => 'n',
            'ņ' => 'n',
            'ň' => 'n',
            'ŉ' => 'n',
            'Ñ' => 'N',
            'Ń' => 'N',
            'Ņ' => 'N',
            'Ň' => 'N',
            'ó' => 'o',
            'ò' => 'o',
            'ö' => 'oe',
            'ô' => 'o',
            'õ' => 'o',
            'ø' => 'o',
            'ō' => 'o',
            'ŏ' => 'o',
            'ő' => 'o',
            'œ' => 'oe',
            'ȍ' => 'o',
            'ȏ' => 'o',
            'Ó' => 'O',
            'Ò' => 'O',
            'Ö' => 'Oe',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ø' => 'O',
            'Ō' => 'O',
            'Ŏ' => 'O',
            'Ő' => 'O',
            'Œ' => 'OE',
            'Ȍ' => 'O',
            'Ȏ' => 'O',
            'ŕ' => 'r',
            'ř' => 'r',
            'ȑ' => 'r',
            'ȓ' => 'r',
            'Ŕ' => 'R',
            'Ř' => 'R',
            'Ȑ' => 'R',
            'Ȓ' => 'R',
            'ß' => 'ss',
            'ś' => 's',
            'ŝ' => 's',
            'ş' => 's',
            'š' => 's',
            'ș' => 's',
            'Ś' => 'S',
            'Ŝ' => 'S',
            'Ş' => 'S',
            'Š' => 'S',
            'Ș' => 'S',
            'ţ' => 't',
            'ť' => 't',
            'ŧ' => 't',
            'ț' => 't',
            'Ţ' => 'T',
            'Ť' => 'T',
            'Ŧ' => 'T',
            'Ț' => 'T',
            'ú' => 'u',
            'ù' => 'u',
            'ü' => 'ue',
            'û' => 'u',
            'ũ' => 'u',
            'ū' => 'u',
            'ŭ' => 'u',
            'ů' => 'u',
            'ű' => 'u',
            'ų' => 'u',
            'ȕ' => 'u',
            'ȗ' => 'u',
            'Ú' => 'U',
            'Ù' => 'U',
            'Ü' => 'Ue',
            'Û' => 'U',
            'Ũ' => 'U',
            'Ū' => 'U',
            'Ŭ' => 'U',
            'Ů' => 'U',
            'Ű' => 'U',
            'Ų' => 'U',
            'Ȕ' => 'U',
            'Ȗ' => 'U',
            'ŵ' => 'w',
            'Ŵ' => 'W',
            'ý' => 'y',
            'ÿ' => 'y',
            'ŷ' => 'y',
            'Ý' => 'Y',
            'Ÿ' => 'Y',
            'Ŷ' => 'Y',
            'ź' => 'z',
            'ż' => 'z',
            'ž' => 'z',
            'Ź' => 'Z',
            'Ż' => 'Z',
            'Ž' => 'Z',
        );

        if (strlen($string) == 0) {
            return "";
        }

        // ensure UTF-8, for single-byte-encodings use either
        //     the internal encoding or assume ISO-8859-1
        $utf8string = mb_convert_encoding(
            $string,
            "UTF-8",
            array("UTF-8", mb_internal_encoding(), "ISO-8859-1")
        );

        // replace known special chars
        $result = strtr($utf8string, $special_chars);
        // upper case
        $result = strtoupper($result);
        // make sure every special char is replaced by one space, not two or three
        $result = mb_convert_encoding($result, "ASCII", "UTF-8");
        $result = preg_replace($this->invalidString_regexp, ' ', $result);

        return $result;
    }

    /**
     * Auxillary method to filter output strings.
     *
     * @param string $str Text to filter
     * @param int    $len Length of text field
     *
     * @access private
     * @return string
     */
    protected function filter($str, $len)
    {
        return substr($this->makeValidString($str), 0, $len);
    }

    /**
    * Writes the DTA file.
    *
    * @param string $filename Filename.
    *
    * @access public
    * @return boolean
    */
    function saveFile($filename)
    {
        $content = $this->getFileContent();

        $Dta_fp = @fopen($filename, "w");
        if (!$Dta_fp) {
            $result = false;
        } else {
            $result = @fwrite($Dta_fp, $content);
            @fclose($Dta_fp);
        }

        return $result;
    }

    /**
    * Returns an array with information about the transactions.
    * Can be used to print an accompanying document (Begleitzettel) for disks.
    *
    * @access public
    * @return array Returns an array with keys: "sender_name",
    *   "sender_bank_code", "sender_account", "sum_amounts",
    *   "count", "date"
    */
    function getMetaData()
    {
        return array(
            "sender_name"      => strval($this->account_file_sender['name']),
            "sender_bank_code" => intval($this->account_file_sender['bank_code']),
            "sender_account"   => floatval($this->account_file_sender['account_number']),
            "sum_amounts"      => floatval($this->sum_amounts / 100.0),
            "count"            => intval($this->count()),
            "date"             => $this->timestamp,
        );
    }

    /**
    * Set the sender of the file.
    * The given account data is also used as default sender's account.
    *
    * Account data contains
    *  name            Sender's name.
    *  additional_name Sender's additional name.
    *  bank_code       Sender's bank code.
    *  account_number  Sender's account number.
    *
    * @param array $account Account data for file sender.
    *
    * @access public
    * @return boolean
    */
    abstract function setAccountFileSender($account);

    /**
    * Adds an exchange. First the account data for the receiver of the exchange is
    * set. In the case the DTA file contains credits, this is the payment receiver.
    * If the sender is not specified, values of the file sender are used by default.
    *
    * Account data for receiver and sender contain the following fields:
    *  name            Name.
    *  bank_code       Bank code.
    *  account_number  Account number.
    *  additional_name If necessary, additional line for name.
    *
    * @param array  $account_receiver Receiver's account data.
    * @param double $amount           Amount of money in this exchange.
    *                                 Currency: EURO
    * @param array  $purposes         An Array of lines or a string for
    *                                 description of the exchange.
    * @param array  $account_sender   Sender's account data.
    *
    * @access public
    * @return boolean
    */
    abstract function addExchange(
        $account_receiver,
        $amount,
        $purposes,
        $account_sender = array()
    );

    /**
    * Returns the full content of the generated file.
    * All added exchanges are processed.
    *
    * @access public
    * @return string
    */
    abstract function getFileContent();

    /* variable and methods to implement Iterator interface */
    protected $iterator_position = 0;
    function current()
    {
        return $this->exchanges[$this->iterator_position];
    }
    function key()
    {
        return $this->iterator_position;
    }
    function next()
    {
        ++$this->iterator_position;
    }
    function rewind()
    {
        $this->iterator_position = 0;
    }
    function valid()
    {
        return isset($this->exchanges[$this->iterator_position]);
    }
    
}
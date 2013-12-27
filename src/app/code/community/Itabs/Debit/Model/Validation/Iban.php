<?php

class Itabs_Debit_Model_Validation_Iban
{
    /**
     * @var array Error message container
     */
    protected $_errors = array();

    /**
     * @var array[] IBAN validation rules
     */
    protected $_rules = array(
        'AD' => array(
            'length' => 24,
            'schema' => '/^AD[0-9]{2}[0-9]{8}[A-Z0-9]{12}$/'
        ),
        'AE' => array(
            'length' => 23,
            'schema' => '/^AE[0-9]{2}[0-9]{3}[0-9]{16}$/'
        ),
        'AL' => array(
            'length' => 28,
            'schema' => '/^AL[0-9]{2}[0-9]{8}[A-Z0-9]{16}$/'
        ),
        'AT' => array(
            'length' => 20,
            'schema' => '/^AT[0-9]{2}[0-9]{5}[0-9]{11}$/'
        ),
        'BA' => array(
            'length' => 20,
            'schema' => '/^BA[0-9]{2}[0-9]{6}[0-9]{10}$/'
        ),
        'BE' => array(
            'length' => 16,
            'schema' => '/^BE[0-9]{2}[0-9]{3}[0-9]{9}$/'
        ),
        'BG' => array(
            'length' => 22,
            'schema' => '/^BG[0-9]{2}[A-Z]{4}[0-9]{4}[0-9]{2}[A-Z0-9]{8}$/'
        ),
        'CH' => array(
            'length' => 21,
            'schema' => '/^CH[0-9]{2}[0-9]{5}[A-Z0-9]{12}$/'
        ),
        'CY' => array(
            'length' => 28,
            'schema' => '/^CY[0-9]{2}[0-9]{8}[A-Z0-9]{16}$/'
        ),
        'CZ' => array(
            'length' => 24,
            'schema' => '/^CZ[0-9]{2}[0-9]{4}[0-9]{16}$/'
        ),
        'DE' => array(
            'length' => 22,
            'schema' => '/^DE[0-9]{2}[0-9]{8}[0-9]{10}$/'
        ),
        'DK' => array(
            'length' => 18,
            'schema' => '/^DK[0-9]{2}[0-9]{4}[0-9]{10}$/'
        ),
        'EE' => array(
            'length' => 20,
            'schema' => '/^EE[0-9]{2}[0-9]{4}[0-9]{12}$/'
        ),
        'ES' => array(
            'length' => 24,
            'schema' => '/^ES[0-9]{2}[0-9]{8}[0-9]{12}$/'
        ),
        'FR' => array(
            'length' => 27,
            'schema' => '/^FR[0-9]{2}[0-9]{10}[A-Z0-9]{13}$/'
        ),
        'FI' => array(
            'length' => 18,
            'schema' => '/^FI[0-9]{2}[0-9]{6}[0-9]{8}$/'
        ),
        'GB' => array(
            'length' => 22,
            'schema' => '/^GB[0-9]{2}[A-Z]{4}[0-9]{14}$/'
        ),
        'GE' => array(
            'length' => 22,
            'schema' => '/^GE[0-9]{2}[A-Z]{2}[0-9]{16}$/'
        ),
        'GI' => array(
            'length' => 23,
            'schema' => '/^GI[0-9]{2}[A-Z]{4}[A-Z0-9]{15}$/'
        ),
        'GR' => array(
            'length' => 27,
            'schema' => '/^GR[0-9]{2}[0-9]{7}[A-Z0-9]{16}$/'
        ),
        'HR' => array(
            'length' => 21,
            'schema' => '/^HR[0-9]{2}[0-9]{7}[0-9]{10}$/'
        ),
        'HU' => array(
            'length' => 28,
            'schema' => '/^HU[0-9]{2}[0-9]{7}[0-9]{1}[0-9]{15}[0-9]{1}$/'
        ),
        'IE' => array(
            'length' => 22,
            'schema' => '/^IE[0-9]{2}[A-Z0-9]{4}[0-9]{6}[0-9]{8}$/'
        ),
        'IL' => array(
            'length' => 23,
            'schema' => '/^IL[0-9]{2}[0-9]{6}[0-9]{13}$/'
        ),
        'IS' => array(
            'length' => 26,
            'schema' => '/^IS[0-9]{2}[0-9]{4}[0-9]{18}$/'
        ),
        'IT' => array(
            'length' => 27,
            'schema' => '/^IT[0-9]{2}[A-Z]{1}[0-9]{10}[A-Z0-9]{12}$/'
        ),
        'KW' => array(
            'length' => 30,
            'schema' => '/^KW[0-9]{2}[A-Z]{4}[A-Z0-9]{22}$/'
        ),
        'LB' => array(
            'length' => 28,
            'schema' => '/^LB[0-9]{2}[0-9]{4}[A-Z0-9]{20}$/'
        ),
        'LI' => array(
            'length' => 21,
            'schema' => '/^LI[0-9]{2}[0-9]{5}[A-Z0-9]{12}$/'
        ),
        'LT' => array(
            'length' => 20,
            'schema' => '/^LT[0-9]{2}[0-9]{5}[0-9]{11}$/'
        ),
        'LU' => array(
            'length' => 20,
            'schema' => '/^LU[0-9]{2}[0-9]{3}[A-Z0-9]{13}$/'
        ),
        'LV' => array(
            'length' => 21,
            'schema' => '/^LV[0-9]{2}[A-Z]{4}[A-Z0-9]{13}$/'
        ),
        'MC' => array(
            'length' => 27,
            'schema' => '/^MC[0-9]{2}[0-9]{10}[A-Z0-9]{11}[0-9]{2}$/'
        ),
        'ME' => array(
            'length' => 22,
            'schema' => '/^ME[0-9]{2}[0-9]{3}[0-9]{15}$/'
        ),
        'MK' => array(
            'length' => 19,
            'schema' => '/^MK[0-9]{2}[0-9]{3}[A-Z0-9]{10}[0-9]{2}$/'
        ),
        'MR' => array(
            'length' => 27,
            'schema' => '/^MR[0-9]{2}[0-9]{10}[0-9]{13}$/'
        ),
        'MT' => array(
            'length' => 31,
            'schema' => '/^MT[0-9]{2}[A-Z]{4}[0-9]{5}[A-Z0-9]{18}$/'
        ),
        'MU' => array(
            'length' => 30,
            'schema' => '/^MU[0-9]{2}[A-Z]{4}[0-9]{4}[0-9]{15}[A-Z]{3}$/'
        ),
        'NL' => array(
            'length' => 18,
            'schema' => '/^NL[0-9]{2}[A-Z]{4}[0-9]{10}$/'
        ),
        'NO' => array(
            'length' => 15,
            'schema' => '/^NO[0-9]{2}[0-9]{4}[0-9]{7}$/'
        ),
        'PL' => array(
            'length' => 28,
            'schema' => '/^PL[0-9]{2}[0-9]{8}[0-9]{16}$/'
        ),
        'PT' => array(
            'length' => 25,
            'schema' => '/^PT[0-9]{2}[0-9]{8}[0-9]{13}$/'
        ),
        'RO' => array(
            'length' => 24,
            'schema' => '/^RO[0-9]{2}[A-Z]{4}[A-Z0-9]{16}$/'
        ),
        'RS' => array(
            'length' => 22,
            'schema' => '/^RS[0-9]{2}[0-9]{3}[0-9]{15}$/'
        ),
        'SA' => array(
            'length' => 24,
            'schema' => '/^SA[0-9]{2}[0-9]{2}[A-Z0-9]{18}$/'
        ),
        'SE' => array(
            'length' => 24,
            'schema' => '/^SE[0-9]{2}[0-9]{3}[0-9]{17}$/'
        ),
        'SI' => array(
            'length' => 19,
            'schema' => '/^SI[0-9]{2}[0-9]{5}[0-9]{8}[0-9]{2}$/'
        ),
        'SK' => array(
            'length' => 24,
            'schema' => '/^SK[0-9]{2}[0-9]{4}[0-9]{16}$/'
        ),
        'SM' => array(
            'length' => 27,
            'schema' => '/^SM[0-9]{2}[A-Z]{1}[0-9]{10}[A-Z0-9]{12}$/'
        ),
        'TN' => array(
            'length' => 24,
            'schema' => '/^TN[0-9]{2}[0-9]{5}[0-9]{15}$/'
        ),
        'TR' => array(
            'length' => 26,
            'schema' => '/^TR[0-9]{2}[0-9]{5}[A-Z0-9]{17}$/'
        )
    );

    /**
     * Check if the given iban is a valid iban
     *
     * @param  string $iban
     * @return bool
     */
    public function validate($iban)
    {
        $iban = strtoupper($iban);
        $iban = str_replace(' ', '', $iban);
        $country = substr($iban, 0, 2);

        /*
         * Validate the country
         */
        if (!array_key_exists($country, $this->_rules)
            || !isset($this->_rules[$country]['length'])
            || !isset($this->_rules[$country]['schema'])
        ) {
            $this->addError('No valid country given.');
            return false;
        }

        /*
         * Validate the length of the IBAN
         */
        if (strlen($iban) != $this->_rules[$country]['length']) {
            $this->addError('Length for the IBAN does not match.');
            return false;
        }


        /*
         * Validate the schema of the IBAN
         */
        if (!preg_match($this->_rules[$country]['schema'], $iban)) {
            $this->addError('The schema of the IBAN does not match.');
            return false;
        }

        /*
         * Validate the checksum of the IBAN
         */

        // Move first 4 chars (country code and checksum) to the end of the file
        $tmp = substr($iban, 4) . substr($iban, 0, 4);

        // Replace all chars with the respective numeric value
        $searchValues = range('A', 'Z');
        $replaceValues = array();
        foreach (range(10, 35) as $value) {
            $replaceValues[] = strval($value);
        }
        $tmp = str_replace($searchValues, $replaceValues, $tmp);

        // Modulo the iban with 97 and calculate the remainder
        $remainder = 0;
        for ($pos=0; $pos < strlen($tmp); $pos += 7) {
            $part = strval($remainder) . substr($tmp, $pos, 7);
            $remainder = intval($part) % 97;
        }

        // Remainder must equal '1' to be considered as valid
        if ($remainder != 1) {
            $this->addError('The checksum of the IBAN is not valid.');
            return false;
        }

        return true;
    }

    /**
     * Retrieve all errors of the validation as array
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @param  string $error
     * @return Itabs_Debit_Model_Validation_Iban
     */
    public function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Retrieve all errors of the validation as string
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $errors = $this->getErrors();

        $message = sprintf("There were %s validation errors of the given IBAN:", count($errors));
        foreach ($this->getErrors() as $error) {
            $message .= "\n-- " . $error . "\n";
        }

        return $message;
    }
}

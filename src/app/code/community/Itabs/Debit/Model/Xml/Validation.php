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
 * Class Itabs_Debit_Model_Xml_Validation
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Model_Xml_Validation
{
    /**
     * @var string
     */
    protected $schema = 'pain.008.002.02.xsd';

    /**
     * @var string|null
     */
    protected $xml = null;

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * Validate the xml file
     *
     * @return bool
     */
    public function validate()
    {
        // Check if a valid xml is given.
        if (null === $this->xml) {
            $this->addError('No valid xml given.');
            return false;
        }

        // Check if the validation file exists
        $dataDir = Mage::getConfig()->getModuleDir('etc', 'Itabs_Debit') . DS . 'data' . DS;
        $filePath = $dataDir . $this->getSchema();
        if (!file_exists($filePath)) {
            $this->addError('XSD for validation does not exist.');
            return false;
        }

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        // Check if the xml document is well formed
        $result = $dom->loadXML($this->getXml());
        if ($result === false) {
            $this->addError('Document is not well formed.');
            return false;
        }

        // Validate the xml against the schema
        if (!$dom->schemaValidate($filePath)) {
            $documentErrors = "Document is not valid:\n";

            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                /* @var $error LibXMLError */

                $documentErrors .= "---\n" . sprintf("file: %s, line: %s, column: %s, level: %s, code: %s\nError: %s",
                    basename($error->file),
                    $error->line,
                    $error->column,
                    $error->level,
                    $error->code,
                    $error->message
                );
            }

            $this->addError($documentErrors);
            return false;
        }

        return true;
    }

    /**
     * Set the sepa xml string
     *
     * @param  string $xml
     * @return Itabs_Debit_Model_Xml_Validation Self.
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
        return $this;
    }

    /**
     * Get the sepa xml string
     *
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Add an error message
     *
     * @param string $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Get the xml erros
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set the schema name
     *
     * @param  string $schema Schema
     * @return Itabs_Debit_Model_Xml_Validation
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * Retrieve the schema name
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }
}

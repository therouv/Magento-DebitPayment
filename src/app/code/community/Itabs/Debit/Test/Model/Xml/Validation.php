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
 * Itabs_Debit_Model_Xml_Validation Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Model_Xml_Validation extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Debit_Model_Xml_Validation
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = new Itabs_Debit_Model_Xml_Validation();
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itabs_Debit_Model_Xml_Validation', $this->_model);
    }

    /**
     * @test
     */
    public function testValidateNoXml()
    {
        $model = new Itabs_Debit_Model_Xml_Validation();
        $this->assertFalse($model->validate());
        $this->assertContains('No valid xml given.', $model->getErrors());
    }

    /**
     * @test
     */
    public function testValidateSchemaNotExists()
    {
        $model = new Itabs_Debit_Model_Xml_Validation();
        $model->setXml('<test/>');
        $model->setSchema(__FUNCTION__.'.xsd');
        $this->assertFalse($model->validate());
        $this->assertContains('XSD for validation does not exist.', $model->getErrors());
    }

    /**
     * @test
     */
    public function testValidateDocumentNotWellFormed()
    {
        $model = new Itabs_Debit_Model_Xml_Validation();
        $model->setXml('<test/');
        $this->assertFalse($model->validate());
        $this->assertContains('Document is not well formed.', $model->getErrors());
    }

    /**
     * @test
     */
    public function testValidateSchemaNotValid()
    {
        $dir = Mage::getConfig()->getModuleDir(false, 'Itabs_Debit') . DS . 'Test' . DS . 'fixtures' . DS;
        $xml = file_get_contents($dir . 'test.xml');
        $xml = str_replace('OOFF', 'ABCD', $xml);

        $model = new Itabs_Debit_Model_Xml_Validation();
        $model->setXml($xml);
        $this->assertFalse($model->validate());

        $errors = $model->getErrors();
        $errors = $errors[0];
        $this->assertContains('Document is not valid', $errors);
    }

    /**
     * @test
     */
    public function testValidate()
    {
        $dir = Mage::getConfig()->getModuleDir(false, 'Itabs_Debit') . DS . 'Test' . DS . 'fixtures' . DS;
        $xml = file_get_contents($dir . 'test.xml');

        $model = new Itabs_Debit_Model_Xml_Validation();
        $model->setXml($xml);
        $this->assertTrue($model->validate());
    }
}

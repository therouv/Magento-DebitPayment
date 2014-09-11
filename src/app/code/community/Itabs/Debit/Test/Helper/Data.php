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
 * Helper/Data.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Debit_Helper_Data
     */
    protected $_helper;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_helper = Mage::helper('debit');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itabs_Debit_Helper_Data', $this->_helper);
    }

    /**
     * @test
     */
    public function testGetBankByIdentifier()
    {
        $this->assertEquals('Kreissparkasse Esslingen-Nürtingen', $this->_helper->getBankByIdentifier('routing', '61150020'));
        $this->assertNull($this->_helper->getBankByIdentifier('routing', '99999999'));
        $this->assertEquals('Kreissparkasse Esslingen-Nürtingen', $this->_helper->getBankByIdentifier('swift', 'ESSLDE66XXX'));
        $this->assertNull($this->_helper->getBankByIdentifier('swift', 'XXXXXXXXXX'));
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testSanitizeData($data)
    {
        // Load all expectations
        $dataSet = $this->readAttribute($this, 'dataName');

        foreach ($data as $key => $value) {
            $this->assertEquals(
                $this->expected($dataSet)->getData($key),
                $this->_helper->sanitizeData($value)
            );
        }
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function testNormalizeString($data)
    {
        // Load all expectations
        $dataSet = $this->readAttribute($this, 'dataName');

        foreach ($data as $key => $value) {
            $this->assertEquals(
                $this->expected($dataSet)->getData($key),
                $this->_helper->normalizeString($value)
            );
        }
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCreditorIdentificationNumber()
    {
        $this->assertEquals('DE98ZZZ09999999999', $this->_helper->getCreditorIdentificationNumber());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function getHintForIbanField()
    {
        $this->assertEquals('Lorem Ipsum Iban', $this->_helper->getHintForIbanField());
    }

    /**
     * @test
     * @loadFixture emptyHintFields
     */
    public function getHintForIbanFieldEmpty()
    {
        $this->assertFalse($this->_helper->getHintForIbanField());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function getHintForBicField()
    {
        $this->assertEquals('Lorem Ipsum Bic', $this->_helper->getHintForBicField());
    }

    /**
     * @test
     * @loadFixture emptyHintFields
     */
    public function getHintForBicFieldEmpty()
    {
        $this->assertFalse($this->_helper->getHintForBicField());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetOffset()
    {
        $this->assertEquals(4, $this->_helper->getOffset());
    }

    /**
     * @test
     * @loadFixture testGetOffsetSmallerThanTwo
     */
    public function testGetOffsetSmallerThanTwo()
    {
        $this->assertEquals(2, $this->_helper->getOffset());
    }
}

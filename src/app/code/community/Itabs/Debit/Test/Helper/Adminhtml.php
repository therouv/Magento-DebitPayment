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
 * Helper/Adminhtml.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Helper_Adminhtml extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @var Itabs_Debit_Helper_Adminhtml
     */
    protected $_helper;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_helper = Mage::helper('debit/adminhtml');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itabs_Debit_Helper_Adminhtml', $this->_helper);
    }

    /**
     * @test
     */
    public function hasNotExportRequirements()
    {
        $this->assertFalse($this->_helper->hasExportRequirements());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function hasExportRequirements()
    {
        $this->assertTrue($this->_helper->hasExportRequirements());
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectations
     */
    public function testGetBankAccount()
    {
        $this->assertEquals($this->expected('bankaccount')->getResult(), $this->_helper->getBankAccount());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectations
     */
    public function testGetSyncedOrders()
    {
        $this->assertEquals(
            $this->expected('orders')->getResult(),
            $this->_helper->getSyncedOrders()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testSetStatusAsExported()
    {
        $result = $this->_helper->setStatusAsExported(1);

        $this->assertTrue($result);
        $model = Mage::getModel('debit/orders')->load(1);
        $this->assertEquals(1, $model->getData('status'));
    }

    /**
     * @test
     * @loadFixture testGetBookingText
     */
    public function testGetBookingText()
    {
        $this->assertEquals('Bestellung 999999999', $this->_helper->getBookingText(0, '999999999'));
    }

    /**
     * @test
     * @loadFixture getCountryOptions
     * @loadExpectations
     */
    public function testGetCountryOptionsHash()
    {
        $this->assertEquals(
            $this->expected('countryoptions')->getResult(),
            $this->_helper->getCountryOptionsHash()
        );
    }

    /**
     * @test
     * @loadFixture getCountryOptions
     */
    public function testGetCountryOptions()
    {
        $this->assertEquals(
            $this->expected('countryoptions')->getResult(),
            $this->_helper->getCountryOptions()
        );
    }

    /**
     * @test
     * @loadFixture getCountryOptions
     */
    public function testGetCountryCollection()
    {
        $collection = $this->_helper->getCountryCollection();
        $this->assertInstanceOf('Mage_Directory_Model_Resource_Country_Collection', $collection);
        $this->assertEquals(2, $collection->count());
    }
}

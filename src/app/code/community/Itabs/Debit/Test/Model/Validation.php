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
 * Model/Validation.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Model_Validation extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @var Itabs_Debit_Model_Validation
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('debit/validation');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itabs_Debit_Model_Validation', $this->_model);
    }

    /**
     * @test
     */
    public function testHasSpecificCustomerGroup()
    {
        $this->assertTrue($this->_model->hasSpecificCustomerGroup());
    }

    /**
     * @test
     * @loadFixture testHasSpecificCustomerGroupFalse
     */
    public function testHasSpecificCustomerGroupFalse()
    {
        $this->assertFalse($this->_model->hasSpecificCustomerGroup());
    }

    /**
     * @test
     * @loadFixture testHasSpecificCustomerGroupTrue
     */
    public function testHasSpecificCustomerGroupTrue()
    {
        $this->assertTrue($this->_model->hasSpecificCustomerGroup());
    }

    /**
     * @test
     */
    public function testHasMinimumOrderCount()
    {
        $this->assertTrue($this->_model->hasMinimumOrderCount());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testHasMinimumOrderCountFalse
     */
    public function testHasMinimumOrderCountNoCustomerFalse()
    {
        $this->assertFalse($this->_model->hasMinimumOrderCount());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testHasMinimumOrderCountFalse
     */
    public function testHasMinimumOrderCountNoOrdersFalse()
    {
        $this->setCurrentStore(1);
        $sessionMock = $this->getModelMock('customer/session', array('renewSession'));
        $sessionMock->loginById(1);
        $this->replaceByMock('singleton', 'customer/session', $sessionMock);
        $this->assertFalse($this->_model->hasMinimumOrderCount());
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testHasMinimumOrderCountTrue
     */
    public function testHasMinimumOrderCountTrue()
    {
        $this->setCurrentStore(1);
        $sessionMock = $this->getModelMock('customer/session', array('renewSession'));
        $sessionMock->loginById(1);
        $this->replaceByMock('singleton', 'customer/session', $sessionMock);
        $this->assertTrue($this->_model->hasMinimumOrderCount());
        $this->reset();
    }

    /**
     * @test
     */
    public function testHasMinimumOrderAmount()
    {
        $this->assertTrue($this->_model->hasMinimumOrderAmount());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture hasMinimumOrderAmountFalse
     */
    public function testHasMinimumOrderAmountNoCustomerFalse()
    {
        $this->assertFalse($this->_model->hasMinimumOrderAmount());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture hasMinimumOrderAmountFalse
     */
    public function testHasMinimumOrderAmountNoAmountFalse()
    {
        $this->setCurrentStore(1);
        $sessionMock = $this->getModelMock('customer/session', array('renewSession'));
        $sessionMock->loginById(1);
        $this->replaceByMock('singleton', 'customer/session', $sessionMock);
        $this->assertFalse($this->_model->hasMinimumOrderAmount());
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture hasMinimumOrderAmountTrue
     */
    public function testHasMinimumOrderAmountTrue()
    {
        $this->setCurrentStore(1);
        $sessionMock = $this->getModelMock('customer/session', array('renewSession'));
        $sessionMock->loginById(1);
        $this->replaceByMock('singleton', 'customer/session', $sessionMock);
        $this->assertTrue($this->_model->hasMinimumOrderAmount());
        $this->reset();
    }

    /**
     * @test
     */
    public function testGetCustomerGroupId()
    {
        $this->assertEquals(
            Mage_Customer_Model_Group::NOT_LOGGED_IN_ID,
            EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getCustomerGroupId')
        );
    }

    /**
     * @test
     */
    public function testGetSessionBackend()
    {
        $this->setCurrentStore(0);
        $this->assertInstanceOf(
            'Mage_Adminhtml_Model_Session_Quote',
            EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getSession')
        );
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCustomerBackend()
    {
        $this->setCurrentStore(0);
        $customer = Mage::getModel('customer/customer')->load(1);
        $sessionMock = $this->getModelMock('adminhtml/session_quote', array('renewSession'));
        $sessionMock->setCustomer($customer);
        $this->replaceByMock('singleton', 'adminhtml/session_quote', $sessionMock);
        $this->assertInstanceOf(
            'Mage_Customer_Model_Customer',
            EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getCustomer')
        );
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCustomerGroupIdBackend()
    {
        $this->setCurrentStore(0);
        $customer = Mage::getModel('customer/customer')->load(1);
        $sessionMock = $this->getModelMock('adminhtml/session_quote', array('renewSession'));
        $sessionMock->setStoreId(1);
        $sessionMock->setQuoteId(1);
        $sessionMock->setCustomerId(1);
        $sessionMock->setCustomer($customer);
        $this->replaceByMock('singleton', 'adminhtml/session_quote', $sessionMock);
        $this->assertEquals(1, EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getCustomerGroupId'));
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCustomerEmailBackend()
    {
        $this->setCurrentStore(0);
        $customer = Mage::getModel('customer/customer')->load(1);
        $sessionMock = $this->getModelMock('adminhtml/session_quote', array('renewSession'));
        $sessionMock->setStoreId(1);
        $sessionMock->setQuoteId(1);
        $sessionMock->setCustomerId(1);
        $sessionMock->setCustomer($customer);
        $this->replaceByMock('singleton', 'adminhtml/session_quote', $sessionMock);
        $this->assertEquals('test@example.org', EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getCustomerEmail'));
        $this->reset();
    }

    /**
     * @test
     */
    public function testGetSessionFrontend()
    {
        $this->setCurrentStore(1);
        $this->assertInstanceOf(
            'Mage_Customer_Model_Session',
            EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getSession')
        );
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCustomerFrontend()
    {
        $this->setCurrentStore(1);
        $sessionMock = $this->getModelMock('customer/session', array('renewSession'));
        $sessionMock->loginById(1);
        $this->replaceByMock('singleton', 'customer/session', $sessionMock);
        $this->assertInstanceOf(
            'Mage_Customer_Model_Customer',
            EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getCustomer')
        );
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCustomerGroupIdFrontend()
    {
        $this->setCurrentStore(1);
        $sessionMock = $this->getModelMock('customer/session', array('renewSession'));
        $sessionMock->loginById(1);
        $this->replaceByMock('singleton', 'customer/session', $sessionMock);
        $this->assertEquals(1, EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getCustomerGroupId'));
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCustomerEmailFrontend()
    {
        $this->setCurrentStore(1);
        $sessionMock = $this->getModelMock('customer/session', array('renewSession'));
        $sessionMock->loginById(1);
        $this->replaceByMock('singleton', 'customer/session', $sessionMock);
        $this->assertEquals('test@example.org', EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getCustomerEmail'));
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCustomerEmailFrontendNotLoggedIn()
    {
        $this->setCurrentStore(1);
        $sessionMock = $this->getModelMock('checkout/session', array('renewSession'));
        $sessionMock->setQuoteId(1);
        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);
        $this->assertEquals('test@example.org', EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getCustomerEmail'));
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCustomerOrders()
    {
        $collection = EcomDev_Utils_Reflection::invokeRestrictedMethod($this->_model, '_getCustomerOrders', array('customerId' => 1));
        $this->assertInstanceOf('Mage_Sales_Model_Resource_Order_Collection', $collection);
        $this->assertEquals(2, $collection->count());
    }
}

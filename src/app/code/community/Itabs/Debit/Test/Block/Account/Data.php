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
 * Block/Form.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Block_Account_Data extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @var Itabs_Debit_Block_Form
     */
    protected $_block;

    /**
     * Instantiate the object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_block = self::app()->getLayout()->createBlock('debit/account_data');
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function testGetBankName()
    {
        // Mock customer session; it's necessary because attribute value is encrypted
        $customerMock = $this->getModelMock('customer/customer', array());
        $customerMock->expects($this->any())
            ->method('getData')
            ->with($this->equalTo('debit_payment_acount_blz'))
            ->will($this->returnValue('61150020'));
        $this->replaceByMock('model', 'customer/customer', $customerMock);
        Mage::getSingleton('customer/session')->setCustomer($customerMock);

        $this->assertEquals('Kr Spk Esslingen-Nürtingen', $this->_block->getBankName());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function testGetBankNameUnavailable()
    {
        // Mock customer session; it's necessary because attribute value is encrypted
        $customerMock = $this->getModelMock('customer/customer', array());
        $customerMock->expects($this->any())
            ->method('getData')
            ->with($this->equalTo('debit_payment_acount_blz'))
            ->will($this->returnValue('99999999'));
        $this->replaceByMock('model', 'customer/customer', $customerMock);
        Mage::getSingleton('customer/session')->setCustomer($customerMock);

        $this->assertEquals('not available', $this->_block->getBankName());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function testGetAccountName()
    {
        // Mock customer session; it's necessary because attribute value is encrypted
        $customerMock = $this->getModelMock('customer/customer', array());
        $customerMock->expects($this->any())
            ->method('getData')
            ->with($this->equalTo('debit_payment_acount_name'))
            ->will($this->returnValue('Test Tester'));
        $this->replaceByMock('model', 'customer/customer', $customerMock);
        Mage::getSingleton('customer/session')->setCustomer($customerMock);

        // Execute test
        $this->assertEquals(
            $this->expected('account')->getData('debit_payment_acount_name'),
            $this->_block->getAccountName()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function testGetAccountBLZ()
    {
        // Mock customer session; it's necessary because attribute value is encrypted
        $customerMock = $this->getModelMock('customer/customer', array());
        $customerMock->expects($this->any())
            ->method('getData')
            ->with($this->equalTo('debit_payment_acount_blz'))
            ->will($this->returnValue('99999999'));
        $this->replaceByMock('model', 'customer/customer', $customerMock);
        Mage::getSingleton('customer/session')->setCustomer($customerMock);

        // Execute test
        $this->assertEquals(
            $this->expected('account')->getData('debit_payment_acount_blz'),
            $this->_block->getAccountBLZ()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function testGetAccountSwift()
    {
        // Mock customer session; it's necessary because attribute value is encrypted
        $customerMock = $this->getModelMock('customer/customer', array());
        $customerMock->expects($this->any())
            ->method('getData')
            ->with($this->equalTo('debit_payment_account_swift'))
            ->will($this->returnValue('XXXXXXXXXXX'));
        $this->replaceByMock('model', 'customer/customer', $customerMock);
        Mage::getSingleton('customer/session')->setCustomer($customerMock);

        // Execute test
        $this->assertEquals(
            $this->expected('account')->getData('debit_payment_account_swift'),
            $this->_block->getAccountSwift()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function testGetAccountIban()
    {
        // Mock customer session; it's necessary because attribute value is encrypted
        $customerMock = $this->getModelMock('customer/customer', array());
        $customerMock->expects($this->any())
            ->method('getData')
            ->with($this->equalTo('debit_payment_account_iban'))
            ->will($this->returnValue('DE99999999999999999999'));
        $this->replaceByMock('model', 'customer/customer', $customerMock);

        Mage::getSingleton('customer/session')->setCustomer($customerMock);

        // Execute test
        $this->assertEquals(
            $this->expected('account')->getData('debit_payment_account_iban'),
            $this->_block->getAccountIban()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function testGetAccountNumber()
    {
        // Mock customer session; it's necessary because attribute value is encrypted
        $customerMock = $this->getModelMock('customer/customer', array());
        $customerMock->expects($this->any())
            ->method('getData')
            ->with($this->equalTo('debit_payment_acount_number'))
            ->will($this->returnValue('9999999999'));
        $this->replaceByMock('model', 'customer/customer', $customerMock);
        Mage::getSingleton('customer/session')->setCustomer($customerMock);

        // Execute test
        $this->assertEquals(
            $this->expected('account')->getData('debit_payment_acount_number'),
            $this->_block->getAccountNumber()
        );
    }
}

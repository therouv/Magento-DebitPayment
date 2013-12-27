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
class Itabs_Debit_Test_Block_Form extends EcomDev_PHPUnit_Test_Case_Controller
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
        $this->_block = Mage::app()->getLayout()->createBlock('debit/form');
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function getAccountSwift()
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
    public function getAccountIban()
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
    public function getAccountCompany()
    {
        $this->customerSession(1);
        $this->assertEquals(
            $this->expected('account')->getData('debit_company'),
            $this->_block->getAccountCompany()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function getAccountStreet()
    {
        $this->customerSession(1);
        $this->assertEquals(
            $this->expected('account')->getData('debit_street'),
            $this->_block->getAccountStreet()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function getAccountCity()
    {
        $this->customerSession(1);
        $this->assertEquals(
            $this->expected('account')->getData('debit_city'),
            $this->_block->getAccountCity()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function getAccountEmail()
    {
        $this->customerSession(1);
        $this->assertEquals(
            $this->expected('account')->getData('debit_email'),
            $this->_block->getAccountEmail()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectation ~Itabs_Debit/accountData
     */
    public function getAccountCountry()
    {
        $this->customerSession(1);
        $this->assertEquals(
            $this->expected('account')->getData('debit_country'),
            $this->_block->getAccountCountry()
        );
    }

    /**
     * @test
     */
    public function getCustomer()
    {
        $this->assertInstanceOf(
            'Mage_Customer_Model_Customer',
            $this->_block->getCustomer()
        );
    }
}

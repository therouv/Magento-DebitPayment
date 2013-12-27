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
 * Block/Info.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Block_Account_Data extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @var Itabs_Debit_Block_Account_Data
     */
    protected $_block;

    /**
     * Instantiate the object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('debit/account_data');
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function getAccountName()
    {
        $this->_createCustomerSession(1);
        $this->assertEquals('Test Tester', $this->_block->getAccountName());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function getAccountSwift()
    {
        $this->_createCustomerSession(1);
        $this->assertEquals('XXXXXXXXXXX', $this->_block->getAccountSwift());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function getAccountIban()
    {
        $this->_createCustomerSession(1);
        $this->assertEquals('DE99999999999999999999', $this->_block->getAccountIban());
    }

    /**
     * Replaces the quote in the checkout session
     */
    protected function _createCustomerSession($customerId)
    {
        $customerSession = $this->getModelMock('customer/session', array('renewSession'));
        $customerSession->loginById($customerId);

        $customerSession->getCustomer()->setData('debit_payment_account_swift', 'XXXXXXXXXXX');
        $customerSession->getCustomer()->setData('debit_payment_account_iban', 'DE99999999999999999999');

        $this->replaceByMock('singleton', 'customer/session', $customerSession);
    }
}

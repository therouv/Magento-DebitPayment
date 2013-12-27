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
 * Block/Mandate.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Block_Mandate extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @var Itabs_Debit_Block_Mandate
     */
    protected $_block;

    /**
     * Instantiate the object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('debit/mandate');
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpecations
     */
    public function getPayee()
    {
        $this->assertEquals(
            $this->expected('config')->getPayee(),
            $this->_block->getPayee()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpecations
     */
    public function getCreditorIdentificationNumber()
    {
        $this->assertEquals(
            $this->expected('config')->getCreditorIdentificationNumber(),
            $this->_block->getCreditorIdentificationNumber()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpecations
     */
    public function getMandateText()
    {
        $this->assertEquals(
            $this->expected('config')->getMandateText(),
            $this->_block->getMandateText()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadExpectations
     */
    public function getMandateReferenceLoggedIn()
    {
        $this->customerSession(1);
        $this->_createCheckoutSession(1);

        $this->assertEquals(
            $this->expected('mandate')->getReference(),
            $this->_block->getMandateReference()
        );

        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function getMandateReferenceNotLoggedIn()
    {
        $this->_createCheckoutSession(2);

        $this->assertEquals(
            $this->expected('mandate')->getReference(),
            $this->_block->getMandateReference()
        );

        $this->reset();
    }

    /**
     * @test
     */
    public function getQuote()
    {
        $this->assertInstanceOf('Mage_Sales_Model_Quote', $this->_block->getQuote());
    }

    /**
     * Replaces the quote in the checkout session
     */
    protected function _createCheckoutSession($quoteId)
    {
        $quote = Mage::getModel('sales/quote')->load($quoteId);
        $checkoutSession = Mage::getSingleton('checkout/session');
        $checkoutSession->replaceQuote($quote);
    }
}

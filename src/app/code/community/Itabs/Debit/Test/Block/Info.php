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
 * Block/Info.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Block_Info extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @var Itabs_Debit_Block_Info
     */
    protected $_block;

    /**
     * Set up the test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_block = self::app()->getLayout()->createBlock('debit/info');

        // Payment Data
        $paymentData = array(
            'debit_cc_type'   => 21050170,
            'debit_cc_owner'  => 'Test Tester',
            'debit_cc_number' => 12345678,
            'debit_swift'     => 'BELADEBEXXX',
            'debit_iban'      => 'DE68210501700012345678',
            'debit_bankname'  => 'Test Bank'
        );

        // Set object data
        $method = Mage::getModel('debit/debit');
        $infoInstance = Mage::getModel('payment/info');
        $infoInstance->setMethod($method->getCode());
        $infoInstance->setMethodInstance($method);
        $method->setData('info_instance', $infoInstance);
        $method->assignData($paymentData);
        $this->_block->setData('method', $method);
        $this->_block->setData('info', $infoInstance);
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itabs_Debit_Block_Info', $this->_block);
    }

    /**
     * @test
     * @loadFixture testGetDebitDataSepa
     */
    public function testToPdf()
    {
        $this->assertNotNull($this->_block->toPdf());
    }

    /**
     * @test
     * @loadFixture testGetDebitDataBank
     */
    public function testToPdfBank()
    {
        $this->assertNotNull($this->_block->toPdf());
    }

    /**
     * @test
     */
    public function testIsEmailContext()
    {
        $this->assertFalse($this->_block->isEmailContext());
    }

    /**
     * @test
     */
    public function testIsEmailContextOrderFrontend()
    {
        $this->setCurrentStore(1);
        $this->assertTrue($this->_getOrderPaymentBlock()->isEmailContext());
        $this->reset();
    }

    /**
     * @test
     */
    public function testIsEmailContextOrderBackend()
    {
        $this->setCurrentStore(0);
        self::app()->getRequest()->setActionName('email');
        $this->assertTrue($this->_getOrderPaymentBlock()->isEmailContext());
        self::app()->getRequest()->setActionName('view');
        $this->assertFalse($this->_getOrderPaymentBlock()->isEmailContext());
        self::app()->getRequest()->setActionName('save');
        $this->assertTrue($this->_getOrderPaymentBlock()->isEmailContext());
        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testSendDataInEmail()
    {
        $this->assertTrue($this->_block->sendDataInEmail());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetDebitDataSepa
     */
    public function testGetDebitType()
    {
        $this->assertEquals('sepa', $this->_block->getDebitType());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetDebitDataSepa
     * @loadExpectations
     */
    public function testGetDebitData()
    {
        $this->assertEquals(
            $this->expected('debitdata')->getResult(),
            $this->_block->getDebitData()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetDebitDataSepa
     * @loadExpectations
     */
    public function testGetDebitDataCheckoutCrypt()
    {
        $this->assertEquals(
            $this->expected('debitdata')->getResult(),
            $this->_block->getDebitData('checkout_crypt')
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetDebitDataSepa
     * @loadExpectations
     */
    public function testGetDebitDataSendmailCrypt()
    {
        $this->assertEquals(
            $this->expected('debitdata')->getResult(),
            $this->_block->getDebitData('sendmail_crypt')
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetDebitDataBank
     * @loadExpectations
     */
    public function testGetDebitDataBank()
    {
        $this->assertEquals(
            $this->expected('debitdata')->getResult(),
            $this->_block->getDebitData()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetDebitDataBank
     * @loadExpectations
     */
    public function testGetDebitDataBankCheckoutCrypt()
    {
        $this->assertEquals(
            $this->expected('debitdata')->getResult(),
            $this->_block->getDebitData('checkout_crypt')
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetDebitDataBank
     * @loadExpectations
     */
    public function testGetDebitDataBankSendmailCrypt()
    {
        $this->assertEquals(
            $this->expected('debitdata')->getResult(),
            $this->_block->getDebitData('sendmail_crypt')
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetDebitDataSepa
     * @loadExpectations
     */
    public function testGetEmailData()
    {
        $this->assertEquals(
            $this->expected('debitdata')->getResult(),
            $this->_block->getEmailData()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetDebitDataBank
     * @loadExpectations
     */
    public function testGetEmailDataBank()
    {
        $this->assertEquals(
            $this->expected('debitdata')->getResult(),
            $this->_block->getEmailData()
        );
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetPdfMessage
     * @loadExpectations
     */
    public function testGetPdfMessage()
    {
        $block = $this->_getOrderPaymentBlock(1);
        $this->assertEquals($this->expected('pdfmessage')->getResult(), $block->getPdfMessage());
    }

    /**
     * @test
     */
    public function testGetPdfMessagePaymentInfo()
    {
        $this->assertFalse($this->_block->getPdfMessage());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @loadFixture testGetPdfMessageNotAllowed
     */
    public function testGetPdfMessageNotAllowed()
    {
        $block = $this->_getOrderPaymentBlock(2);
        $this->assertFalse($block->getPdfMessage());
    }

    /**
     * Retrieve the order payment info block
     *
     * @param  null|int $orderId Order Id
     * @return Itabs_Debit_Block_Info
     */
    protected function _getOrderPaymentBlock($orderId=null)
    {
        /* @var $block Itabs_Debit_Block_Info */
        $block = self::app()->getLayout()->createBlock('debit/info');
        $method = Mage::getModel('debit/debit');
        $infoInstance = Mage::getModel('sales/order_payment');
        $infoInstance->setMethod($method->getCode());
        $infoInstance->setMethodInstance($method);

        if (null !== $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $infoInstance->setOrder($order);
        }

        $method->setData('info_instance', $infoInstance);
        $block->setData('method', $method);
        $block->setData('info', $infoInstance);

        return $block;
    }
}

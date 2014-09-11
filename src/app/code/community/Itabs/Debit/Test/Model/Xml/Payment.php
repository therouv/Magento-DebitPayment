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
 * Itabs_Debit_Model_Xml_Payment Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Model_Xml_Payment extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Debit_Model_Xml_Payment
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_initModel();
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itabs_Debit_Model_Xml_Payment', $this->_model);
    }

    /**
     * @test
     */
    public function testGetControlSum()
    {
        $this->assertEquals(600.00, $this->_model->getControlSum());
    }

    /**
     * @test
     */
    public function testGetBookings()
    {
        $this->assertCount(3, $this->_model->getBookings());
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_Payment::setCreditorIban()
     * @covers \Itabs_Debit_Model_Xml_Payment::getCreditorIban()
     */
    public function testSetCreditorIban()
    {
        $this->_model->setCreditorIban('de00 2105 0170 00 1234 5678');
        $this->assertEquals('DE00210501700012345678', $this->_model->getCreditorIban());
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_Payment::setCreditorId()
     * @covers \Itabs_Debit_Model_Xml_Payment::getCreditorId()
     */
    public function testSetCreditorId()
    {
        $this->_model->setCreditorId('test');
        $this->assertEquals('test', $this->_model->getCreditorId());
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_Payment::setCreditorName()
     * @covers \Itabs_Debit_Model_Xml_Payment::getCreditorName()
     */
    public function testSetCreditorName()
    {
        $this->_model->setCreditorName('test tester');
        $this->assertEquals('test tester', $this->_model->getCreditorName());
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_Payment::setCreditorSwift()
     * @covers \Itabs_Debit_Model_Xml_Payment::getCreditorSwift()
     */
    public function testSetCreditorSwift()
    {
        $this->_model->setCreditorSwift('XXXXXXXXXXX');
        $this->assertEquals('XXXXXXXXXXX', $this->_model->getCreditorSwift());
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_Payment::setCurrency()
     * @covers \Itabs_Debit_Model_Xml_Payment::getCurrency()
     */
    public function testSetCurrency()
    {
        $this->_model->setCurrency('SEK');
        $this->assertEquals('SEK', $this->_model->getCurrency());
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_Payment::setOffset()
     * @covers \Itabs_Debit_Model_Xml_Payment::getOffset()
     */
    public function testSetOffset()
    {
        $this->_model->setOffset(10);
        $this->assertEquals(10, $this->_model->getOffset());
        $this->_model->setOffset(0);
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_Payment::setRecurringSequence()
     * @covers \Itabs_Debit_Model_Xml_Payment::getRecurringSequence()
     */
    public function testSetRecurringSequence()
    {
        $this->_model->setRecurringSequence(true);
        $this->assertTrue($this->_model->getRecurringSequence());
        $this->_model->setRecurringSequence(false);
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_Payment::setOneTimePayment()
     * @covers \Itabs_Debit_Model_Xml_Payment::getOneTimePayment()
     */
    public function testSetOneTimePayment()
    {
        $this->_model->setOneTimePayment(false);
        $this->assertFalse($this->_model->getOneTimePayment());
        $this->_model->setOneTimePayment(true);
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_Payment::setTransactionDate()
     * @covers \Itabs_Debit_Model_Xml_Payment::getTransactionDate()
     */
    public function testSetTransactionDate()
    {
        $this->_model->setTransactionDate('2014-01-01');
        $this->assertEquals('2014-01-01', $this->_model->getTransactionDate());
        $this->_model->setTransactionDate(null);
    }

    /**
     * @test
     */
    public function testGetTransactionDate()
    {
        $this->assertEquals(date('Y-m-d'), $this->_model->getTransactionDate());
        $this->_model->setOffset(2);
        $this->assertEquals(date('Y-m-d', time() + (24 * 3600 * 2)), $this->_model->getTransactionDate());
    }

    /**
     * @param bool $oneTime
     * @param bool $recurringSequence
     * @param bool $mandateChange
     */
    protected function _initModel($oneTime=true, $recurringSequence=false, $mandateChange=false)
    {
        $this->_model = new Itabs_Debit_Model_Xml_Payment(
            'DE98ZZZ09999999999',
            'Test Company',
            'DE68210501700012345678',
            'BELADEBEXXX'
        );
        $this->_model->setOneTimePayment($oneTime);
        $this->_model->setRecurringSequence($recurringSequence);

        for ($i=1; $i<=3; $i++) {
            $booking = new Itabs_Debit_Model_Xml_Booking();
            $booking->setAccountOwner('Debitor Name');
            $booking->setIban('DE68210501700012345678');
            $booking->setSwift('BELADEBEXXX');
            $booking->setAmount($i*100);
            $booking->setBookingText('Order '.$i);
            $booking->setMandateId($i);
            $booking->setMandateChange($mandateChange);
            $this->_model->addBooking($booking);
        }
    }
}

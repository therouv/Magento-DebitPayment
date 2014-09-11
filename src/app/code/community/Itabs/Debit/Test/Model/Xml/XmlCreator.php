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
 * Itabs_Debit_Model_Xml_XmlCreator Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Model_Xml_XmlCreator extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Debit_Model_Xml_XmlCreator
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
        $this->assertInstanceOf('Itabs_Debit_Model_Xml_XmlCreator', $this->_model);
    }

    /**
     * @test
     */
    public function testGenerateXml()
    {
        $xmlValidation = new Itabs_Debit_Model_Xml_Validation();
        $xmlValidation->setXml($this->_model->generateXml());
        $result = $xmlValidation->validate();
        $this->assertTrue($result);

        $xmlValidation = new Itabs_Debit_Model_Xml_Validation();
        $this->_initModel(false, false, true);
        $xmlValidation->setXml($this->_model->generateXml());
        $result = $xmlValidation->validate();
        $this->assertTrue($result);

        $xmlValidation = new Itabs_Debit_Model_Xml_Validation();
        $this->_initModel(false, true, true);
        $xmlValidation->setXml($this->_model->generateXml());
        $result = $xmlValidation->validate();
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function testGetNumberOfTransactions()
    {
        $this->assertEquals(3, $this->_model->getNumberOfTransactions());
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
     * @covers \Itabs_Debit_Model_Xml_XmlCreator::getCreditorName()
     */
    public function testSetCreditorName()
    {
        $this->assertEquals('Test Company', $this->_model->getCreditorName());
        $this->_model->setCreditorName('Company');
        $this->assertEquals('Company', $this->_model->getCreditorName());
    }

    /**
     * @test
     * @covers \Itabs_Debit_Model_Xml_XmlCreator::setCurrency()
     * @covers \Itabs_Debit_Model_Xml_XmlCreator::getCurrency()
     */
    public function testSetCurrency()
    {
        $this->_model->setCurrency('SEK');
        $this->assertEquals('SEK', $this->_model->getCurrency());
    }

    /**
     * @param bool $oneTime
     * @param bool $recurringSequence
     * @param bool $mandateChange
     */
    protected function _initModel($oneTime=true, $recurringSequence=false, $mandateChange=false)
    {
        $this->_model = new Itabs_Debit_Model_Xml_XmlCreator('Test Company');

        $payment = new Itabs_Debit_Model_Xml_Payment(
            'DE98ZZZ09999999999',
            'Test Company',
            'DE68210501700012345678',
            'BELADEBEXXX'
        );
        $payment->setOneTimePayment($oneTime);
        $payment->setRecurringSequence($recurringSequence);

        for ($i=1; $i<=3; $i++) {
            $booking = new Itabs_Debit_Model_Xml_Booking();
            $booking->setAccountOwner('Debitor Name');
            $booking->setIban('DE68210501700012345678');
            $booking->setSwift('BELADEBEXXX');
            $booking->setAmount($i*100);
            $booking->setBookingText('Order '.$i);
            $booking->setMandateId($i);
            $booking->setMandateChange($mandateChange);
            $payment->addBooking($booking);
        }

        $this->_model->addPayment($payment);
    }
}

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
 * Model/Observer.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Debit_Model_Observer
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('debit/observer');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itabs_Debit_Model_Observer', $this->_model);
    }

    /**
     * @test
     */
    public function testPaymentMethodIsActiveWrongMethod()
    {
        $model = Mage::getModel('payment/method_checkmo');

        $observer = new Varien_Event_Observer();
        $event = new Varien_Event();
        $event->setData('method_instance', $model);
        $observer->setData('method_instance', $model);
        $observer->setEvent($event);

        $this->assertFalse($this->_model->paymentMethodIsActive($observer));
    }

    /**
     * @test
     * @loadFixture testPaymentMethodIsActiveDebitNotActive
     */
    public function testPaymentMethodIsActiveDebitNotActive()
    {
        $model = Mage::getModel('debit/debit');

        $observer = new Varien_Event_Observer();
        $event = new Varien_Event();
        $event->setData('method_instance', $model);
        $observer->setData('method_instance', $model);
        $observer->setEvent($event);

        $this->assertFalse($this->_model->paymentMethodIsActive($observer));
    }

    /**
     * @test
     * @loadFixture testPaymentMethodIsActive
     */
    public function testPaymentMethodIsActive()
    {
        $model = Mage::getModel('debit/debit');

        $checkResult = new StdClass;
        $checkResult->isAvailable = true;

        $observer = new Varien_Event_Observer();
        $event = new Varien_Event();
        $event->setData('method_instance', $model);
        $event->setData('result', $checkResult);
        $observer->setData('method_instance', $model);
        $observer->setData('result', $checkResult);
        $observer->setEvent($event);

        $this->_model->paymentMethodIsActive($observer);

        $this->assertTrue($observer->getEvent()->getResult()->isAvailable);
    }
}

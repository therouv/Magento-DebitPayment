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
 * Customer Attribute Backend Encrypted
 */
class Itabs_Debit_Test_Model_Entity_Customer_Attribute_Backend_Encrypted
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Debit_Model_Entity_Customer_Attribute_Backend_Encrypted
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('debit/entity_customer_attribute_backend_encrypted');

        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('customer', 'debit_payment_account_swift');
        $this->_model->setAttribute($attribute);
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itabs_Debit_Model_Entity_Customer_Attribute_Backend_Encrypted', $this->_model);
    }

    /**
     * @test
     */
    public function testBeforeSave()
    {
        $object = new Varien_Object();
        $object->setData('debit_payment_account_swift', 'BELADEBEXXX');

        $this->_model->beforeSave($object);

        $this->assertNotEquals('BELADEBEXXX', $object->getData('debit_payment_account_swift'));
    }

    /**
     * @test
     */
    public function testAfterLoad()
    {
        $object = new Varien_Object();
        $object->setData('debit_payment_account_swift', 'L+m+iX777mjxyQia1EqAOw==');

        $this->_model->afterLoad($object);

        $this->assertNotEquals('L+m+iX777mjxyQia1EqAOw==', $object->getData('debit_payment_account_swift'));
    }
}

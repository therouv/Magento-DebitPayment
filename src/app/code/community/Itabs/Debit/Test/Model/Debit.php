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
 * Model/Debit.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Model_Debit extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Debit_Model_Debit
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('debit/debit');
        $infoInstance = Mage::getModel('payment/info');
        $infoInstance->setMethod($this->_model->getCode());
        $infoInstance->setMethodInstance($this->_model);
        $this->_model->setData('info_instance', $infoInstance);
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itabs_Debit_Model_Debit', $this->_model);
    }

    /**
     * @test
     */
    public function testGetCode()
    {
        $this->assertEquals('debit', $this->_model->getCode());
    }

    /**
     * @test
     */
    public function testGetFormBlockType()
    {
        $this->assertEquals('debit/form', $this->_model->getFormBlockType());
    }

    /**
     * @test
     */
    public function testGetInfoBlockType()
    {
        $this->assertEquals('debit/info', $this->_model->getInfoBlockType());
    }

    /**
     * @test
     */
    public function testCanCapture()
    {
        $this->assertTrue($this->_model->canCapture());
    }

    /**
     * @test
     */
    public function testCanCapturePartial()
    {
        $this->assertTrue($this->_model->canCapturePartial());
    }

    /**
     * @test
     */
    public function testAssignDataOldMode()
    {
        $array = array(
            'cc_type' => 21050170,
            'cc_owner' => 'Test Tester',
            'cc_number' => 12345678,
            'debit_bankname' => 'Bank'
        );

        $this->_model->assignData($array);
    }

    /**
     * @test
     */
    public function testAssignData()
    {
        $array = array(
            'debit_cc_type'   => 21050170,
            'debit_cc_owner'  => 'Test Tester',
            'debit_cc_number' => 12345678,
            'debit_swift'     => 'BELADEBEXXX',
            'debit_iban'      => 'DE68210501700012345678',
            'debit_bankname'  => 'Test Bank'
        );

        $this->_model->assignData($array);

        $this->assertEquals('Test Tester', $this->_model->getAccountName());
        $this->assertEquals(12345678, $this->_model->getAccountNumber());
        $this->assertEquals(21050170, $this->_model->getAccountBLZ());
        $this->assertEquals('Test Bank', $this->_model->getAccountBankname());
        $this->assertEquals('BELADEBEXXX', $this->_model->getAccountSwift());
        $this->assertEquals('DE68210501700012345678', $this->_model->getAccountIban());

        $info = $this->_model->getInfoInstance();
        $accountNumber = $info->encrypt($info->encrypt(12345678));
        $info->setCcNumberEnc($accountNumber);
        $this->assertEquals(12345678, $this->_model->getAccountNumber());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCustomText()
    {
        $this->assertEquals('Debit Custom Text', $this->_model->getCustomText());
    }

    /**
     * Test if you mask a string that you get the correct result
     *
     * @dataProvider dataProvider
     * @loadExpectations
     */
    public function testMaskBankData($data)
    {
        // Load all expectations
        $dataSet = $this->readAttribute($this, 'dataName');

        for ($i = 0; $i < count($data); $i++) {
            $this->assertEquals(
                $this->expected($dataSet)->getData('string_'.$i),
                $this->_model->maskBankData($data[$i])
            );
        }
    }

    /**
     * Test if you mask a string that you get the correct result
     *
     * @dataProvider dataProvider
     * @loadExpectations
     */
    public function testMaskSepaData($data)
    {
        // Load all expectations
        $dataSet = $this->readAttribute($this, 'dataName');

        for ($i = 0; $i < count($data); $i++) {
            $this->assertEquals(
                $this->expected($dataSet)->getData('string_'.$i),
                $this->_model->maskSepaData($data[$i])
            );
        }
    }
}

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
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
/**
 * Model/Debit.php Test Class
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
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

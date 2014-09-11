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
 * Model/Pdf/Mandate.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Model_Pdf_Mandate extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Debit_Model_Pdf_Mandate
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('debit/pdf_mandate');
    }

    /**
     * @test
     */
    public function testLn()
    {
        $this->_model->y = 800;
        $this->_model->ln(20);
        $this->assertEquals(780, $this->_model->y);
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetCreditorIdentificationNumber()
    {
        $this->assertEquals('DE98ZZZ09999999999', $this->_model->getCreditorIdentificationNumber());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function getCreditorAddress()
    {
        $expected = array(
            'Musterfirma GmbH',
            'Musterstraße 99',
            '99999 Musterstadt'
        );

        $this->assertEquals($expected, $this->_model->getCreditorAddress());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function getMandatePdfHeadline()
    {
        $this->assertEquals('Headline', $this->_model->getMandatePdfHeadline());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     */
    public function getMandatePdfText()
    {
        $expected = array(
            'Mandate',
            'Text',
            'For PDF'
        );

        $this->assertEquals($expected, $this->_model->getMandatePdfText());
    }
}

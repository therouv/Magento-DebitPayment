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
 * Model/Cron.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Model_Cron extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Debit_Model_Cron
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('debit/cron');
    }

    /**
     * Test the mandates collection
     *
     * @loadFixture ~Itabs_Debit/default
     */
    public function testGetMandates()
    {
        $this->assertInstanceOf(
            'Itabs_Debit_Model_Resource_Mandates_Collection',
            $this->_model->getMandates()
        );

        // Test that only not generated mandates are returned
        $this->assertEquals(
            $this->expected('collection')->getResult(),
            $this->_model->getMandates()->getAllIds()
        );
    }
}

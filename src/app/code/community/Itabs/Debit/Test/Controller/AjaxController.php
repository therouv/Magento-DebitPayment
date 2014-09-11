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
 * controllers/AjaxController.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Controller_AjaxController
    extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @doNotIndexAll
     */
    public function testCheckBlzValid()
    {
        $this->setCurrentStore(1);

        $this->getRequest()->setPost('bankparam', '61150020');
        $this->getRequest()->setPost('identifier', 'routing');
        $this->dispatch('debit/ajax/checkblz', array('_store' => 'default'));

        $this->assertResponseBodyJson();
        $this->assertResponseBodyContains('61150020');

        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @doNotIndexAll
     */
    public function testCheckBlzInvalid()
    {
        $this->setCurrentStore(1);

        $this->getRequest()->setPost('bankparam', '99999999');
        $this->getRequest()->setPost('identifier', 'routing');
        $this->dispatch('debit/ajax/checkblz', array('_store' => 'default'));

        $this->assertResponseBodyJson();
        $this->assertResponseBodyContains('Bank not found');

        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @doNotIndexAll
     */
    public function testCheckSwiftCodeValid()
    {
        $this->setCurrentStore(1);

        $this->getRequest()->setPost('bankparam', 'ESSLDE66XXX');
        $this->getRequest()->setPost('identifier', 'swift');
        $this->dispatch('debit/ajax/checkblz', array('_store' => 'default'));

        $this->assertResponseBodyJson();
        $this->assertResponseBodyContains('ESSLDE66XXX');

        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @doNotIndexAll
     */
    public function testCheckSwiftCodeInvalid()
    {
        $this->setCurrentStore(1);

        $this->getRequest()->setPost('bankparam', 'XXXXXXXXXXX');
        $this->getRequest()->setPost('identifier', 'swift');
        $this->dispatch('debit/ajax/checkblz', array('_store' => 'default'));

        $this->assertResponseBodyJson();
        $this->assertResponseBodyContains('Bank not found');

        $this->reset();
    }
}

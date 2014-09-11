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
 * controllers/MandateController.php Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Controller_MandateController
    extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @doNotIndexAll
     */
    public function toHtml()
    {
        $this->setCurrentStore(1);

        $this->dispatch('debit/mandate/index', array('_store' => 'default'));

        $this->assertResponseHttpCode(200);
        $this->assertLayoutLoaded();
        $this->assertLayoutHandleLoaded('debit_mandate_index');
        $this->assertLayoutHandleNotLoaded('debit_dynamic_layout_handle');

        $this->reset();
    }

    /**
     * @test
     * @loadFixture ~Itabs_Debit/default
     * @doNotIndexAll
     */
    public function toHtmlAccount()
    {
        $this->setCurrentStore(1);

        $customerMock = $this->getModelMock('customer/session', array('renewSession'));
        $customerMock->loginById(1);
        $this->replaceByMock('model', 'customer/session', $customerMock);

        $this->dispatch('debit/mandate/index', array('_store' => 'default', 'account' => 1));

        $this->assertResponseHttpCode(200);
        $this->assertLayoutLoaded();
        $this->assertLayoutHandleLoaded('debit_mandate_index');
        $this->assertLayoutHandleLoaded('debit_dynamic_layout_handle');

        $this->reset();
    }

    /**
     * @test
     */
    public function printAction()
    {
        $data = array(
            'account_holder' => 'Test Tester',
            'iban' => 'DE68210501700012345678',
            'swiftcode' => 'BELADEBEXXX',
            'bank_name' => 'Test Bank',
            'street' => 'Musterstraße 123',
            'city' => '99999 Musterstadt'
        );

        self::app()->getRequest()->setPost($data);

        $this->dispatch('debit/mandate/print', array('_store' => 'default'));

        $this->assertResponseHttpCode(200);

        $this->reset();
    }
}

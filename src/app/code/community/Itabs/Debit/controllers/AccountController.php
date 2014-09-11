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
 * Account Controller
 */
class Itabs_Debit_AccountController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Check if the customer is authenticated
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Edit bank data
     */
    public function editAction()
    {
        $this->loadLayout();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Debit Account Data'));
        }
        $this->renderLayout();
    }

    /**
     * Save the bank data
     *
     * @throws Mage_Core_Exception
     * @throws Exception
     * @return void
     */
    public function saveAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer) {
            return;
        }

        $now = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $customer->setData('debit_payment_acount_update', $now);
        $customer->setData('debit_payment_acount_name', $this->getRequest()->getPost('account_name'));
        if ($accountNumber = $this->getRequest()->getPost('account_number')) {
            $customer->setData('debit_payment_acount_number', $accountNumber);
        }
        if ($accountBlz = $this->getRequest()->getPost('account_blz')) {
            $customer->setData('debit_payment_acount_blz', $accountBlz);
        }
        if ($accountSwift = $this->getRequest()->getPost('account_swift')) {
            $customer->setData('debit_payment_account_swift', $accountSwift);
        }
        if ($accountIban = $this->getRequest()->getPost('account_iban')) {
            $customer->setData('debit_payment_account_iban', $accountIban);
        }
        if ($bankName = $this->getRequest()->getPost('bank_name')) {
            $customer->setdata('debit_payment_account_bankname', $bankName);
        }

        try {
            $customer->save();
            $this->_getSession()->setCustomer($customer)
                ->addSuccess($this->__('Debit account information was successfully saved'));
            $this->_redirect('customer/account');

            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                ->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                ->addException($e, $this->__('Can\'t save customer'));
        }
    }
}

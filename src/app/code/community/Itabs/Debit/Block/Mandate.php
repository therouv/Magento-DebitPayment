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
 * Debit Mandate Block
 */
class Itabs_Debit_Block_Mandate extends Mage_Core_Block_Template
{
    /**
     * Returns the from action url
     *
     * @return string the url
     */
    public function getFormAction()
    {
        return Mage::getUrl('debit/mandate/print');
    }

    /**
     * Returns the current customer session
     *
     * @return Mage_Customer_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retruns the current customer from customer session
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->getSession()->getCustomer();
    }

    /**
     * Retrieve the hint for the IBAN field
     *
     * @return string|bool
     */
    public function getHintForIbanField()
    {
        return Mage::helper('debit')->getHintForIbanField();
    }

    /**
     * Retrieve the hint for the BIC field
     *
     * @return string|bool
     */
    public function getHintForBicField()
    {
        return Mage::helper('debit')->getHintForBicField();
    }
}

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
 * AjaxController
 */
class Itabs_Debit_AjaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * Checks the given BLZ/BIC if it exists and returns the bankname or an error message
     */
    public function checkblzAction()
    {
        $result = array();

        $bankparam = $this->getRequest()->getPost('bankparam');
        $identifer = $this->getRequest()->getPost('identifier');

        $bankparam = Mage::helper('debit')->sanitizeData($bankparam);
        if ($bank = Mage::helper('debit')->getBankByIdentifier($identifer, $bankparam)) {
            $result['found'] = 1;
            $result['blz'] = $bankparam;
            $result['bank'] = $bank;
        } else {
            $result['found'] = 0;
            $result['blz'] = $bankparam;
            $result['bank'] = $this->__('Bank not found');
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}

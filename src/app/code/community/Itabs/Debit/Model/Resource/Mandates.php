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
 * Resource Model for Export Orders
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Model_Resource_Mandates
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init the main table and the id field name
     */
    protected function _construct()
    {
        $this->_init('debit/mandates', 'id');
    }

    /**
     * Load sepa mandate by order id
     *
     * @param  Itabs_Debit_Model_Mandates $mandates
     * @param  int                        $orderId
     * @return Itabs_Debit_Model_Resource_Mandates
     */
    public function loadByOrder(Itabs_Debit_Model_Mandates $mandates, $orderId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('order_id' => $orderId);
        $select  = $adapter->select()
            ->from($this->getMainTable(), array($this->getIdFieldName()))
            ->where('order_id = :order_id');

        $mandateId = $adapter->fetchOne($select, $bind);
        if ($mandateId) {
            $this->load($mandates, $mandateId);
        } else {
            $mandates->setData(array());
        }

        return $this;
    }
}

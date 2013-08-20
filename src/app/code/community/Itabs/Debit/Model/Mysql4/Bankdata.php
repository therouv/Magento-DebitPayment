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
 * Resource Model for Export Orders
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Model_Mysql4_Bankdata extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * (non-PHPdoc)
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     */
    protected function _construct()
    {
        $this->_init('debit/bankdata', 'id');
    }

    /**
     * Delete all entries by the given country id
     *
     * @param  string $countryId
     * @return Itabs_Debit_Model_Bankdata
     */
    public function deleteByCountryId($countryId)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('country_id = ?', $countryId);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
    }

    /**
     * Retrieve the bank by the given data
     *
     * @param  string $country
     * @param  string $identifier (Routing or Swift)
     * @param  string $value
     * @return bool|string
     */
    public function loadByIdentifier($country, $identifier, $value)
    {
        /* @var $adapter Varien_Db_Adapter_Pdo_Mysql */
        $adapter = $this->_getReadAdapter();

        if ($identifier == 'routing') {
            $field = 'routing_number';
        } else {
            $field = 'swift_code';
        }

        $select = $adapter->select()
            ->from($this->getMainTable(), 'bank_name')
            ->where('country_id=?', $country)
            ->where($field.'=?', $value)
            ->limit(1);

        $result = $adapter->fetchOne($select);
        if (!$result) {
            return false;
        }

        return $result;
    }
}

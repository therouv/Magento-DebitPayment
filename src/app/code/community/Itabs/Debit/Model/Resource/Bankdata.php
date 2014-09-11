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
 * Resource Model for Export Orders
 */
class Itabs_Debit_Model_Resource_Bankdata
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init the main table and id field name
     */
    protected function _construct()
    {
        $this->_init('debit/bankdata', 'id');
    }

    /**
     * Delete all entries by the given country id
     *
     * @param  string $countryId Country ID
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
     * @param  string      $identifier Routing or Swift
     * @param  string      $value      Value
     * @param  null|string $country    Country ID
     * @return bool|string
     */
    public function loadByIdentifier($identifier, $value, $country=null)
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
            ->where($field.'=?', $value);

        // Limit by country if param is given
        if (null !== $country) {
            $select->where('country_id=?', $country);
        }

        // Allow only one result
        $select->limit(1);

        $result = $adapter->fetchOne($select);
        if (!$result) {
            return false;
        }

        return $result;
    }
}

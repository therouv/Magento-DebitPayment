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
 * Model for Bank Data
 *
 * @method Itabs_Debit_Model_Mysql4_Bankdata getResource()
 * @method Itabs_Debit_Model_Mysql4_Bankdata _getResource()
 */
class Itabs_Debit_Model_Bankdata extends Mage_Core_Model_Abstract
{
    /**
     * Init model and resource model
     */
    protected function _construct()
    {
        $this->_init('debit/bankdata');
    }

    /**
     * Delete all entries by the given country id
     *
     * @param  string $countryId Country ID
     * @return Itabs_Debit_Model_Bankdata
     */
    public function deleteByCountryId($countryId)
    {
        $this->_getResource()->deleteByCountryId($countryId);
        return $this;
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
        return $this->_getResource()->loadByIdentifier($identifier, $value, $country);
    }
}

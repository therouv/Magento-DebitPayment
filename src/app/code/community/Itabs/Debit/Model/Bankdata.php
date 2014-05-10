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
 * @version   1.1.0
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
/**
 * Model for Bank Data
 *
 * @method Itabs_Debit_Model_Mysql4_Bankdata getResource()
 * @method Itabs_Debit_Model_Mysql4_Bankdata _getResource()
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.1.0
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Model_Bankdata extends Mage_Core_Model_Abstract
{
    /**
     * (non-PHPdoc)
     * @see Varien_Object::_construct()
     */
    protected function _construct()
    {
        $this->_init('debit/bankdata');
    }

    /**
     * Delete all entries by the given country id
     *
     * @param  string $countryId
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
     * @param  string      $identifier (Routing or Swift)
     * @param  string      $value
     * @param  null|string $country
     * @return bool|string
     */
    public function loadByIdentifier($identifier, $value, $country=null)
    {
        return $this->_getResource()->loadByIdentifier($identifier, $value, $country);
    }
}

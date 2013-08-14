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
 * Abstract Export Model
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Model_Export_Abstract extends Varien_Object
{
    /**
     * @var string File Extension
     */
    protected $_fileExt = '';

    /**
     * @var array
     */
    protected $_orderFilter = false;

    /**
     * Retrieve the helper class
     *
     * @return Itabs_Debit_Helper_Adminhtml
     */
    protected function _getDebitHelper()
    {
        return Mage::helper('debit/adminhtml');
    }

    /**
     * Retrieve the filename for the export file
     *
     * @return string
     */
    public function getFileName()
    {
        return 'EXPORT'.date('YmdHis') . $this->_fileExt;
    }

    /**
     * Check if there are orders available for export..
     *
     * @return Itabs_Debit_Model_Mysql4_Orders_Collection|false
     */
    protected function _hasOrdersToExport()
    {
        /* @var $collection Itabs_Debit_Model_Mysql4_Orders_Collection */
        $collection = Mage::getModel('debit/orders')->getCollection()
            ->addFieldToFilter('status', 0);

        // Apply custom filters if applicable
        if ($this->_orderFilter) {
            foreach ($this->_orderFilter as $field => $condition) {
                $collection->addFieldToFilter($field, $condition);
            }
        }

        // Check if collection coontains orders
        if ($collection->count() == 0) {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->_getDebitHelper()->__('No orders to export.')
            );

            return false;
        }

        return $collection;
    }
}

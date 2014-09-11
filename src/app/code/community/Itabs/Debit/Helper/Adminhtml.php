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
 * Helper class for helper functionalities especially in the adminhtml area..
 */
class Itabs_Debit_Helper_Adminhtml extends Itabs_Debit_Helper_Data
{
    const XML_PATH_BANKACCOUNT_ACCOUNTOWNER  = 'debitpayment/bankaccount/account_owner';
    const XML_PATH_BANKACCOUNT_ROUTINGNUMBER = 'debitpayment/bankaccount/routing_number';
    const XML_PATH_BANKACCOUNT_ACCOUNTNUMBER = 'debitpayment/bankaccount/account_number';

    /**
     * @var Mage_Directory_Model_Resource_Country_Collection
     */
    protected $_countryCollection;

    /**
     * Check if the export requirements are reached for export. Store owner
     * has to enter his bank account data.
     *
     * @return bool
     */
    public function hasExportRequirements()
    {
        if (Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ACCOUNTOWNER) == ''
            || Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ROUTINGNUMBER) == ''
            || Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ACCOUNTNUMBER) == ''
        ) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve the bank account data of the store owenr as array
     *
     * @return array Bank account
     */
    public function getBankAccount()
    {
        return array(
            'name'           => Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ACCOUNTOWNER),
            'bank_code'      => Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ROUTINGNUMBER),
            'account_number' => Mage::getStoreConfig(self::XML_PATH_BANKACCOUNT_ACCOUNTNUMBER)
        );
    }

    /**
     * Retrieve a list of already synced orders, so that a single order is not
     * exported multiple times.
     *
     * @return array Orders
     */
    public function getSyncedOrders()
    {
        $entityIds = array();
        $collection = Mage::getResourceModel('debit/orders_collection');
        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                $entityIds[] = $item->getData('entity_id');
            }
        }

        return $entityIds;
    }

    /**
     * Updates the status of an export order item to "exported"..
     *
     * @param  int $orderId Export Order ID
     * @return bool
     */
    public function setStatusAsExported($orderId)
    {
        $model = Mage::getModel('debit/orders')->load($orderId);
        $model->setData('status', 1);
        $model->save();

        return true;
    }

    /**
     * Retrieve the correct booking text with the configurable text
     *
     * @param  int    $storeId     Store ID
     * @param  string $incrementId Increment ID
     * @return string
     */
    public function getBookingText($storeId, $incrementId)
    {
        $bookingText = array(
            Mage::getStoreConfig('debitpayment/sepa/booking_text', $storeId),
            $incrementId
        );
        return implode(' ', array_filter($bookingText));
    }

    /**
     * Retrieve the option hash
     *
     * @return array
     */
    public function getCountryOptionsHash()
    {
        $options = array();

        $allOptions = $this->getCountryOptions();
        foreach ($allOptions as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }

    /**
     * Retrieve all countries
     *
     * @return bool|array
     */
    public function getCountryOptions()
    {
        $options  = false;
        $useCache = Mage::app()->useCache('config');
        $cacheId  = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
        $cacheTags = array('config');

        if ($useCache) {
            if ($optionsCache = Mage::app()->loadCache($cacheId)) {
                $options = unserialize($optionsCache);
            }
        }

        if ($options == false) {
            $options = $this->getCountryCollection()->toOptionArray(false);
            if ($useCache) {
                Mage::app()->saveCache(serialize($options), $cacheId, $cacheTags);
            }
        }

        return $options;
    }

    /**
     * Retrieve the country collection
     *
     * @return Mage_Directory_Model_Resource_Country_Collection
     */
    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getSingleton('directory/country')
                ->getResourceCollection()
                ->loadByStore();
        }

        return $this->_countryCollection;
    }
}

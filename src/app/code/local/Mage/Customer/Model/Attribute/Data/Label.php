<?php
/**
 * ============================================================================
 * 
 * NECESSARY FILE FOR EXTENSION "DEBIT PAYMENT"
 * 
 * THIS FILE IS IN THIS PACKAGE BECAUSE MAGENTO DOES NOT PROVIDE SUCH AN FILE
 * IF YOU HAVE AN CUSTOM CUSTOMER ATTRIBUTE "LABEL" FOR THE "INPUT" FIELD.
 * 
 * THIS FILE WILL BE REMOVED WHEN MAGENTO WILL HOPEFULLY DELIVER SUCH A FILE IN 
 * AN UPCOMING MAGENTO RELEASE.
 * 
 * ============================================================================
 * 
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Customer Attribute Select Data Model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Attribute_Data_Label extends Mage_Customer_Model_Attribute_Data_Abstract
{
    /**
     * Extract data from request and return value
     *
     * @param Zend_Controller_Request_Http $request
     * @return array|string
     */
    public function extractValue(Zend_Controller_Request_Http $request)
    {
        return $this->_getRequestValue($request);
    }

    /**
     * Validate data
     * Return true or array of errors
     *
     * @param array|string $value
     * @return boolean|array
     */
    public function validateValue($value)
    {
        return true;
    }

    /**
     * Export attribute value to entity model
     *
     * @param array|string $value
     * @return Mage_Customer_Model_Attribute_Data_Text
     */
    public function compactValue($value)
    {
        if ($value !== false) {
            $this->getEntity()->setData($this->getAttribute()->getAttributeCode(), $value);
        }
        return $this;
    }

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param array|string $value
     * @return Mage_Customer_Model_Attribute_Data_Abstract
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * Return a text for option value
     *
     * @param int $value
     * @return string
     */
    protected function _getOptionText($value)
    {
        return $this->getAttribute()->getSource()->getOptionText($value);
    }

    /**
     * Return formated attribute value from entity model
     *
     * @return string|array
     */
    public function outputValue($format = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT)
    {
        $value = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());
        switch ($format) {
            case Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_JSON:
                $output = $value;
            default:
                if ($value != '') {
                    $output = $this->_getOptionText($value);
                } else {
                    $output = '';
                }
                break;
        }
        return $output;
    }
}

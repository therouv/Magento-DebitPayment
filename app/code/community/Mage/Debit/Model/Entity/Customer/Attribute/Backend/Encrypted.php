<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package    Mage_Debit
 * @copyright  Copyright (c) 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Debit_Model_Entity_Customer_Attribute_Backend_Encrypted extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Encrypt value before saving
     * @param <type> $object
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $value = Mage::helper('core')->encrypt($object->getData($attributeName));
        $object->setData($attributeName, $value);
    }

    /**
     * decrypt value after load
     * @param <type> $object
     */
    public function afterLoad($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $value = Mage::helper('core')->decrypt($object->getData($attributeName));
        $object->setData($attributeName, $value);
    }
}
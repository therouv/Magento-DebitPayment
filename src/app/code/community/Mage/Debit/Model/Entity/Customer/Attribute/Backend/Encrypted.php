<?php
/**
 * This file is part of the Mage_Debit module.
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
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
/**
 * Customer Attribute Backend Encrypted
 *
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
class Mage_Debit_Model_Entity_Customer_Attribute_Backend_Encrypted
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Encrypts the value before saving
     *
     * @param  <type> $object Object
     * @return void
     */
    public function beforeSave($object)
    {
        $helper = Mage::helper('core');
        $attributeName = $this->getAttribute()->getName();
        $value = $helper->encrypt($object->getData($attributeName));
        $object->setData($attributeName, $value);
    }

    /**
     * Decrypts the value after load
     *
     * @param  <type> $object Object
     * @return void
     */
    public function afterLoad($object)
    {
        $helper = Mage::helper('core');
        $attributeName = $this->getAttribute()->getName();
        $value = $helper->decrypt($object->getData($attributeName));
        $object->setData($attributeName, $value);
    }
}

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
 * Setup script
 *
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();


/*
 * INSTALL ATTRIBUTES
 */
$read = Mage::getSingleton('Mage_Core_Model_Resource')->getConnection('core_read');
$eid = $read->fetchRow(
    "select entity_type_id from {$installer->getTable('eav_entity_type')} where entity_type_code = 'customer'"
);
$customerTypeId = $eid['entity_type_id'];

$attrDate = array(
    'type' => 'datetime',
    'input' => 'date',
    'label' => 'Account update date',
    'global' => 1,
    'required' => 0,
    'default' => '',
    'position' => '100'
);

$attrName = array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Account Name',
    'global' => 1,
    'required' => 0,
    'default' => '',
    'position' => '100'
);

$attrNumber = array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Account number',
    'backend' => 'Mage_Debit_Model_Entity_Customer_Attribute_Backend_Encrypted',
    'global' => 1,
    'required' => 0,
    'default' => '',
    'position' => '100'
);

$attrBlz = array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Bank code',
    'backend' => 'Mage_Debit_Model_Entity_Customer_Attribute_Backend_Encrypted',
    'global' => 1,
    'required' => 0,
    'default' => '',
    'position' => '100'
);


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute($customerTypeId, 'debit_payment_acount_update', $attrDate);
$setup->addAttribute($customerTypeId, 'debit_payment_acount_name', $attrName);
$setup->addAttribute($customerTypeId, 'debit_payment_acount_number', $attrNumber);
$setup->addAttribute($customerTypeId, 'debit_payment_acount_blz', $attrBlz);

// Since 1.4.2.0 this is necessary!
$eavConfig = Mage::getSingleton('Mage_Eav_Model_Config');

$attribute = $eavConfig->getAttribute($customerTypeId, 'debit_payment_acount_update');
$attribute->setData('used_in_forms', array('customer_account_edit', 'customer_account_create', 'adminhtml_customer'));
$attribute->save();

$attribute = $eavConfig->getAttribute($customerTypeId, 'debit_payment_acount_name');
$attribute->setData('used_in_forms', array('customer_account_edit', 'customer_account_create', 'adminhtml_customer'));
$attribute->save();

$attribute = $eavConfig->getAttribute($customerTypeId, 'debit_payment_acount_number');
$attribute->setData('used_in_forms', array('customer_account_edit', 'customer_account_create', 'adminhtml_customer'));
$attribute->save();

$attribute = $eavConfig->getAttribute($customerTypeId, 'debit_payment_acount_blz');
$attribute->setData('used_in_forms', array('customer_account_edit', 'customer_account_create', 'adminhtml_customer'));
$attribute->save();


/*
 * INSTALL DATABASE TABLE
 */

$sql = "DROP TABLE IF EXISTS `{$installer->getTable('debit_order_grid')}`;
	CREATE TABLE `{$installer->getTable('debit_order_grid')}` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
	`entity_id` int(10) unsigned NOT NULL COMMENT 'Entity Id',
  	`store_id` smallint(5) unsigned DEFAULT NULL COMMENT 'Store Id',
  	`customer_id` int(10) unsigned DEFAULT NULL COMMENT 'Customer Id',
  	`grand_total` decimal(12,4) DEFAULT NULL COMMENT 'Grand Total',
  	`increment_id` varchar(50) DEFAULT NULL COMMENT 'Increment Id',
  	`order_currency_code` varchar(255) DEFAULT NULL COMMENT 'Order Currency Code',
  	`billing_name` varchar(255) DEFAULT NULL COMMENT 'Billing Name',
  	`created_at` timestamp NULL DEFAULT NULL COMMENT 'Created At',
  	`status` int(1) unsigned DEFAULT '0' COMMENT 'Status',
  	PRIMARY KEY (`id`),
  	UNIQUE KEY `UNQ_DEBITPAYMENT_ORDER_GRID_INCREMENT_ID` (`increment_id`),
  	CONSTRAINT `FK_DEBITPAYMENT_ORDER_GRID_CUSTOMER_ID_CUSTOMER_ENTITY_ENTITY_ID` FOREIGN KEY (`customer_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  	CONSTRAINT `FK_DEBITPAYMENT_GRID_ENTITY_ID_SALES_FLAT_ORDER_ENTITY_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('sales_flat_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  	CONSTRAINT `FK_DEBITPAYMENT_ORDER_GRID_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Debit Payment Order Grid';";

$installer->run($sql);


// End setup
$installer->endSetup();
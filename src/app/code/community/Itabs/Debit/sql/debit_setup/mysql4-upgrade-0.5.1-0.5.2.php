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
 * @version   1.0.0
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
/**
 * Setup script
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.0
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$sql = "DROP TABLE IF EXISTS `{$installer->getTable('debit/order_grid')}`;
    CREATE TABLE `{$installer->getTable('debit/order_grid')}` (
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
      CONSTRAINT `FK_DEBITPAYMENT_ORDER_GRID_CUSTOMER_ID_CUSTOMER_ENTITY_ENTITY_ID`
          FOREIGN KEY (`customer_id`)
          REFERENCES `{$installer->getTable('customer/entity')}` (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
      CONSTRAINT `FK_DEBITPAYMENT_GRID_ENTITY_ID_SALES_FLAT_ORDER_ENTITY_ID`
          FOREIGN KEY (`entity_id`)
          REFERENCES `{$installer->getTable('sales/order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_DEBITPAYMENT_ORDER_GRID_STORE_ID_CORE_STORE_STORE_ID`
          FOREIGN KEY (`store_id`)
          REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Debit Payment Order Grid';";

$installer->run($sql);

$installer->endSetup();

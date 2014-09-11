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
 * Setup script
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// Get the tablename
$tableName = $installer->getTable('debit/bankdata');

// Delete table if it already exists..
if ($installer->getConnection()->isTableExists($tableName)) {
    $installer->getConnection()->dropTable($tableName);
}

// Set the table structure and create the table
$table = $installer->getConnection()->newTable($tableName)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
            'identity' => true,
        ), 'ID')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(), 'Country ID')
    ->addColumn('routing_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Routing Number')
    ->addColumn('swift_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'SWIFT Code')
    ->addColumn('bank_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Bank Name')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array('nullable' => false), 'Created At')
    ->setComment('Debit Payment Bank Data');
$installer->getConnection()->createTable($table);

$installer->endSetup();

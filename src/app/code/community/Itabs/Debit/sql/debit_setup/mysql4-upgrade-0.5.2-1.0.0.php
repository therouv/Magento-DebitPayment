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
 * Setup script
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
// load id for customer entity
$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$eid = $read->fetchRow(
    "select entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code = 'customer'"
);
$customerTypeId = $eid['entity_type_id'];

$attrSwift = array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Account Swift Code',
    'backend' => 'debit/entity_customer_attribute_backend_encrypted',
    'global' => 1,
    'required' => 0,
    'default' => '',
    'position' => '100'
);

$attrIban = array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Account IBAN',
    'backend' => 'debit/entity_customer_attribute_backend_encrypted',
    'global' => 1,
    'required' => 0,
    'default' => '',
    'position' => '100'
);

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute($customerTypeId, 'debit_payment_account_swift', $attrSwift);
$setup->addAttribute($customerTypeId, 'debit_payment_account_iban', $attrIban);

// Since 1.4.2.0 this is necessary!
$eavConfig = Mage::getSingleton('eav/config');

$attribute = $eavConfig->getAttribute($customerTypeId, 'debit_payment_account_swift');
$attribute->setData('used_in_forms', array('customer_account_edit', 'customer_account_create', 'adminhtml_customer'));
$attribute->save();

$attribute = $eavConfig->getAttribute($customerTypeId, 'debit_payment_account_iban');
$attribute->setData('used_in_forms', array('customer_account_edit', 'customer_account_create', 'adminhtml_customer'));
$attribute->save();

// Add new fields to quote_payment and order_payment table

$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote_payment'),
    'debit_swift',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'lenght' => 255,
        'comment' => 'Debit Swift Code'
    )
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote_payment'),
    'debit_iban',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'lenght' => 255,
        'comment' => 'Debit IBAN'
    )
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote_payment'),
    'debit_type',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'lenght' => 4,
        'comment' => 'Debit Type'
    )
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_payment'),
    'debit_swift',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'lenght' => 255,
        'comment' => 'Debit Swift Code'
    )
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_payment'),
    'debit_iban',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'lenght' => 255,
        'comment' => 'Debit IBAN'
    )
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_payment'),
    'debit_type',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'lenght' => 4,
        'comment' => 'Debit Type'
    )
);

// Add new field to the debit order grid
$installer->getConnection()->addColumn(
    $installer->getTable('debit/order_grid'),
    'debit_type',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'lenght' => 4,
        'comment' => 'Debit Type'
    )
);


// End setup
$installer->endSetup();

// EOF

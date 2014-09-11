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

// load id for customer entity
$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$eid = $read->fetchRow(
    "select entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code = 'customer'"
);
$customerTypeId = $eid['entity_type_id'];

$attrBankname = array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Account Bank Name',
    'global' => 1,
    'required' => 0,
    'default' => '',
    'position' => '100'
);

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute($customerTypeId, 'debit_payment_account_bankname', $attrBankname);

// Since 1.4.2.0 this is necessary!
$eavConfig = Mage::getSingleton('eav/config');

$attribute = $eavConfig->getAttribute($customerTypeId, 'debit_payment_account_bankname');
$attribute->setData('used_in_forms', array('customer_account_edit', 'customer_account_create', 'adminhtml_customer'));
$attribute->save();

// Add new fields to quote_payment and order_payment table

$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote_payment'),
    'debit_bankname',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Debit Bank Name'
    )
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_payment'),
    'debit_bankname',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Debit Bank Name'
    )
);

// End setup
$installer->endSetup();

// EOF

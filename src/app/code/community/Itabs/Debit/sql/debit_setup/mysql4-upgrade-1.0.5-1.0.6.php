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
 * @copyright 2008-2014 ITABS GmbH (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.7
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
/**
 * Setup script
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/*
 * PREREQUISITES
 */

$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$eid = $read->fetchRow(
    "select entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code = 'customer'"
);
$customerTypeId = $eid['entity_type_id'];

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$eavConfig = Mage::getSingleton('eav/config');


/*
 * DEFINE NEW ATTRIBUTES
 */

$attributes = array(
    'debit_company' => array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Debit Account Company',
        'global' => 1,
        'required' => 0,
        'default' => '',
        'position' => '100'
    ),
    'debit_street' => array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Debit Account Street',
        'global' => 1,
        'required' => 0,
        'default' => '',
        'position' => '100'
    ),
    'debit_city' => array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Debit Account City',
        'global' => 1,
        'required' => 0,
        'default' => '',
        'position' => '100'
    ),
    'debit_country' => array(
        'type' => 'varchar',
        'input' => 'select',
        'label' => 'Debit Account Country',
        'source' => 'customer/entity_address_attribute_source_country',
        'frontend_class' => 'countries input-text',
        'global' => 1,
        'required' => 0,
        'default' => '',
        'position' => '100'
    ),
    'debit_email' => array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Debit Account Email Address',
        'global' => 1,
        'required' => 0,
        'default' => '',
        'position' => '100'
    )
);


/*
 * PROCESS ATTRIBUTES
 */

foreach ($attributes as $attributeCode => $attributeData) {
    // Add customer attribute
    $setup->addAttribute($customerTypeId, $attributeCode, $attributeData);

    // Add customer attribute to forms
    $attribute = $eavConfig->getAttribute($customerTypeId, $attributeCode);
    $attribute->setData('used_in_forms', array('customer_account_edit', 'customer_account_create', 'adminhtml_customer'));
    $attribute->save();

    // Add sales_flat_quote_payment field
    $installer->getConnection()->addColumn(
        $installer->getTable('sales/quote_payment'),
        $attributeCode,
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'comment' => $attributeData['label']
        )
    );

    // Add sales_flat_order_payment field
    $installer->getConnection()->addColumn(
        $installer->getTable('sales/order_payment'),
        $attributeCode,
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'comment' => $attributeData['label']
        )
    );
}

$installer->endSetup();

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
// load id for customer entity
$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$eid = $read->fetchRow(
    "select entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code = 'customer'"
);
$customerTypeId = $eid['entity_type_id'];

$attrDate = array(
    'type' => 'datetime',
    'input' => 'label',
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
    'backend' => 'debit/entity_customer_attribute_backend_encrypted',
    'global' => 1,
    'required' => 0,
    'default' => '',
    'position' => '100'
);

$attrBlz = array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Bank code',
    'backend' => 'debit/entity_customer_attribute_backend_encrypted',
    'global' => 1,
    'required' => 0,
    'default' => '',
    'position' => '100'
);

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute($customerTypeId, 'debit_payment_acount_update', $attrDate);
$setup->addAttribute($customerTypeId, 'debit_payment_acount_name', $attrName);
$setup->addAttribute($customerTypeId, 'debit_payment_acount_number', $attrNumber);
$setup->addAttribute($customerTypeId, 'debit_payment_acount_blz', $attrBlz);

// Since 1.4.2.0 this is necessary!
$eavConfig = Mage::getSingleton('eav/config');

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

// End setup
$installer->endSetup();

// EOF

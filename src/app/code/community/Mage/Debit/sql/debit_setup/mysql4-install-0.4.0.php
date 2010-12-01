<?php
// load id for customer entity
$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$eid = $read->fetchRow("select entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code = 'customer'");
$customer_type_id = $eid['entity_type_id'];

$attr_date = array(
	'type' => 'datetime',
	'input' => 'label',
	'label' => 'Account update date',
	'global' => 1,
	'required' => 0,
	'default' => '',
    'position' => '100'
);

$attr_name = array(
	'type' => 'varchar',
	'input' => 'text',
	'label' => 'Account Name',
	'global' => 1,
	'required' => 0,
	'default' => '',
    'position' => '100'
);


$attr_number = array(
	'type' => 'varchar',
	'input' => 'text',
	'label' => 'Account number',
    'backend' => 'debit/entity_customer_attribute_backend_encrypted',
	'global' => 1,
	'required' => 0,
	'default' => '',
    'position' => '100'
);

$attr_blz = array(
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
$setup->addAttribute($customer_type_id, 'debit_payment_acount_data_update', $attr_date);
$setup->addAttribute($customer_type_id, 'debit_payment_acount_name', $attr_name);
$setup->addAttribute($customer_type_id, 'debit_payment_acount_number', $attr_number);
$setup->addAttribute($customer_type_id, 'debit_payment_acount_blz', $attr_blz);

$installer->endSetup();

// EOF


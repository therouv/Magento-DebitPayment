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
 * Config Test Class
 *
 * @group Itabs_Debit
 */
class Itabs_Debit_Test_Config_Config extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * @test
     * @loadExpections
     */
    public function globalConfig()
    {
        $this->assertModuleVersion($this->expected('module')->getVersion());
        $this->assertModuleCodePool($this->expected('module')->getCodePool());

        foreach ($this->expected('module')->getDepends() as $depend) {
            $this->assertModuleDepends($depend);
        }
    }

    /**
     * @test
     */
    public function testClassAliases()
    {
        $this->assertBlockAlias('debit/account_data', 'Itabs_Debit_Block_Account_Data');
        $this->assertBlockAlias('debit/adminhtml_bankdata_grid', 'Itabs_Debit_Block_Adminhtml_Bankdata_Grid');
        $this->assertBlockAlias('debit/adminhtml_bankdata_upload', 'Itabs_Debit_Block_Adminhtml_Bankdata_Upload');
        $this->assertBlockAlias('debit/adminhtml_order_grid', 'Itabs_Debit_Block_Adminhtml_Order_Grid');
        $this->assertBlockAlias('debit/adminhtml_bankdata', 'Itabs_Debit_Block_Adminhtml_Bankdata');
        $this->assertBlockAlias('debit/adminhtml_order', 'Itabs_Debit_Block_Adminhtml_Order');
        $this->assertBlockAlias('debit/form', 'Itabs_Debit_Block_Form');
        $this->assertBlockAlias('debit/info', 'Itabs_Debit_Block_Info');
        $this->assertBlockAlias('debit/mandate', 'Itabs_Debit_Block_Mandate');

        $this->assertHelperAlias('debit/adminhtml', 'Itabs_Debit_Helper_Adminhtml');
        $this->assertHelperAlias('debit', 'Itabs_Debit_Helper_Data');

        $this->assertModelAlias(
            'debit/entity_customer_attribute_backend_encrypted',
            'Itabs_Debit_Model_Entity_Customer_Attribute_Backend_Encrypted'
        );
        $this->assertModelAlias('debit/export_abstract', 'Itabs_Debit_Model_Export_Abstract');
        $this->assertModelAlias('debit/export_csv', 'Itabs_Debit_Model_Export_Csv');
        $this->assertModelAlias('debit/export_dtaus', 'Itabs_Debit_Model_Export_Dtaus');

        $this->assertModelAlias('debit/import_bankdata', 'Itabs_Debit_Model_Import_Bankdata');

        $this->assertModelAlias('debit/mysql4_orders_collection', 'Itabs_Debit_Model_Mysql4_Orders_Collection');
        $this->assertModelAlias('debit/mysql4_orders', 'Itabs_Debit_Model_Mysql4_Orders');

        $this->assertModelAlias('debit/resource_orders_collection', 'Itabs_Debit_Model_Resource_Orders_Collection');
        $this->assertModelAlias('debit/resource_orders', 'Itabs_Debit_Model_Resource_Orders');

        $this->assertModelAlias('debit/pdf_mandate', 'Itabs_Debit_Model_Pdf_Mandate');

        $this->assertModelAlias(
            'debit/system_config_source_customer_group',
            'Itabs_Debit_Model_System_Config_Source_Customer_Group'
        );
        $this->assertModelAlias(
            'debit/system_config_source_debit_status',
            'Itabs_Debit_Model_System_Config_Source_Debit_Status'
        );
        $this->assertModelAlias(
            'debit/system_config_source_debit_type',
            'Itabs_Debit_Model_System_Config_Source_Debit_Type'
        );
        $this->assertModelAlias('debit/bankdata', 'Itabs_Debit_Model_Bankdata');
        $this->assertModelAlias('debit/debit', 'Itabs_Debit_Model_Debit');
        $this->assertModelAlias('debit/observer', 'Itabs_Debit_Model_Observer');
        $this->assertModelAlias('debit/orders', 'Itabs_Debit_Model_Orders');
        $this->assertModelAlias('debit/validation', 'Itabs_Debit_Model_Validation');
    }

    /**
     * @test
     */
    public function testSetupResource()
    {
        $this->assertSetupResourceDefined('Itabs_Debit', 'debit_setup');
        $this->assertSetupResourceExists('Itabs_Debit', 'debit_setup');
        $this->assertSetupScriptVersions();
    }

    /**
     * @test
     */
    public function testEventObserver()
    {
        $this->assertEventObserverDefined(
            'global',
            'payment_method_is_active',
            'debit/observer',
            'paymentMethodIsActive'
        );

        $this->assertEventObserverDefined(
            'global',
            'sales_order_save_after',
            'debit/observer',
            'saveAccountInfo'
        );

        $this->assertEventObserverDefined(
            'adminhtml',
            'sales_quote_payment_save_before',
            'debit/observer',
            'encryptBankDataInAdminhtmlQuote'
        );

        $this->assertEventObserverDefined(
            'adminhtml',
            'sales_order_payment_save_before',
            'debit/observer',
            'encryptBankDataInAdminhtmlOrder'
        );

        $this->assertEventObserverDefined(
            'frontend',
            'controller_action_layout_load_before',
            'debit/observer',
            'controllerActionLayoutLoadBefore'
        );
    }

    /**
     * @test
     */
    public function testLayoutFiles()
    {
        $this->assertLayoutFileDefined('frontend', 'debit.xml');
        $this->assertLayoutFileExists('frontend', 'debit.xml');
        $this->assertLayoutFileExists('adminhtml', 'debit.xml');
        $this->assertLayoutFileDefined('adminhtml', 'debit.xml');
    }
}

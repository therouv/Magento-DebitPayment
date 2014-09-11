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

/* @var Mage_Core_Model_App_Emulation $emulation */
$emulation = Mage::getModel('core/app_emulation');
$oldStore = $emulation->startEnvironmentEmulation(Mage_Core_Model_App::ADMIN_STORE_ID);

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block')->setStoreId(0)->load('debit_mandate_form');
$block->setStores(array(0));
$block->setIdentifier('debit_mandate_form');
$block->setTitle('Debit Mandate Forme');
$block->setContent('Statischer Block "debit_mandate_form"');
$block->setActive(true);
$block->save();

$emulation->stopEnvironmentEmulation($oldStore);

$installer->endSetup();

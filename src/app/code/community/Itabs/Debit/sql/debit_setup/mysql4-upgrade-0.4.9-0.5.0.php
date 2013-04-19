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
$installer = $this;
$installer->startSetup();

$write = Mage::getSingleton('core/resource')->getConnection('core_write');
$write->query('SET FOREIGN_KEY_CHECKS=0;');
$write->query("
    UPDATE {$this->getTable('eav_attribute')}
    SET attribute_code = 'debit_payment_acount_update'
    WHERE attribute_code = 'debit_payment_acount_data_update';
");
$write->query('SET FOREIGN_KEY_CHECKS=1;');

$installer->endSetup();

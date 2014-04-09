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
require_once 'abstract.php';
/**
 * Shell script
 *
 * @category Itabs
 * @package  Itabs_Debit
 * @author   Rouven Alexander Rieker <rouven.rieker@itabs.de>
 */
class Itabs_Debit_Shell extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('mandates')) {
            /* @var $cronClass Itabs_Debit_Model_Cron */
            $cronClass = Mage::getModel('debit/cron');
            $cronClass->generate();
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f debit.php -- [options]

  mandates     Generates the mandates PDF and sends them to the customers
  help         This help

USAGE;
    }
}

$shell = new Itabs_Debit_Shell();
$shell->run();

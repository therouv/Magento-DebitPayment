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
 * Helper/Data.php Test Class
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test if the getBankByBlz method returns a bank name
     *
     * @param array $data
     * @dataProvider dataProvider
     */
    public function testGetBankByBlz($data)
    {
        /* @var $helper Itabs_Debit_Helper_Data */
        $helper = Mage::helper('debit');

        // Load all expectations
        $dataSet = $this->readAttribute($this, 'dataName');

        for ($i = 0; $i < count($data); $i++) {
            $this->assertEquals(
                $this->expected($dataSet)->getData('name_'.$i),
                $helper->getBankByBlz($data[$i])
            );
        }
    }

    /**
     * Test if the customer enters a faulty string that it
     * gets sanitized correctly
     *
     * @param array $data
     * @dataProvider dataProvider
     */
    public function testSanitizeData($data)
    {
        /* @var $helper Itabs_Debit_Helper_Data */
        $helper = Mage::helper('debit');

        // Load all expectations
        $dataSet = $this->readAttribute($this, 'dataName');

        foreach ($data as $key => $value) {
            $this->assertEquals(
                $this->expected($dataSet)->getData($key),
                $helper->sanitizeData($value)
            );
        }
    }
}

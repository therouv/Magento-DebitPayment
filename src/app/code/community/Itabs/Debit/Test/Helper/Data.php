<?php
/**
 * This file is part of the customizing project.
 *
 * PHP version 5
 *
 * @category
 * @package
 * @author    ITABS GmbH / Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2013 ITABS GmbH (http://www.itabs.de/). All rights served.
 * @license   http://www.itabs.de/ Commercial License
 * @version   $Id:$
 * @link      http://www.itabs.de/
 */
class Itabs_Debit_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @param array $data
     * @dataProvider dataProvider
     */
    public function testSanitizeData($data)
    {
        $dataSet = $this->readAttribute($this, 'dataName');

        /* @var $helper Itabs_Debit_Helper_Data */
        $helper = Mage::helper('debit');

        foreach ($data as $key => $value) {
            $this->assertEquals(
                $this->expected($dataSet)->getData($key),
                $helper->sanitizeData($value)
            );
        }
    }
}

<?php
/**
 * This file is part of the Mage_Debit module.
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
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2011 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
/**
 * System Config Customer Groups
 * 
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2011 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
class Mage_Debit_Model_System_Config_Source_Customer_Group
{
    /** 
     * @var array Customer Groups
     */
    protected $_options;

    /**
     * Returns the customer groups as an array for system configuration
     * 
     * @return array Customer Groups
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $collection = Mage::getResourceModel('customer/group_collection')
                ->loadData()
                ->toOptionArray();
            $this->_options = $collection;

            array_unshift(
                $this->_options,
                array(
                    'value' => '',
                    'label' => Mage::helper('debit')->__('-- Please Select --')
                )
            );
        }
        return $this->_options;
    }
}

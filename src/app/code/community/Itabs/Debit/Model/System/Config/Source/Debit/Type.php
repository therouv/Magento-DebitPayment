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
 * Debit Types
 *
 * @category  Itabs
 * @package   Itabs_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2008-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   1.0.2
 * @link      http://www.magentocommerce.com/magento-connect/debitpayment.html
 */
class Itabs_Debit_Model_System_Config_Source_Debit_Type
{
    /**
     * @var array Debit Types
     */
    protected $_options;

    /**
     * Returns the debit types as an array for system configuration
     *
     * @return array Debit Types
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => 'bank',
                    'label' => Mage::helper('debit')->__('Bank Account & Routing Number')
                ),
                array(
                    'value' => 'sepa',
                    'label' => Mage::helper('debit')->__('SEPA')
                )
            );
        }

        return $this->_options;
    }

    /**
     * Returns the debit types as option hash for grid view
     *
     * @return array
     */
    public function toOptionHash()
    {
        $options = $this->toOptionArray();

        $hash = array();
        foreach ($options as $option) {
            $hash[$option['value']] = $option['label'];
        }

        return $hash;
    }
}

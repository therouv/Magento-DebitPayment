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
 * Upload Block
 */
class Itabs_Debit_Block_Adminhtml_Bankdata_Upload extends Mage_Adminhtml_Block_Widget
{
    /**
     * Upload Form constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('debit/bankdata/upload.phtml');
    }

    /**
     * Retrieve the country html select
     *
     * @return string
     */
    public function getCountryHtmlSelect()
    {
        $countryId = Mage::helper('core')->getDefaultCountry();

        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('country_id')
            ->setId('country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->helper('debit/adminhtml')->getCountryOptions());

        return $select->getHtml();
    }
}

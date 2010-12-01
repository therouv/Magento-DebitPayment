<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package    Mage_Debit
 * @copyright  Copyright (c) 2010 ITABS GbR - Rouven Alexander Rieker
 * @copyright  Copyright (c) 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Debit_Model_Debit extends Mage_Payment_Model_Method_Abstract
{
    /**
     * unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'debit';
    protected $_formBlockType = 'debit/form';
    protected $_infoBlockType = 'debit/info';


    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setCcType($info->encrypt($data->getDebitBlz()))		 // BLZ
             ->setCcOwner($data->getDebitOwner())						 // Kontoinhaber
             ->setCcNumberEnc($info->encrypt($data->getDebitNumber())); // Kontonummer
        return $this;
    }

    public function getCustomText()
    {
        return $this->getConfigData('customtext');
    }

	public function getAccountName()
	{
		$info = $this->getInfoInstance();
		return $info->getCcOwner();
	}

	public function getAccountNumber()
	{
		$info = $this->getInfoInstance();
		$data = $info->getCcNumberEnc();
		
		if(!is_numeric($data)) {
			$data = $info->decrypt($data);
		}
		if(!is_numeric($data)) {
			$data = $info->decrypt($data);
		}
		
		return $data;
	}

	public function getAccountBLZ()
	{
		$info = $this->getInfoInstance();
		$data = $info->getCcType();
		
		if(!is_numeric($data)) {
			$data = $info->decrypt($data);
		}
		
		return $data;
	}

	public function getAccountBankname()
	{
        $bankName = Mage::helper('debit')->getBankByBlz($this->getAccountBLZ());
        if ($bankName == null) {
            $bankName = Mage::helper('debit')->__('not available');
        }
        return $bankName;
	}

	/**
     * Encrypt data for mail
     *
     * @param   string $data
     * @return  string
     */
	public function maskString($data)
	{
		$crypt = str_repeat('*', strlen($data)-3) . substr($data,-3);
		return $crypt;
	}
}

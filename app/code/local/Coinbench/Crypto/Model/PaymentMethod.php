<?php
class Coinbench_Crypto_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'crypto';
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = false;
	protected $_canUseForMultishipping  = false;
	protected $_formBlockType = 'crypto/form_currency';
	protected $_infoBlockType = 'crypto/info_payment';


	public function assignData($data)
	    {
		if (!($data instanceof Varien_Object)) {
		    $data = new Varien_Object($data);
		}
		$info = $this->getInfoInstance();
		$info->setCryptoCurrency($data->getCryptoCurrency());
		$info->setCryptoAmount(Mage::helper('crypto')->getCoinQuotes($data->getCryptoCurrency()));
		return $this;
	    }
	 
	 
	    public function validate()
	    {
		parent::validate();
	 
		$info = $this->getInfoInstance();
	 
		$currency = $info->getCryptoCurrency();
		if(empty($currency)){
		    $errorCode = 'invalid_data';
		    $errorMsg = $this->_getHelper()->__('No crypto-currency selected.');
		}
	 
		if(isset($errorMsg)){
		    Mage::throwException($errorMsg);
		}
		return $this;
	    }

}

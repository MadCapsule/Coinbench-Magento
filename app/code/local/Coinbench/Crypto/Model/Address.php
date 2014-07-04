<?php
class Coinbench_Crypto_Model_Address extends Mage_Core_Model_Abstract 
{	
	
	protected function _construct()
    	{
		$this->_init('crypto/address');

		//TODO: Move token method call here, but remember this model is used to get transactions by order_id in the Success block

	}  	

	function getFromPool($token, $currency, $value){

		$address_request = array(
			'amount'        => $value,
			'reference'     => 'test-1',
			'currency'	=> $currency
		);

		return Mage::helper('crypto')->request('getnewaddress', $address_request, $token);
		
	}	

	function verify($token, $currency, $address, $value){

		$confirmation_request = array(
			'address'       => $address,
			'amount'	=> $value
		);

		return Mage::helper('crypto')->request('getconfirmations', $confirmation_request, $token);
		
	}		
}

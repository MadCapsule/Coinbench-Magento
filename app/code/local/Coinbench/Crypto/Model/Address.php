<?php
class Coinbench_Crypto_Model_Address extends Mage_Core_Model_Abstract 
{	
	
    protected function _construct()
    {
        $this->_init('crypto/address');
    }  	

    function getFromPool($token, $currency, $value){

	$address_request = array(
			'amount'        => $value,
			'reference'     => 'test-1',
			'currency'	=> $currency
	);

	return Mage::helper('crypto')->request('getnewaddress', $address_request, $token);
		
    }			
}

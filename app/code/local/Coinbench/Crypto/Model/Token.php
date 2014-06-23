<?php
class Coinbench_Crypto_Model_Token extends Mage_Core_Model_Abstract 
{
    function obtain(){

	$authentication_details = array(
			'username' => Mage::getStoreConfig('payment/crypto/username'),
			'client_id'     => Mage::getStoreConfig('payment/crypto/clientid'),
			'secret'     => Mage::getStoreConfig('payment/crypto/clientsecret'),
			'code'     => Mage::getStoreConfig('payment/crypto/clientcode')
	);

	return Mage::helper('crypto')->request('authorise', $authentication_details);
		
    }
}

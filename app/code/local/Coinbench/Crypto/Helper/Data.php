<?php
class Coinbench_Crypto_Helper_Data extends Mage_Core_Helper_Abstract
{	

	const _COINBENCH_API_URL = 'https://coinbench.io/api/';
	const _COINBENCH_RATES_URL = 'https://coinbench.io/rates/rates';


	public function getCoinQuotes($coin = null)
	{

		$quotes = array();
		$available_currencies = array();

		if(Mage::getStoreConfig('payment/crypto/coins') && is_null($coin)){

			$available_currencies = explode(',', Mage::getStoreConfig('payment/crypto/coins'));

		}elseif(!is_null($coin)){

			$available_currencies[] = $coin;

		}

		// TODO: get base currency code for current store locale 
		$base_currency = 'gbp';

		if(!empty($available_currencies)){

			if(is_null($rates = $this->getRates())){
				return $quotes;
			}

			foreach($available_currencies as $currency){

				$quote = $rates->{strtolower($currency)}->{$base_currency};

				if($quote){
					$quotes[] = array('code'=>$currency,'quote'=>$quote);
				}

			}
		}

		return $quotes;
	
	}
	
	public function getExtensionVersion()
	{
		return (string) Mage::getConfig()->getNode()->modules->Coinbench_Crypto->version;
	}

	public function coinbench($resource, $postfields = null, $token = null){

		return $this->request(self::_COINBENCH_NEW_ADDRESS_URI.$resource, $postfields, $token);
		
	}

	public function getRates(){


		try{
			Zend_Loader::loadClass('Zend_Http_Client'); 

		}catch(Zend_Http_Client $e) {
			throw new Exception($this->__('Unable to load Zend_Http_Client.'));
		}

		$feed = new Zend_Http_Client(self::_COINBENCH_RATES_URL);
		$response = $feed->request('GET');

		if($response->isError()){
			return null;
		}

		return json_decode($response->getBody());

	}

	/*
		TODO: Use Zend_Http_Client for this in the same way getRates works above and reduce code reuse
	*/
	public function request($uri, $postfields = null, $token = null)
	{
		Mage::log("JSON: ".json_encode($postfields),null,'coinbench.log');	

		$username = Mage::getStoreConfig('payment/crypto/username');
		$ch = curl_init();
		$data = array();
	
		if(is_null($token)){
			$headers = array(
				"username: {$username}",
				'Content-Type: application/json',
			);
		}else{
			$headers = array(
				 "Authorization: Bearer {$token}",
				 "username: {$username}",
				 "currency: bitcoin",
				 "Content-Type: application/json",
			);
		}	
		Mage::log("header: ".print_r($headers, true),null,'coinbench.log');	
		$curl_url = 'https://coinbench.io/api/'.$uri;
		Mage::log("url:: ".$curl_url,null,'coinbench.log');	
		curl_setopt($ch, CURLOPT_URL, $curl_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if(!is_null($postfields)){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postfields));
		}			
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		//if(Mage::getStoreConfig('payment/crypto/verify')){
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//}

		$response = curl_exec($ch);
		
		if (false === $response) {
			curl_close($ch);
			return false;
		}

		$response_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$responseBody = json_decode($response);
		curl_close($ch);
		$responseBod =  (array) $responseBody;
		Mage::log("response body: ".print_r($responseBod, true),null,'coinbench.log');	
		Mage::log("response code:".$response_code,null,'coinbench.log'); 
		if (200 !== $response_code) {
			return array("error"=>$responseBod['error_description']);
		}
			
		if(!empty($responseBody->response)){
			$decoded_json = (array) $responseBody->response;
			Mage::log("JSON Response: ".print_r($decoded_json, true),null,'coinbench.log');				
			return $decoded_json;
		}else{
			return;
		}
		
			
			
			
	}	
}


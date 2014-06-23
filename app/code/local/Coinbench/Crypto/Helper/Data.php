<?php
class Coinbench_Crypto_Helper_Data extends Mage_Core_Helper_Abstract
{	

	//define URL https://coinbench.io/api/token
	
	public function getExtensionVersion()
	{
		return (string) Mage::getConfig()->getNode()->modules->Coinbench_Crypto->version;
	}

	public function request($resource, $postfields = null, $token = null, $method = null)
	{
		Mage::log("JSON: ".json_encode($postfields),null,'coinbench.log');	

		$ch = curl_init();
		$data = array();
			
		if(!is_null($token)){
			$headers = array(
				"username: {$token}",
				'Content-Type: application/json',
			);
		}else{
			$headers = array('Content-Type: application/json');
		}	
			
		$authenticationUrl = 'https://coinbench.io/api/token';
		curl_setopt($ch, CURLOPT_URL, $authenticationUrl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if(!is_null($postfields)){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postfields));
		}
		if(!is_null($method) && $method=='p'){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
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
			$decoded_json = $responseBody->response;
			Mage::log("Query product b: ".print_r($decoded_json, true),null,'coinbench.log');				
			return $decoded_json;
		}else{
			return;
		}
		
			
			
			
	}	
}


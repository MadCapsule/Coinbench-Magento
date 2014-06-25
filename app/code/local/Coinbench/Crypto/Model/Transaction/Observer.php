<?php
class Coinbench_Crypto_Model_Transaction_Observer {

	public function assignAddress($observer) {

		if(Mage::registry('sales_order_save_commit_after_executed')){
			return;
		}

		Mage::log("Coinbench active");

		Mage::register('sales_order_save_commit_after_executed', true); 
		$_order = $observer->getEvent()->getOrder();
		$increment_id = $_order->getIncrementId();
		$order_id = $observer->getEvent()->getOrder()->getId();

		$transaction_data = array('order_id'=>$increment_id);

		$token = Mage::getModel('crypto/token')->obtain();
		if(!empty($token['error'])){		
			$transaction_data['message'] = $token['error'];
			$transaction_data['status'] = 0;
			Mage::log("Order ".$increment_id." no Coibench API token obtained. Error: ".$token['error']);
		}

		$address = Mage::getModel('crypto/address')->getFromPool($token['token'], 'btc', '31.20902000');
		if(!empty($address['error']) && empty($transaction_data['message'])){
			$transaction_data['message'] = $address['error'];
			$transaction_data['status'] = 0;
		}elseif(!empty($address['address'])){
			$transaction_data['address'] = $address['address'];
			$transaction_data['status'] = 1;
		}

		try{
			$transaction = Mage::getModel('crypto/transaction')->setData($transaction_data)->save();
		}catch (Exception $e) {
			Mage::log($e,null,'coinbench.log');
	       	} 			
	}

}

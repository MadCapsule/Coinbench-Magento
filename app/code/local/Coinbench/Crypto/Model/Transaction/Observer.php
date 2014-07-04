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

	public function verification(){

		Mage::log("Coinbench verification process started");

		$token = Mage::getModel('crypto/token')->obtain();

		if(!empty($token['error'])){		
			Mage::log("Coinbench can't verify transactions because there is no API token.");
		}

     		$transits = Mage::getModel("crypto/transaction")->getCollection();
    		$transits->addFieldToFilter('status', array('like' => '1'));

    		$transactions = $transits->getItems();
		foreach($transactions as $transaction){

			//if not verified and created date more than 1 hour ago, cancel.

			// https://coinbench.io/api/getconfirmations
			// "address":"125bvWgjBRmzoY1cEExFdiP5DsZQExb3sq","amount":"0.01010000"

			$order = Mage::getModel('sales/order')->loadByIncrementId($transaction['order_id']);

			//$verified = Mage::getModel('crypto/address')->verify($token['token'], 'btc', '31.20902000');

			if($order->getState()!=Mage::getStoreConfig('payment/crypto/order_status')){
				continue;
			}

			//check if order save does an exception

			//add order note

			if(isset($verified)){
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
			}else{
				$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
			}

			try{
				$order->save();
			}catch (Exception $e) {
				//log exception
			}


		}


	}

}

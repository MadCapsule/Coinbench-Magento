<?php
class Coinbench_Crypto_Model_Transaction_Observer {

	public function assignAddress($observer) {

		if(Mage::registry('sales_order_save_commit_after_executed')){
			return;
		}

		Mage::log("Coinbench active", null, 'coinbench.log');

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

		$address = Mage::getModel('crypto/address')->getFromPool($token['token'], $_order->getPayment()->getCryptoCurrency(), $_order->getPayment()->getCryptoAmount());
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

		Mage::log("Coinbench verification process started", null, 'coinbench.log');

		$token = Mage::getModel('crypto/token')->obtain();

		if(!empty($token['error'])){		
			return Mage::log("Coinbench can't verify transactions because there is no API token.", null, 'coinbench.log');
		}

     		$transits = Mage::getModel("crypto/transaction")->getCollection();
    		$transits->addFieldToFilter('status', array('like' => '1'));

    		$transactions = $transits->getItems();
		foreach($transactions as $transaction){

			//TODO: if not verified and created date more than 1 hour ago, cancel.

			$order = Mage::getModel('sales/order')->loadByIncrementId($transaction['order_id']);
				Mage::log('order: '.$transaction['order_id'].' state: '.$order->getState().' seting: '.Mage::getStoreConfig('payment/crypto/order_status'), null, 'coinbench.log');
			/*if($order->getState()!=Mage::getStoreConfig('payment/crypto/order_status')){
				continue;
			}*/

			if($order->getState()!='new'){
				continue;
			}

			$verified = Mage::getModel('crypto/address')->verify($token['token'], $order->getPayment()->getCryptoCurrency(), $order->getPayment()->getCryptoAmount());
			Mage::log("Tried to verify transaction: ".$transactonp['order_id']." Got response: ".print_r($verified),null,'coinbench.log');				


/*
    [response] => stdClass Object
        (
            [code] => 200
            [confirmations] => 
            [amount] => 0.00000000
        )
*/

			if(isset($verified)){
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
			}else{
				$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
			}

			try{
				$order->save();

			}catch (Exception $e) {
				Mage::log($e, null, 'coinbench.log');
			}

			//update coinbench table
			//add order note


		}


	}

}

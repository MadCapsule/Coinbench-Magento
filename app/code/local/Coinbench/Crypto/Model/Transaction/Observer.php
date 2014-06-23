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
			try{
				$transaction = Mage::getModel('crypto/transaction')->setData($transaction_data)->save();
			}catch (Exception $e) {
				Mage::log($e,null,'coinbench.log');
	       		} 
			Mage::log("Order ".$increment_id." no Coibench API token obtained. Error: ".$token['error']);
			return;
		}				
	}

}

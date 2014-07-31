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
			$transaction_data['status'] = 'failed';
			Mage::log("Order ".$increment_id." no Coibench API token obtained. Error: ".$token['error']);
		}

		$address = Mage::getModel('crypto/address')->getFromPool($token['token'], $_order->getPayment()->getCryptoCurrency(), $_order->getPayment()->getCryptoAmount());
		if(!empty($address['error']) && empty($transaction_data['message'])){
			$transaction_data['message'] = $address['error'];
			$transaction_data['status'] = 'failed';
		}elseif(!empty($address['address'])){
			$transaction_data['address'] = $address['address'];
		}

		try{
			$transaction = Mage::getModel('crypto/transaction')->setData($transaction_data)->save();
		}catch (Exception $e) {
			Mage::log($e,null,'coinbench.log');
	       	} 

		if(!empty($transaction_data['address'])){
			$this->emailConfirmation($transaction_data['address'], $_order->getPayment()->getCryptoCurrency(), $_order->getPayment()->getCryptoAmount(), $_order->getCustomerEmail());
		}			
	}		

	public function verification(){

		Mage::log("Coinbench verification process started", null, 'coinbench.log');

		$token = Mage::getModel('crypto/token')->obtain();

		if(!empty($token['error'])){		
			return Mage::log("Coinbench can't verify transactions because there is no API token.", null, 'coinbench.log');
		}

     		$transits = Mage::getModel("crypto/transaction")->getCollection();
    		$transits->addFieldToFilter('status', array('like' => 'pending'));

    		$transactions = $transits->getItems();
		foreach($transactions as $transaction){

			$order_updated = false;
			$transaction_status = 1;

			$order = Mage::getModel('sales/order')->loadByIncrementId($transaction['order_id']);
			Mage::log('Order: '.$transaction['order_id'].' state: '.$order->getState().' seting: '.Mage::getStoreConfig('payment/crypto/order_status'), null, 'coinbench.log');
			/*if($order->getState()!=Mage::getStoreConfig('payment/crypto/order_status')){
				continue;
			}*/

			if($order->getState()!='new'){
				//continue;
			}

			$verified = Mage::getModel('crypto/address')->verify($token['token'], $order->getPayment()->getCryptoCurrency(), $transaction['address'], $order->getPayment()->getCryptoAmount());			
			Mage::log("Confirmations(".Mage::getStoreConfig('payment/crypto/verifications')."): ".$verified['confirmations']." Amount: ".$verified['amount'], null, 'coinbench.log');

			if(!empty($verified['error'])){
				continue;
			}

			//TODO: update transaction statuses based on the below outcomes
			if($verified['confirmations']>=Mage::getStoreConfig('payment/crypto/verifications') && $verified['amount']==$order->getPayment()->getCryptoAmount()){
				
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
				$transaction_status = 'verified';

			}elseif(strtotime($transaction['created']) <= strtotime('-1 hour')){

				$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
				$transaction_status = 'expired';
			}

			try{
				$order->save();
				$order_updated = true;

			}catch (Exception $e) {
				Mage::log($e, null, 'coinbench.log');
			}

			//update coinbench table
			if($order_updated){
	
		    		$update_transaction = Mage::getModel('crypto/transaction');
				$update_transaction->setTransactionId($transaction['transaction_id']);
				$update_transaction->setStatus($transaction_status);
				$update_transaction->save();

			}

			//add order note


		}


	}

	private function emailConfirmation($address, $currency, $amount, $email){

		$emailTemplate  = Mage::getModel('core/email_template')
					->loadDefault('coinbench_transaction_template');	

		/*$order = new Mage_Sales_Model_Order();
		$incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order->loadByIncrementId($incrementId);*/

		$emailTemplateVariables = array();
		$emailTemplateVariables['address'] = $address;
		$emailTemplateVariables['currency'] = $currency;
		$emailTemplateVariables['amount'] = $amount;
/*
		$customer_name = "".$order['customer_firstname']." ".$order['customer_lastname']."";

		if(Mage::getStoreConfig('returndiscounts/settings/discountype')==1)
		{
			$em_subject = $amount."%";
		}else{
			$em_subject = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().$amount;
		}				
*/	
		$emailTemplate->setTemplateSubject('Re: Your '.$currency.' Payment');

		$storeEmail = Mage::getStoreConfig('trans_email/ident_sales/email');
		$storeContact = Mage::getStoreConfig('trans_email/ident_sales/name'); 
		$emailTemplate->setSenderEmail($storeEmail);
		$emailTemplate->setSenderName($storeContact);

		$emailTemplate->send($email, null, $emailTemplateVariables);
		return;


	}

}

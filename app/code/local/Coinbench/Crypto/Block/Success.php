<?php
class Coinbench_Crypto_Block_Success extends Mage_Core_Block_Template
{
	protected function _construct()
	{
		parent::_construct();
	}

	public function transactionDetail()
	{

		$order_id = Mage::getSingleton('checkout/session')->getLastOrderId();

		if(!$order = Mage::getModel('sales/order')->load($order_id)){
			return array();
		}

		$address = Mage::getModel('crypto/address')->load($order->getIncrementId());

		return array('increment_id'=>$order->getIncrementId(), 'crypto_amount'=>$order->getPayment()->getCryptoAmount(), 'crypto_currency'=>$order->getPayment()->getCryptoCurrency(),'crypto_address'=>$address->getAddress());

	}
}

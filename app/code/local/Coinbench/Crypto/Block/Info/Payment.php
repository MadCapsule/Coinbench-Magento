<?php
class Coinbench_Crypto_Block_Info_Payment extends Mage_Payment_Block_Info
{
	protected function _prepareSpecificInformation($transport = null)
	{
		if (null !== $this->_paymentSpecificInformation) {
		    return $this->_paymentSpecificInformation;
		}

		$info = $this->getInfo();
		$transport = new Varien_Object();
		$transport = parent::_prepareSpecificInformation($transport);
	        $transport->addData(array(
		    Mage::helper('payment')->__('Currency') => $info->getCryptoCurrency(),
		    Mage::helper('payment')->__('Total') => $info->getCryptoAmount() //This should come from a rates aggrigator
		));

		return $transport;
	}


}

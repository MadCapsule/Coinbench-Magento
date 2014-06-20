<?php
class Coinbench_Crypto_Helper_Data extends Mage_Core_Helper_Abstract
{	
	
	public function getExtensionVersion()
	{
		return (string) Mage::getConfig()->getNode()->modules->Coinbench_Crypto->version;
	}	
}


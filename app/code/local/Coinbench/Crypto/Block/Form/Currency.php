<?php
class Coinbench_Crypto_Block_Form_Currency extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
	$token = Mage::getModel('crypto/token')->obtain();

	if(!empty($token['error'])){		
		$this->setTemplate('crypto/form/apierror.phtml');
	}else{
        	$this->setTemplate('crypto/form/currency.phtml');
	}
    }

}

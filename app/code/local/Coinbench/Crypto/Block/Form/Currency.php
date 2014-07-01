<?php
class Coinbench_Crypto_Block_Form_Currency extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('crypto/form/currency.phtml');
    }

}

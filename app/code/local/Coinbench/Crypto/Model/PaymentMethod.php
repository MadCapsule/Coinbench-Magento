<?php
class Coinbench_Crypto_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'crypto';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;



}

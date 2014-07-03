<?php
class Coinbench_Crypto_Model_Mysql4_Address extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct()
    {
        $this->_init('crypto/address', 'order_id');
    }   
}

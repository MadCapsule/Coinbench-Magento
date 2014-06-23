<?php
class Coinbench_Crypto_Model_Mysql4_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    protected function _construct()
    {
            $this->_init('crypto/transaction');
    }
}

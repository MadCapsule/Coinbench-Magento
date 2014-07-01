<?php
class Coinbench_Crypto_Currencies
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'XBT', 'label'=>Mage::helper('adminhtml')->__('Bitcoin')),
			array('value' => 'LTC', 'label'=>Mage::helper('adminhtml')->__('Litecoin')),
			array('value' => 'XDG', 'label'=>Mage::helper('adminhtml')->__('Dogecoin'))
        );
    }
}

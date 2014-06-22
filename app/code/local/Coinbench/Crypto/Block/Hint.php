<?php
class Coinbench_Crypto_Block_Hint
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'coinbench/system/config/fieldset/hint.phtml';

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }
    
    public function getCryptoVersion()
    {
        return (string) Mage::getConfig()->getNode('modules/Coinbench_Crypto/version');
    }
}

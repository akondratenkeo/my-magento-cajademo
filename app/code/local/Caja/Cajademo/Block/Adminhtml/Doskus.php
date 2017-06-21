<?php 


class Caja_Cajademo_Block_Adminhtml_Doskus extends Mage_Adminhtml_Block_Widget_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_cajademo';
        $this->_headerText = Mage::helper('cajademo')->__('Caja Skus Synchronization...');

        parent::__construct();
        $this->setTemplate('caja_cajademo/doskus.phtml');
    }

    protected function _prepareLayout()
    {
        $this->_addButton('back', array(
            'label'   => Mage::helper('catalog')->__('back'),
            'onclick' => "setLocation('{$this->getUrl('*/*')}')",
            'class'   => 'back'
        ));

        return parent::_prepareLayout();
    }

    public function getSkus()
    {
        return Mage::registry('sync_skus');
    }

}

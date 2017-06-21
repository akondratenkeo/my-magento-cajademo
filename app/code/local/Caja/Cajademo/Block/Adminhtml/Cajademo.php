<?php 


class Caja_Cajademo_Block_Adminhtml_Cajademo extends Mage_Adminhtml_Block_Widget_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_cajademo';
        $this->_headerText = Mage::helper('cajademo')->__('Caja-Dashboard');

        parent::__construct();
        $this->setTemplate('caja_cajademo/index.phtml');
    }

    protected function _prepareLayout()
    {
        $this->_addButton('doSkus', array(
            'label'   => Mage::helper('catalog')->__('Sync Skus'),
            'onclick' => "setLocation('{$this->getUrl('*/*/doSkus')}')",
            'class'   => 'do_sync'
        ));

        $this->_addButton('doOrders', array(
            'label'   => Mage::helper('catalog')->__('Sync Orders'),
            'onclick' => "setLocation('{$this->getUrl('*/*/doOrders')}')",
            'class'   => 'do_orders'
        ));

        $this->_addButton('doDelete', array(
            'label'   => Mage::helper('catalog')->__('Delete Data'),
            'onclick' => "setLocation('{$this->getUrl('*/*/doDelete')}')",
            'class'   => 'do_delete'
        ));

        return parent::_prepareLayout();
    }

    public function getZoneInfo()
    {
        return Mage::registry('zoneInfo');
    }

    public function getZoneStructure()
    {
        return Mage::registry('zoneStructure');
    }

    public function getMaxColNumber()
    {
        $zones = $this->getZoneStructure();
        $maxCols = 0;

        foreach ($zones as $zone) {
            if ($maxCols < count($zone)) {
                $maxCols = count($zone);
            }
        }

        return $maxCols;
    }

    public function getUrlForZoneStatusRequest()
    {
        return $this->getUrl('adminhtml/cajademo/ajaxGetZoneStatus');
    }

    public function getUrlForOrderLineRequest()
    {
        return $this->getUrl('adminhtml/cajademo/ajaxOrderlineComplete');
    }
}

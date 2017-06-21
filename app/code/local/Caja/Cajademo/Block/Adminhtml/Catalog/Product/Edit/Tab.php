<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 22.02.2016
 * Time: 15:56
 */

class Caja_Cajademo_Block_Adminhtml_Catalog_Product_Edit_Tab extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return $this->__('Locations');
    }

    public function getTabTitle()
    {
        return $this->__('Locations');
    }

    public function isHidden()
    {
        return false;
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/*/locations', array('_current' => true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

}
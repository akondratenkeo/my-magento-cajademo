<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 22.02.2016
 * Time: 16:24
 */

class Caja_Cajademo_Block_Adminhtml_Catalog_Product_Edit_Tab_Locations extends Mage_Adminhtml_Block_Catalog_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('caja_cajademo/catalog/product/edit/tab/location.phtml');
    }

    protected function _prepareForm()
    {
        $product = Mage::registry('product');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('locations', array('legend' => Mage::helper('cajademo')->__('Locations')));

        $aLocationValues = Mage::helper('cajademo')->_createSkusLocationsList($product->getSku());
        $binType = Mage::helper('cajademo')->_getBinType();

        $fieldset->addField('p_alocation', 'select', array(
            'label' => Mage::helper('cajademo')->__('Available location'),
            'title'     => Mage::helper('cajademo')->__('Available location'),
            'name'      =>'p_alocation',
            'values'    => $aLocationValues
        ));

        $fieldset->addField('p_qty', 'text', array(
            'label' => Mage::helper('cajademo')->__('Qty'),
            'title' => Mage::helper('cajademo')->__('Qty'),
            'name'  => 'p_qty'
        ));

        $fieldset->addField('p_sku', 'hidden', array(
            'name'  => 'p_sku',
            'value'    => $product->getSku()
        ));

        $fieldset->addField('p_bin_type', 'hidden', array(
            'name'  => 'p_bin_type',
            'value'    => $binType
        ));

        $fieldset->addField('add_bin', 'button', array(
            'value' => Mage::helper('cajademo')->__('Add Bin'),
            'onclick' => 'caja_setAvailableLocation(\''. $this->getUrl('adminhtml/catalog_product/ajaxNewBinCreation') .'\')'
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
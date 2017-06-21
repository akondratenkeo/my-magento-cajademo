<?php

require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').'/Catalog/ProductController.php');

class Caja_Cajademo_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    /**
     * Get custom products grid and serializer block
     */
    public function locationsAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog_product_edit_tab_locations');//->setProductsCustom($this->getRequest()->getPost('product_locations', null));
        $this->renderLayout();
    }

    public function ajaxNewBinCreationAction() {

        $response = array();

        if ($postData = $this->getRequest()->getPost()) {

            $bins = Mage::getModel('cajademo/rest_bins');
            $insert_bin = &Mage::helper('cajademo')->prepareBinsArrayForCreate($postData);

            if (!empty($insert_bin)) {
                $restResponse = $bins->createBin($insert_bin);
            }
        }

        if (is_array($restResponse) && count($restResponse) > 0) {
            $response['error'] = $restResponse;
            $response = json_encode($response);
        } else {
            $response = 'Bin successfully created';
        }

        $this->getResponse()->setBody($response);
    }
}

<?php 


class Caja_Cajademo_Adminhtml_CajademoController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title(Mage::helper('cajademo')->__('CajaDemo Module'));

        $zoneName = Mage::getStoreConfig('general/cajademo/zone_id');

        $zones = Mage::getModel('cajademo/rest_zones');
        $zoneInfo = $zones->getZonesByName($zoneName);

        //$zones = Mage::getModel('cajademo/rest_zonesd');
        //$zoneInfo = $zones->getZonesByName($zoneName);

        if (is_array($zoneInfo) && !array_key_exists('errorCode', $zoneInfo)) {
            $zoneStructure  = $zones->getZonesNameStructure($zoneName);

            $orientation = Mage::helper('cajademo')->getZoneOrientation($zoneStructure['frames']);
            $structure = array();

            foreach ($zoneStructure['frames'] as $frame) {
                if (!array_key_exists($frame['z'], $structure)) {
                    $structure[$frame['z']] = array();
                }
                foreach ($structure as $key => $value) {
                    if ($key == $frame['z']) {
                        if ($orientation == 'vertical') {
                            $structure[$key][$frame['x']] = $frame;
                        } else {
                            $structure[$key][$frame['y']] = $frame;
                        }
                    }
                }
            }
            $sortStructure = &Mage::helper('cajademo')->sortZone($structure);
            Mage::register('zoneStructure', $sortStructure);
        }

        Mage::register('zoneInfo', $zoneInfo);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function doSkusAction()
    {
        $products = Mage::getModel('cajademo/inner_products');
        $inner_items = $products->items();

        $skus = Mage::getModel('cajademo/rest_skus');
        $skus_items = $skus->getSkus();

        $insert_collection = &Mage::helper('cajademo')->prepareSkusArrayForCreate($inner_items, $skus_items);

        if ($insert_collection) {
            foreach ($insert_collection as $action => $items) {
                if ($action == 'create') {
                    if (count($items['skus']) > 0) {
                        $skus->createSkus($items);
                    }
                } elseif ($action == 'update_rest') {
                    if (count($items['skus']) > 0) {
                        $skus->updateSkus($items);
                    }
                } elseif ($action == 'update_inner') {
                    if (count($items['skus']) > 0) {
                        $products->updateItems($items);
                    }
                }
            }
        }

        $this->_redirectReferer();
    }

    public function doOrdersAction()
    {
        $inner_orders = $this->_getInnerOrders();
        $orders = Mage::getModel('cajademo/rest_orders');

        $iOrders_model = Mage::getModel('cajademo/inner_orders');

        if ($inner_orders && !empty($inner_orders)) {

            $rest_orders = $orders->getOrdersFilter(Mage::helper('cajademo')->_getOrdersIds($inner_orders));
            $insert_collection = &Mage::helper('cajademo')->prepareOrdersArrayForCreate($inner_orders, $rest_orders['orders']);

            if ($insert_collection) {
                foreach ($insert_collection as $action => $items) {
                    if ($action == 'create') {
                        if (count($items) > 0 && is_array($items)) {
                            foreach ($items as $item) {
                                $orders->createOrders($item);
                            }
                        }
                    } elseif ($action == 'update_inner') {
                        if (count($items) > 0 && is_array($items)) {
                            $iOrders_model->updateOrders($items);
                        }
                    }
                }
            }
        }

        $this->_redirectReferer();
    }

    public function doDeleteAction()
    {
        $this->_redirectReferer();
    }

    public function ajaxGetZoneStatusAction() {

        $response = array();

        $zoneName = Mage::getStoreConfig('general/cajademo/zone_id');
        $zones = Mage::getModel('cajademo/rest_zones');
        //$zones = Mage::getModel('cajademo/rest_zonesd');

        $zoneStatus = $zones->getZonesNameStatus($zoneName);

        if (is_array($zoneStatus) && array_key_exists('errorCode', $zoneStatus)) {
            $response['error'] = $zoneStatus;
            $response = json_encode($response);
        } else {
            $zoneStatus = Mage::helper('cajademo')->prepareZoneStatusResponse($zoneStatus);
            $response = json_encode($zoneStatus);
        }

        $this->getResponse()->setBody($response);
    }

    public function ajaxOrderlineCompleteAction() {

        $response = array();

        if ($postData = $this->getRequest()->getPost()) {

            $orders = Mage::getModel('cajademo/rest_orders');
            $line_data = Mage::helper('cajademo')->prepareOrderlineArrayForUpdate($postData);
            $line_id = $postData['orderline_id'];

            if (!empty($line_data)) {
                $restResponse = $orders->completeOrderLine($line_id, $line_data);
            }
        }

        if (is_array($restResponse) && array_key_exists('errorCode', $restResponse)) {
            $response['error'] = $restResponse;
            $response = json_encode($response);
        } else {
            $response = 'Done...';
        }

        $this->getResponse()->setBody($response);
    }

    protected function _getInnerOrders()
    {
        $result = array();
        $states = array("new", "processing");

        $orders = Mage::getModel('cajademo/inner_orders');

        foreach ($states as $state) {
            $result = array_merge($result, $orders->items(array("state" => $state)));
        }

        return $result;
    }

    public function doSomeBgAction()
    {
        if ($postData = $this->getRequest()->getPost()) {

            $model = Mage::getSingleton('checkout/agreement');
            $model->setData($postData);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('checkout')->__('The condition has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('checkout')->__('An error occurred while saving this condition.'));
            }

            Mage::getSingleton('adminhtml/session')->setAgreementData($postData);
            $this->_redirectReferer();
        }
    }
}

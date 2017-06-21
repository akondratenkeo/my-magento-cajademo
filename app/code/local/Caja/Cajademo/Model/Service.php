<?php

class Caja_Cajademo_Model_Service extends Mage_Catalog_Model_Abstract
{
    public function syncSkus()
    {
        $report = array();

        $products = Mage::getModel('cajademo/inner_products');
        $inner_items = $products->items();

        $skus = Mage::getModel('cajademo/rest_skus');
        $skus_items = $skus->getSkus();

        $insert_collection = &Mage::helper('cajademo')->prepareSkusArrayForCreate($inner_items, $skus_items);

        if ($insert_collection) {
            foreach ($insert_collection as $action => $items) {
                if ($action == 'create') {
                    if (count($items['skus']) > 0) {
                        //$skus->createSkus($items);
                    }
                    $report[] = 'Created: '. count($items['skus']);
                } elseif ($action == 'update_rest') {
                    if (count($items['skus']) > 0) {
                        //$skus->updateSkus($items);
                    }
                    $report[] = 'Updated (rest): '. count($items['skus']);
                } elseif ($action == 'update_inner') {
                    if (count($items['skus']) > 0) {
                        //$products->updateItems($items);
                    }
                    $report[] = 'Updated (inner): '. count($items['skus']);
                }
            }
        }

        $this->_log('sync-skus.log', $report);
    }

    public function syncOrders()
    {
        $report = array();

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
                                //$orders->createOrders($item);
                            }
                        }
                        $report[] = 'Created: '. count($items);
                    } elseif ($action == 'update_inner') {
                        if (count($items) > 0 && is_array($items)) {
                            //$iOrders_model->updateOrders($items);
                        }
                        $report[] = 'Updated (inner): '. count($items);
                    }
                }
            }
        }

        $this->_log('sync-orders.log', $report);
    }

    private function _getInnerOrders()
    {
        $result = array();
        $states = array("new", "processing");

        $orders = Mage::getModel('cajademo/inner_orders');

        foreach ($states as $state) {
            $result = array_merge($result, $orders->items(array("state" => $state)));
        }

        return $result;
    }

    private function _log($file, $report)
    {
        $log = fopen($file, 'a');
        fwrite($log, date("Y-m-d H:i:s").PHP_EOL);
        fwrite($log, '--------'.PHP_EOL);

        if (count($report) > 0) {
            foreach ($report as $val) {
                fwrite($log, $val.PHP_EOL);
            }
        } else {
            fwrite($log, '- No changes detected'.PHP_EOL);
        }

        fwrite($log, ''.PHP_EOL);
        fwrite($log, ''.PHP_EOL);
        fclose($log);
    }
}

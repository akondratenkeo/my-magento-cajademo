<?php

class Caja_Cajademo_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function &prepareSkusArrayForCreate($needle, $haystack)
    {
        $collection = array(
            "create"        => array(),
            "update_rest"   => array(),
            "update_inner"  => array()
        );

        if(count($needle) == 0 || count($haystack) == 0) {
            return false;
        }

        foreach ($needle as $pos => $item) {
            foreach ($haystack['skus'] as $key => $value) {
                if($item['skuCode'] == $value['skuCode']) {
                    if ($this->_compareArrayForUpdate($this->_formatSkusArray($item, 'rest'), $this->_formatSkusArray($value, 'rest'))) {
                        $collection['update_rest'][] = $this->_formatSkusArray($item, 'rest');
                    }
                    if ($this->_compareArrayForUpdate($this->_formatSkusArray($item, 'inner'), $this->_formatSkusArray($value, 'inner'))) {
                        $item['totalQuantity'] = $value['totalQuantity'];
                        $item['reservedQuantity'] = $value['reservedQuantity'];
                        $collection['update_inner'][] = $item;
                    }
                    unset($needle[$pos]);
                }
            }
        }

        foreach ($needle as $val) {
            $collection['create'][] = $this->_formatSkusArray($val, 'rest');
        }

        $return = array(
            "create"        => array("skus" => array_values($collection['create'])),
            "update_rest"   => array("skus" => array_values($collection['update_rest'])),
            "update_inner"  => array("skus" => array_values($collection['update_inner']))
        );

        return $return;
    }

    public function &prepareOrdersArrayForCreate($needle, $haystack)
    {
        $collection = array(
            "create"        => array(),
            "update_inner"  => array()
        );

        if (count($haystack) > 0) {
            foreach ($needle as $pos => $item) {
                foreach ($haystack as $key => $value) {
                    if($item['externalId'] == $value['externalId']) {
                        if ($value['status'] == 'PROCESSED' && $this->_allOrderlinesComplete($value['skus'])) {
                            $collection['update_inner'][] = $item;
                        }
                        unset($needle[$pos]);
                    }
                }
            }
        }

        $collection['create'] = $needle;
        $return = array(
            "create"        => array_values($collection['create']),
            "update_inner"  => array_values($collection['update_inner'])
        );

        return $return;
    }

    public function &prepareBinsArrayForCreate($data)
    {
        $create = array();
        $p_location = explode('-', $data['product_location']);

        $create['barcodeValue'] = dechex(time());
        $create['x'] = $p_location[0];
        $create['y'] = $p_location[1];
        $create['z'] = $p_location[2];

        $create['binTypeId'] = $data['product_bin_type'];
        $create['isAvailable'] = ($p_location[3] != 0) ? true : false;

        $create['binSKUs'] = array(
            array(
                "binSKU" => $data['product_sku'],
                "binQty" => $data['product_qty'],
                "binMinQty" => 5
            )
        );

        return $create;
    }

    public function prepareZoneStatusResponse($data)
    {
        $response = array();
        $status = array(
            "ALLOCATED" => array(
                "color" => "#ea9999",
                "status" => "Allocated"
            ),
            "PENDING_ARRIVAL" => array(
                "color" => "#ffff00",
                "status" => "Pending arrival"
            ),
            "PENDING_PICKING" => array(
                "color" => "#b6d7a8",
                "status" => "Pending picking"
            )
        );

        foreach ($data['locationStates'] as $key => $value) {

            $value["cellId"] = 'zone-'. $value['locationX'] .'-'. $value['locationY'] .'-'. $value['locationZ'];

            switch ($value['status']) {
                case 'ALLOCATED':
                    $value["body"]  = '<p>Location busy</p>';
                    break;

                case 'PENDING_ARRIVAL':
                    $value["body"]  = '<p>Order: '. $value['orderId'] .'</p>'.
                        '<p>Sku: '. $value['skuId'] .'</p>'.
                        '<p>Status: '. $status[$value['status']]['status'] .'</p>'.
                        '<p>Qty2Pick: '. $value['pickQty'] .'</p>';
                    break;

                case 'PENDING_PICKING':
                    $value["body"]  = '<p>Order: '. $value['orderId'] .'</p>'.
                        '<p>Sku: '. $value['skuId'] .'</p>'.
                        '<p>Status: '. $status[$value['status']]['status'] .'</p>'.
                        '<p>Qty2Pick: '. $value['pickQty'] .'</p>'.
                        '<button class="zone-action-button" onclick="caja_popupShow(\''. $value['orderLineId'] .'\', \''. $value['pickQty'] .'\', \''. $value["cellId"] .'\')">Done</button>';
                    break;
                default:
                    $value["body"] = '';
                    break;
            }

            $value['statusColor'] = $status[$value['status']]['color'];
            $response[$key] = $value;
        }

        return $response;
    }

    public function prepareOrderlineArrayForUpdate($data) {

        $update = array();
        $update['pickedqty'] = $data['orderline_qty'];

        return $update;
    }

    private function _formatSkusArray($source, $type = 'rest') {

        $result = array();

        if ($type == 'rest') {

            $format = array("skuCode", "description", "minQuantityAllowed", "isAvailable");
        } elseif ($type == 'inner') {

            $format = array("totalQuantity", "reservedQuantity");
        } else {

            $format = array("skuCode", "description", "minQuantityAllowed", "isAvailable");
        }

        foreach ($source as $key => $value) {
            if (in_array($key, $format)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    private function _allOrderlinesComplete($skus) {
        $valid_items = 0;

        foreach ($skus as $item) {
            if ($item['status'] == 'COMPLETED') {
                $valid_items++;
            }
        }

        if (count($skus) == $valid_items) {
            return true;
        }
        return false;
    }

    private function _compareArrayForUpdate($needle, $haystack)
    {
        $compareResult = $this->_array_diff($needle, $haystack);
        if (!empty($compareResult) && is_array($compareResult)) {
            return true;
        } else {
            return false;
        }
    }

    public function _createSkusLocationsList($code) {

        $options = array();

        $skus = Mage::getModel('cajademo/rest_skus');
        $sku_locations = $skus->getSkusLocationsByCode($code);

        if (count($sku_locations['frames']) > 0) {
            foreach ($sku_locations['frames'] as $location) {
                $value = $location['x'] .'-'. $location['y'] .'-'. $location['z'] .'-'. $location['isAvailable'];
                $label = $location['locationArea'] .'-'. $location['x'] .'-'. $location['y'] .'-'. $location['z'];
                $options[$value] = $label;
            }
        }

        return $options;
    }

    public function _getOrdersIds($orders) {

        $ids = array();

        foreach ($orders as $key => $value) {
            $ids[] = $value['externalId'];
        }

        $return = array("ids" => array_values($ids));

        return $return;
    }

    public function _getBinType() {

        $bin_type = '';

        $bins = Mage::getModel('cajademo/rest_bins');
        $bins_types = $bins->getBinsTypes();

        if (count($bins_types['binTypes']) > 0) {
            $bin_type = $bins_types['binTypes'][0]['id'];
        }

        return $bin_type;
    }

    private function _array_diff($needle, $haystack) {

        $diff = array();

        foreach ($needle as $key => $value) {
            if ($haystack[$key] != $value) {
                $diff[] = $key;
            }
        }

        return $diff;
    }

    public function getZoneOrientation($zones) {
        $x = array();
        $y = array();

        foreach ($zones as $key => $value) {
            $x[] = $value['x'];
            $y[] = $value['y'];
        }

        if(count(array_unique($x)) == 1) {
            return 'horizontal';
        } elseif (count(array_unique($y)) == 1) {
            return 'vertical';
        }

        return false;
    }

    public function &sortZone($zones) {
        $sorted = array();

        foreach ($zones as $key => $value) {
            ksort($value);
            $sorted[$key] = $value;
        }

        krsort($sorted);
        return $sorted;
    }
}

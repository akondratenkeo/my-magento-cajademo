<?php

class Caja_Cajademo_Model_Rest_Zonesd extends Caja_Cajademo_Model_Rest_Abstract
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getZonesByName($zName)
    {
        $path = '/zones'.'/'.$zName;
        $info = array(
            "name" => "PickingZone1",
            "description" => "Picking station zone 1"
        );

        return $info;
    }

    public function getZonesNameStatus($zName)
    {
        $path = '/zones'.'/'.$zName.'/status';
        $status = array(
            "locationStates" => array(
                0 => array(
                    "status" => "ALLOCATED",
                    "locationX" => 2615,
                    "locationY" => 300,
                    "locationZ" => 1500,
                    "skuId" => "a3WsA2q",
                    "orderId"=> "a3WsA2q",
                    "bin"=> array(
                        "barcodeValue" => "a3WsA2q",
                        "x" => 0,
                        "y" => 0,
                        "z" => 0,
                        "binTypeId" => "a3WsA2q",
                        "isAvailable" => true,
                        "binSKUs" => array(
                            "binSKU" => "hmb0015",
                            "binQty" => 2,
                            "binMinQty" => 5
                        )
                    ),
                    "orderLineId" => "199",
                    "pickQty" => 2
                ),
                1 => array(
                    "status" => "PENDING_ARRIVAL",
                    "locationX" => 3115,
                    "locationY" => 300,
                    "locationZ" => 1100,
                    "skuId" => "a3WsA2q",
                    "orderId"=> "a3WsA2q",
                    "bin"=> array(
                        "barcodeValue" => "a3WsA2q",
                        "x" => 0,
                        "y" => 0,
                        "z" => 0,
                        "binTypeId" => "a3WsA2q",
                        "isAvailable" => true,
                        "binSKUs" => array(
                            "binSKU" => "hmb0015",
                            "binQty" => 2,
                            "binMinQty" => 5
                        )
                    ),
                    "orderLineId" => "200",
                    "pickQty" => 4
                ),
                2 => array(
                    "status" => "PENDING_PICKING",
                    "locationX" => 4115,
                    "locationY" => 300,
                    "locationZ" => 1500,
                    "skuId" => "a3WsA2q",
                    "orderId"=> "a3WsA2q",
                    "bin"=> array(
                        "barcodeValue" => "a3WsA2q",
                        "x" => 0,
                        "y" => 0,
                        "z" => 0,
                        "binTypeId" => "a3WsA2q",
                        "isAvailable" => true,
                        "binSKUs" => array(
                            "binSKU" => "hmb0015",
                            "binQty" => 2,
                            "binMinQty" => 5
                        )
                    ),
                    "orderLineId" => "198",
                    "pickQty" => 9
                )
            )
        );

        return $status;
        //return $this->_client->restGet($path, array("pageNum" => 0, "pageSize" => 99999));
    }

    public function getZonesNameStructure($zName)
    {
        $path = '/zones'.'/'.$zName.'/structure';
        $struct = array(
            "frames" => array(
                0 => array(
                    "x" => 4615,
                    "y" => 300,
                    "z" => 1100,
                    "vacant" => true
                ),
                1 => array(
                    "x" => 3115,
                    "y" => 300,
                    "z" => 1100,
                    "vacant" => true
                ),
                2 => array(
                    "x" => 2615,
                    "y" => 300,
                    "z" => 1500,
                    "vacant" => true
                ),
                3 => array(
                    "x" => 4115,
                    "y" => 300,
                    "z" => 1100,
                    "vacant" => true
                ),
                4 => array(
                    "x" => 3615,
                    "y" => 300,
                    "z" => 1500,
                    "vacant" => true
                ),
                5 => array(
                    "x" => 4615,
                    "y" => 300,
                    "z" => 1500,
                    "vacant" => true
                ),
                6 => array(
                    "x" => 3115,
                    "y" => 300,
                    "z" => 1500,
                    "vacant" => true
                ),
                7 => array(
                    "x" => 2615,
                    "y" => 300,
                    "z" => 1100,
                    "vacant" => true
                ),
                8 => array(
                    "x" => 4115,
                    "y" => 300,
                    "z" => 1500,
                    "vacant" => true
                ),
                9 => array(
                    "x" => 3615,
                    "y" => 300,
                    "z" => 1100,
                    "vacant" => true
                )
            )
        );

        return $struct;
        //return $this->_client->restGet($path, array("pageNum" => 0, "pageSize" => 99999));
    }
}
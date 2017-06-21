<?php

class Caja_Cajademo_Model_Rest_Skus extends Caja_Cajademo_Model_Rest_Abstract
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSkus()
    {
        $path = '/skus';
        return $this->_client->restGet($path, array("pageNum" => 0, "pageSize" => 99999));
    }

    public function createSkus($data)
    {
        $path = '/skus';
        return $this->_client->restPost($path, $data, array("ActionTypes" => "CREATE"));
    }

    public function updateSkus($data)
    {
        $path = '/skus';
        return $this->_client->restPost($path, $data, array("ActionTypes" => "UPDATE"));
    }

    public function deleteSkus($data)
    {
        $data = array(
            "skuCodes" => array(
                "SAEFSVS47-1",
                "SAEFSVS47-3",
            )
        );
        $path = '/skus';
        return $this->_client->restDelete($path, $data);
    }

    public function getSkusByCode($code)
    {
        $path = '/skus'.'/'.$code;
        return $this->_client->get($path);
    }

    public function deleteSkusByCode($code)
    {
        $path = '/skus'.'/'.$code;
        return $this->_client->restDelete($path);
    }

    public function getSkusLocationsByCode($code)
    {
        $path = '/skus'.'/'.$code.'/locations/available';
        return $this->_client->restGet($path, array("pageNum" => 0, "pageSize" => 99999));
    }
}
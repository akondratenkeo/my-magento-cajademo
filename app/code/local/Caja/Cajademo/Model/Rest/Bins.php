<?php

class Caja_Cajademo_Model_Rest_Bins extends Caja_Cajademo_Model_Rest_Abstract
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBins()
    {
        $path = '/bins';
        return $this->_client->restGet($path, array("pageNum" => 0, "pageSize" => 999999));
    }

    public function getBinsTypes()
    {
        $path = '/bins/types';
        return $this->_client->restGet($path, array("pageNum" => 0, "pageSize" => 999999));
    }

    public function createBin($data)
    {
        $path = '/bins';
        return $this->_client->restPost($path, $data);
    }
}
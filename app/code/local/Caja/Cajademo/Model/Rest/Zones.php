<?php

class Caja_Cajademo_Model_Rest_Zones extends Caja_Cajademo_Model_Rest_Abstract
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getZonesByName($zName)
    {
        $path = '/zones'.'/'.$zName;
        return $this->_client->restGet($path);
    }

    public function getZonesNameStatus($zName)
    {
        $path = '/zones'.'/'.$zName.'/status';
        return $this->_client->restGet($path, array("pageNum" => 0, "pageSize" => 99999));
    }

    public function getZonesNameStructure($zName)
    {
        $path = '/zones'.'/'.$zName.'/structure';
        return $this->_client->restGet($path, array("pageNum" => 0, "pageSize" => 99999));
    }
}
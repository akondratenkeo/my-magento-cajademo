<?php

class Caja_Cajademo_Model_Rest_Orders extends Caja_Cajademo_Model_Rest_Abstract
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOrders()
    {
        $path = '/orders';
        return $this->_client->restGet($path, array("pageNum" => 0, "pageSize" => 99999));
    }

    public function getOrdersFilter($data)
    {
        $path = '/orders/filters/ids';
        return $this->_client->restPut($path, $data);
    }

    public function createOrders($data)
    {
        $path = '/orders';
        return $this->_client->restPost($path, $data);
    }

    public function updateOrders($id, $data)
    {
        $path = '/orders'.'/'.$id;
        return $this->_client->restPut($path, $data);
    }

    public function completeOrderLine($id, $data)
    {
        $path = '/orderlines'.'/'. $id .'/complete';
        return $this->_client->restPut($path, $data);
    }
}
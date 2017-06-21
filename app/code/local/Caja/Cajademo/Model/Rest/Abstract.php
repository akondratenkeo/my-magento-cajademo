<?php

abstract class Caja_Cajademo_Model_Rest_Abstract extends Mage_Catalog_Model_Abstract
{
    protected $_uri = 'http://54.68.213.156:8084';

    protected $_client = null;

    public function __construct()
    {
        $this->_client = new Caja_Rest_Client($this->_uri);
    }
}

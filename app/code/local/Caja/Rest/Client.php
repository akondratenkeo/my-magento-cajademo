<?php

class Caja_Rest_Client extends Zend_Rest_Client
{
    protected $enctype = 'application/json';

    public function __construct($uri = null)
    {
        if (!empty($uri)) {
            $this->setUri($uri);
        }
    }

    private function _prepareRest($path)
    {
        // Get the URI object and configure it
        if (!$this->_uri instanceof Zend_Uri_Http) {
            #require_once 'Zend/Rest/Client/Exception.php';
            throw new Zend_Rest_Client_Exception('URI object must be set before performing call');
        }

        $uri = $this->_uri->getUri();

        if ($path[0] != '/' && $uri[strlen($uri)-1] != '/') {
            $path = '/' . $path;
        }

        $this->_uri->setPath($path);

        /**
         * Get the HTTP client and configure it for the endpoint URI.  Do this each time
         * because the Zend_Http_Client instance is shared among all Zend_Service_Abstract subclasses.
         */
        if ($this->_noReset) {
            // if $_noReset we do not want to reset on this request,
            // but we do on any subsequent request
            $this->_noReset = false;
        } else {
            self::getHttpClient()->resetParameters();
        }

        self::getHttpClient()->setUri($this->_uri);
    }

    /**
     * Method _performPost overload
     */
    protected function _performPost($method, $data = null, $params = null)
    {
        $client = self::getHttpClient();
        $client->setHeaders('Content-Type', $this->enctype);
        $data = json_encode($data);

        if (is_string($data)) {
            $client->setRawData($data, $this->enctype);
        } elseif (is_array($data) || is_object($data)) {
            $client->setRawData(json_encode($data), $this->enctype);
        }

        if (!empty($params) && is_array($params)) {
            $client->setParameterGet($params);
        }

        $response = $client->request($method);

        if ($response->getHeader("Content-Type")=="application/json") {
            return json_decode($response->getBody(), true);
        } else {
            return new Zend_Rest_Client_Result($response->getBody());
        }
    }

    public function restGet($path, array $query = null)
    {
        $this->_prepareRest($path);
        $client = self::getHttpClient();
        $client->setConfig(array('timeout' => 100));

        $client->setParameterGet($query);

        $response = $client->request('GET');

        if ($response->getHeader("Content-Type")=="application/json") {
            return json_decode($response->getBody(), true);
        } else {
            return new Zend_Rest_Client_Result($response->getBody());
        }
    }

    public function restPost($path, $data = null, $params = null)
    {
        $this->_prepareRest($path);
        return $this->_performPost('POST', $data, $params);
    }


    public function restPut($path, $data = null, $params = null)
    {
        $this->_prepareRest($path);
        return $this->_performPost('PUT', $data, $params);
    }

    public function restDelete($path, $data = null, $params = null)
    {
        $this->_prepareRest($path);
        return $this->_performPost('DELETE', $data, $params);
    }

    /**
     * Method call overload
     */
    public function __call($method, $args)
    {
        $methods = array('post', 'get', 'delete', 'put');

        if (in_array(strtolower($method), $methods)) {
            if (!isset($args[0])) {
                $args[0] = $this->_uri->getPath();
            }

            $this->_data['rest'] = 1;
            $data = array_slice($args, 1);
            $response = $this->{'rest' . $method}($args[0], $data);
            $this->_data = array();
            if ($response->getHeader("Content-Type")=="application/json") {
                return json_decode($response->getBody(), true);
            } else {
                return new Zend_Rest_Client_Result($response->getBody());
            }
        } else {
            // More than one arg means it's definitely a Zend_Rest_Server
            if (sizeof($args) == 1) {
                // Uses first called function name as method name
                if (!isset($this->_data['method'])) {
                    $this->_data['method'] = $method;
                    $this->_data['arg1']  = $args[0];
                }
                $this->_data[$method]  = $args[0];
            } else {
                $this->_data['method'] = $method;
                if (sizeof($args) > 0) {
                    foreach ($args as $key => $arg) {
                        $key = 'arg' . $key;
                        $this->_data[$key] = $arg;
                    }
                }
            }
            return $this;
        }
    }
}

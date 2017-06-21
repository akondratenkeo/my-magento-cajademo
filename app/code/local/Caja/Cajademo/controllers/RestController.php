<?php 


class Caja_Cajademo_RestController extends Mage_Core_Controller_Front_Action
{
    protected function indexAction()
    {
        //Basic parameters that need to be provided for oAuth authentication on Magento
        $params = array(
            'siteUrl' => 'http://caja-demo.loc/oauth',
            'requestTokenUrl' => 'http://caja-demo.loc/oauth/initiate',
            'accessTokenUrl' => 'http://caja-demo.loc/oauth/token',
            'authorizeUrl' => 'http://caja-demo.loc/admin/oAuth_authorize',
            'consumerKey' => 'a43b1b2d2aad091f2ae9fb0ab69c412a',
            'consumerSecret' => 'b8c5cac6f03df4a1c137ed248278311a',
            'callbackUrl' => 'http://caja-demo.loc/cajademo/rest/callback'
        );

        $consumer = new Zend_Oauth_Consumer($params);

        // fetch a request token
        $token = $consumer->getRequestToken();

        // persist the token to storage
        $session = Mage::getSingleton('core/session');
        $session->setRequestToken(serialize($token));

        $consumer->redirect();
    }

    public function callbackAction()
    {
        $params = array(
            'siteUrl' => 'http://caja-demo.loc/oauth',
            'requestTokenUrl' => 'http://caja-demo.loc/oauth/initiate',
            'accessTokenUrl' => 'http://caja-demo.loc/oauth/token',
            'authorizeUrl' => 'http://caja-demo.loc/admin/oAuth_authorize',
            'consumerKey' => 'a43b1b2d2aad091f2ae9fb0ab69c412a',
            'consumerSecret' => 'b8c5cac6f03df4a1c137ed248278311a',
            'callbackUrl' => 'http://caja-demo.loc/cajademo/rest/callback'
        );

        $consumer = new Zend_Oauth_Consumer($params);

        $session = Mage::getSingleton('core/session');
        $requestToken = $session->getRequestToken();

        if (!empty($_GET) && isset($requestToken)) {

            $token = $consumer->getAccessToken($_GET, unserialize($requestToken));

            $restClient = $token->getHttpClient($params);
            $restClient->setUri('http://caja-demo.loc/api/rest/products');
            $restClient->setHeaders('Accept', 'application/json');
            $restClient->setMethod(Zend_Http_Client::GET);
            $response = $restClient->request();

            Zend_Debug::dump($response);
        } else {

            exit('Invalid callback request. Oops. Sorry.');
        }
    }
}

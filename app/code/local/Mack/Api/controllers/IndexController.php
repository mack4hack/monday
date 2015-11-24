<?php
 
/**
 *
 * @author     Darko Goleš <darko.goles@inchoo.net>
 * @package    Inchoo
 * @subpackage RestConnect
 * 
 * Url of controller is: http://monday.local.com/restconnect/test/[action] 
 */
class Mack_Api_IndexController extends Mage_Core_Controller_Front_Action {
 
    public function indexAction() {
 
        //Basic parameters that need to be provided for oAuth authentication
        //on Magento
        $params = array(
            'siteUrl' => 'http://monday.local.com/oauth',
            'requestTokenUrl' => 'http://monday.local.com/oauth/initiate',
            'accessTokenUrl' => 'http://monday.local.com/oauth/token',
            'authorizeUrl' => 'http://monday.local.com/admin/oAuth_authorize', //This URL is used only if we authenticate as Admin user type
            'consumerKey' => '3917a9e68c8d9b809fcd673d42ebc48e', //Consumer key registered in server administration
            'consumerSecret' => 'b379a257ad5e3e1bde25a41da76262b5', //Consumer secret registered in server administration
            'callbackUrl' => 'http://monday.local.com/mackapi/index/callback', //Url of callback action below
        );
 
 
        $oAuthClient = Mage::getModel('mack_api/oauth_client');
        $oAuthClient->reset();
 
        $oAuthClient->init($params);
        $oAuthClient->authenticate();
 
        return;
    }
 
    public function callbackAction() {
 
        $oAuthClient = Mage::getModel('mack_api/oauth_client');
        $params = $oAuthClient->getConfigFromSession();
        $oAuthClient->init($params);
 
        $state = $oAuthClient->authenticate();
 
        if ($state == Mack_Api_Model_OAuth_Client::OAUTH_STATE_ACCESS_TOKEN) {
            $acessToken = $oAuthClient->getAuthorizedToken();
        }
 
        $restClient = $acessToken->getHttpClient($params);
        // Set REST resource URL
        $restClient->setUri('http://monday.local.com/api/rest/products');
        // In Magento it is neccesary to set json or xml headers in order to work
        $restClient->setHeaders('Accept', 'application/json');
        // Get method
        $restClient->setMethod(Zend_Http_Client::GET);
        //Make REST request
        $response = $restClient->request();
        // Here we can see that response body contains json list of products
        Zend_Debug::dump($response);
 
        return;
    }
 
}
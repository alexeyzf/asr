<?php
/*require_once ('../../../../library/Asr/Soaptest.php');
require_once 'Zend/Controller/Action.php';

class SoapController extends Zend_Controller_Action
{
	private $_WSDL_URI = "https://xxx.xxx.xxx.xxx/mobile_request/get.aspx?admin";

 	public function init()
    {

    }

    public function indexAction()
    {
    	$this->_helper->viewRenderer->setNoRender();

    	if(isset($_GET['wsdl'])) {
    		//return the WSDL
    		$this->hadleWSDL();
		} else {
			//handle SOAP request
    		$this->handleSOAP();
		}
    }

	private function hadleWSDL() {
		$autodiscover = new Zend_Soap_AutoDiscover();
    	$autodiscover->setClass('Soaptest');
    	$autodiscover->handle();
	}

	private function handleSOAP() {
		$soap = new Zend_Soap_Server($this->_WSDL_URI);
    	$soap->setClass('Soaptest');
    	$soap->handle();
	}

    public function clientAction() {
    	$client = new Zend_Soap_Client($this->_WSDL_URI);

    	$this->view->add_result = $client->math_add(11, 55);
    	$this->view->logical_not_result = $client->logical_not(true);
    	$this->view->sort_result = $client->simple_sort( array("d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple"));

    }

}*/
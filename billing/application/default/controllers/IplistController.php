<?php
require_once ('BaseController.php');
require_once ('PointIpAddresses.php');

class IplistController extends BaseController
{

    public function indexAction()
    {

    	if($this->_request->isPost())
    	{
    		$type_ip = trim($this->_request->getParam('type_ip'));

    		$ipModel = new PointIpAddresses();

    		$data    = $ipModel->getOccupedOrFreeIP($type_ip);

			$this->view->getIpList = $data;
  		}
    }
}
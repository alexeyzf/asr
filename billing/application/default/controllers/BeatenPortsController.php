<?php
require_once ('BaseController.php');

class BeatenPortsController extends BaseController
{

    public function indexAction()
    {
    	$portModel = new Ports();

    	$dataBreakenPorts = $portModel->getBrokenPort();

		$this->view->data = $dataBreakenPorts;
    	$this->view->errors = MessageHelper::getAllErrors();
        $this->view->infos  = MessageHelper::getAllInfos();
    }

    public function saveChangeStatusAction()
    {
    	$translator = Zend_Registry::get('Zend_Translate');

		if($this->_request->isPost())
		{
			$data = $this->_request->getPost();

			if($data['mark'] == "")
			{
				MessageHelper::addInfo('port-info', $translator->_('notSetAction'));
				$this->_redirect('/beaten-ports/index');
			}
			elseif($data['mark'] == 0)
			{
				$portModel = new Ports();
				$result = $portModel->setStatusFromBrokenPort($data['port_id']);
				MessageHelper::addInfo('port-info', $translator->_('repairDone'));
				$this->_redirect('/beaten-ports/index');
			}
		}
    }
}
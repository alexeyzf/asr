<?php
require_once ('BaseController.php');
require_once ('SaldoModel.php');

class SaldoController extends BaseController
{

    public function indexAction()
    {
		if($this->_request->isPost())
        {
            $client_id  = $this->_request->getPost('client_id');
            $sdate       = $this->_request->getPost('sdate');

			$saldoModel = new SaldoModel();

			$this->view->data = $saldoModel->getSaldoFromDate($sdate, $client_id);
        }

    }

    public function setSaldoAction()
    {
    	if($this->_request->isPost())
    	{
			$newsaldo    = $this->_request->getParam('newsaldo');
			$ballance_id = $this->_request->getParam('b_id');
			$clientid    = $this->_request->getParam('clientid');

			$saldoModel = new SaldoModel();
			$saldoModel->setSaldoNew($newsaldo, $ballance_id);

			$saldoModel->setRealBallance($clientid, $newsaldo);

			$this->_redirect('/Saldo');
    	}
    }
}
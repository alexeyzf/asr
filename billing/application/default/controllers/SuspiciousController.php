<?php
require_once ('BaseController.php');


class SuspiciousController extends BaseController
{
    public function indexAction()
    {
		if($this->_request->isPost())
		{
			$tarifID = $this->_request->getParam('tarif_id');
			$month   = $this->_request->getParam('month');

			$clientModel = new ClientModel();
			$result = $clientModel->getSuspiciousClients($month, $tarifID);

			$this->view->result = $result;
		}

		$model 	   = new TarifListModel();
		$allTarifs = $model->getStreamUnlim();

		$this->view->tarifs = $allTarifs;
    }
}
<?php
require_once ('BaseController.php');
require_once ('CorpTraffic.php');
require_once('jQuery.php');

class TrafficController extends BaseController
{

    public function indexAction()
    {
    	require_once('forms/DatePeriod.php');
		$form = new Form_DatePeriod();
		$this->view->form = $form;

        $point_id  = $this->_request->getParam('pid');

        $pointModel = new EditPointModel();
        $clientModel = new ClientModel();
        $point = $pointModel->fetchRecordByID($point_id);
        $client = $clientModel->getClientName($point['client_id']);
        $this->view->clientName = $client;

        $corpModel = new CorpTraffic();

        if($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            if($form->isValid($data))
            {
                $startDate = $form->getValue('startdate');
                $endDate   = $form->getValue('enddate');

                $this->view->getList = $corpModel->showStat($point_id, $startDate, $endDate);

                $this->view->getTotal = $corpModel->getTotalBytes($point_id, $startDate, $endDate);
            }
        }
    }

    public function getDetaledTrafficAction()
    {
		if($this->_request->isPost())
		{
			$data = $this->_request->getParams();
			if($data)
			{
				$trafficModel = new FreeTraffic();
				$trafficModel->insertForTrafficDetails($data);

				jQuery('#traffic_details_id')->html('<b>Запрос записан!</b>');
        		$this->getResponse()->appendBody(jQuery::getResponse());
        		exit;
			}
		}
    }

    public function searchDetailsAction()
	{

		if($this->_request->isPost())
		{
			$ip = $this->_request->getPost('ip_search');

			$model = new FreeTraffic();

			$this->view->ip = $ip;
			$this->view->data = $model->searchDetailsFromDB($ip);
		}
	}
}
<?php
require_once ('BaseController.php');
require_once ('FinanceModel.php');
require_once ('PaginatorHelper.php');
require_once ('forms/Startdate.php');

class FinanceController extends BaseController
{
    public function indexAction()
    {

    }

    public function financehistoryAction()
    {
            $client_id = trim($this->_request->getParam('client_id'));
            $searchdata = $this->_request->getPost();

            $startdate = new Form_Startdate();
            $today['startdate'] = date('Y-m-d');
            $today['enddate'] = ('2011-01-01');
            $startdate->populate($today);

            $this->view->startdate = $startdate;
            $this->view->client_id = $client_id;

            $financeModel = new FinanceModel();// Создаем модель Finance
            $list = $financeModel->getTransactionList($client_id, $searchdata);

            $page                      = $this->_request->getParam('page');
            $this->view->paginator   = PaginatorHelper::getPaginator($list, $page);
            $this->view->client_name = $list[0]['client_name'];
    }

    public function saldohistoryAction()
    {
        $client_id = trim($this->_request->getParam('client_id'));

        $saldoModel = new FinanceModel();
        $data         = $saldoModel->getSaldo($client_id);

        $page                      = $this->_request->getParam('page');
        $this->view->paginator   = PaginatorHelper::getPaginator($data, $page);
        $this->view->client_name = $data[0]['client_name'];
    }

    public function traffichistoryAction()
    {
        $clientID = intval($this->_request->getParam('client_id'));
		$point_id = intval($this->_request->getParam('pid'));

        $clientModel = new ClientModel();
        $clientName = $clientModel->getClientName($clientID);

        $this->view->client_id = $clientID;
        $this->view->client_name = $clientName;

        if ($this->_request->getParam('year'))
        {
            $data = $this->_request->getParams();
            $startDate = date('Y-m-d', strtotime($data['year'] . '-' . $data['month'] . '-' . $data['day']));
            $endDate = date('Y-m-d', strtotime($data['yearend'] . '-' . $data['monthend'] . '-' . $data['dayend']));
        }
        else
        {
            //по умолчанию за текущий месяц
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-01', strtotime('+ 1 month'));

        }

        $form = new Form_Startdate();
        $formData['startdate'] = $startDate;
        $formData['enddate'] = $endDate;
        $form->populate($formData);

        $this->view->form = $form;

        $radacctModel = new Radacct();
        $freeTrafficModel = new FreeTraffic();

        $list = $radacctModel->getTrafficHistory($clientID, $startDate, $endDate);
		$this->view->traffic = $list;
		$this->view->stream_traffic = $radacctModel->getStreamTraffic($point_id, $startDate, $endDate);
		$this->view->freeTraffic    = $freeTrafficModel->getTrafficByClientID($clientID, $startDate);
    }
}
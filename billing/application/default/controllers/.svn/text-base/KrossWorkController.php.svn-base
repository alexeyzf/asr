<?php
require_once ('BaseController.php');
require_once ('forms/conductingForm.php');

class KrossWorkController extends  BaseController
{

  protected $_amountPerKross = 7500;

  public function indexAction()
  {
  	$month = $this->_request->getParam('month');

  	if($this->_request->isPost())
  	{
		$start = date('Y')."-".$month."-"."01";
		$end   = date('Y')."-".$month."-".date('t', strtotime($start));

  		$phoneHubModel = new PhoneHubList();
		$hubData       = $phoneHubModel->getAllDataHubs($start, $end);

		$this->view->hubs   	 = $hubData;
		$this->view->month  	 = $month;
		$this->view->amountKross = $this->_amountPerKross;
  	}
  	else
  	{
  		$this->view->month = date('m');
  	}
  }

  public function conductingBuildAction()
  {
		$ID    = $this->_request->getParam('id');
		$month = $this->_request->getParam('month');

		if($ID)
		{
			$this->view->hubID = $ID;
			$this->view->month = $month;
		}

		if($this->_request->isPost())
		{
			$summa 	  = $this->_request->getParam('summa');
			$trantype = $this->_request->getParam('trantype');
			$hubID    = $this->_request->getParam('hub_id');
			$notes    = $this->_request->getParam('notes');

			$modelPhineHubList = new PhoneHubList();
			//addTransactionOnHub($hubID, $trantype, $summa, $notes)
			$modelPhineHubList->addTransactionOnHub($hubID, $trantype, $summa, $notes);

			$this->_redirect('/kross-work/index');
		}
  }

  public function financeHistoryAction()
  {
  		$hubID = $this->_request->getParam('hub_id');

  		$model = new PhoneHubList();

  		$this->view->data = $model->getFinanceHistory($hubID);
  }
}
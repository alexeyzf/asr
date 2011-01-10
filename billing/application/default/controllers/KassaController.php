<?php
require_once('BaseController.php');
require_once('forms/Ratechange.php');
require_once('forms/Recalculation.php');
require_once('KassaModel.php');
require_once('ClientModel.php');


class KassaController extends BaseController
{
	public function indexAction()
	{

	}

	public function ratechangeAction()
	{
		$newrate = $this->_request->getPost();

		$modelRate = new KassaModel();
		$result    = $modelRate->show();


		$data['startdate'] = date('Y-m-d');
		$data['rate']	   = $result;
		$form = new Form_Ratechange();
        $form->populate($data);

        $this->view->form 		  = $form;
        $this->view->current_rate = $result;
	}

	public function saverateAction()
	{
		$newrate = $this->_request->getPost();
		$startdate = $newrate['startdate_year']. $newrate['startdate_month']. $newrate['startdate_day'];

		$saverateModel = new KassaModel();
		$save = $saverateModel->saverate($startdate, $newrate['rate']);
		$this->_redirect('/Kassa/ratechange');
	}


	public function recalculationAction()
	{
		$word    = $this->_request->getPost();


		$model  = new KassaModel();
		$result = $model->getClient($word['typesearch'], $word['word']);

		$data['word'] = $word['word'];
		$form = new Form_Recalculation();
        $form->populate($data);

        $this->view->form 		  = $form;
        $this->view->result = $result;
	}

	public function startrecalcAction()
	{
		$client_id = $this->_request->getParam('client_id');

		$modelRecalc   = new KassaModel();
		if($client_id == "")
		{
			$recalc_result = $modelRecalc->recalculate();
		}
		else
		{
			$recalc_result = $modelRecalc->recalculate($client_id);
		}
		$this->_redirect('/Kassa/recalculation');

		//Пересчитать баланс по транзакциям для всех клиентов
	}
}
?>
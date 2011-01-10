<?php
/**
 * CorpClientController
 * 
 * Controller for corporate clients pages
 * 
 * @author marat
 */

require_once 'BaseController.php';

class CorpClientController extends BaseController 
{
	public function showClientsBallanceAction()
	{
		$paramDate = $this->_request->getParam('date');
		
		if ( ! $paramDate )
		{
			$paramDate = date('Y-m-01');
		}
		
		$date = date('Y-m-d', strtotime($paramDate));
		
		$clientModel = new ClientModel();
		$financeModel = new FinanceModel();
		
		$corpClients = $clientModel->getCorpClients();
		$ballances = $financeModel->getBallancesOnDate($date);
		
		foreach ($corpClients as $key => $client)
		{
			$corpClients[$key]['ballance'] = $ballances[$client['client_id']];
		}
		
		$this->view->corpClients = $corpClients;
		$this->view->date = $paramDate;
	}
	
	public function showNotInEffectAction()
	{
		$clientModel = new ClientModel();
		$this->view->data = $clientModel->getCorpsNotInEffect();
	}
}
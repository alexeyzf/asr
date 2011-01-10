<?php

require_once 'Zend/Controller/Action.php';
require_once 'CacheHelper.php';
require_once 'BankClientHelper.php';
require_once 'Zend/Http/Client.php';

class SomecurlController  extends Zend_Controller_Action
{
	public function indexAction()
	{
		$clientModel 	 = new ClientModel();
		$bankClientModel = new BankClient();

		$rows = BankClientHelper::getDataTransactions("2010-08-10");

		$transactions = array();

		foreach($rows as $item)
		{
			if ( ($item['account'] != '20208000304118577004')
				  && ($item['account'] != '29801000200000974565') // online-bil
				  && ($item['account'] != '29814000504118577001') // inkass
				  && ($item['account'] != '')
			   )
			{
				$clientID = $clientModel->getClientByAccount($item['account'], 0, $item);

				if (!$clientID )
				{
					$item['notes'] = 'Не найден';
				}

				$item['client_id'] = $clientID;

				array_push($transactions, $item);
			}
		}

		$this->view->result = $transactions;

		$this->view->options = $this->_options;

		$this->view->action = 'kapital';

		$this->render('index');
		$this->render('kapital');

	}

}
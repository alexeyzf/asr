<?php
/**
 * Controller for cards pages
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('Cards.php');

class CardsController extends BaseController
{
	public function searchAction()
	{
		$cardsModel = new Cards();

		if ($this->_request->isPost())
		{
			$serial = $this->_request->getParam('serial');
			$number = $this->_request->getParam('number');
			$cards  = $cardsModel->search($serial, $number);

			$this->view->activated_data 	 = $cardsModel->cardActivationDetails($serial, $number);
			$this->view->card_peregovor_stat = $cardsModel->cardPeregovorDetails($serial, $number);

			$this->view->cards  = $cards;
			$this->view->serial = $serial;
			$this->view->number = $number;
		}
	}

	public function finhistoryAction()
	{
		$client_id  = $this->_request->getParam('cid');
		$cardsModel = new Cards();

		$this->view->finhistory = $cardsModel->showFinanceHistory($client_id);
	}

}
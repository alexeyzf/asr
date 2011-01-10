<?php
/**
 * Controller for e-ticket pages
 * 
 * @author marat
 */

require_once ('BaseController.php');
require_once ('TicketModel.php');
require_once ('forms/TicketForm.php');

class TicketController extends BaseController
{
	/**
	 * Показывает организации, даты событий, общее кол-во билетов, кол-во проданных билетов
	 * @return void
	 */
	public function indexAction()
	{
		if ($_SESSION['ticket_error'])
		{
			$this->view->error =  $_SESSION['ticket_error'];
			unset($_SESSION['ticket_error']);
		}
		$ticketModel = new TicketModel();
		$this->view->events = $ticketModel->getEvents();
	}
	
	public function addEventAction()
	{
		$form = new TicketForm();
		$this->view->form = $form;
		
		if ( $this->_request->isPost() )
		{
			$postData = $this->_request->getPost();
			if ($form->isValid($postData))
			{
				$data = $form->getValues();
				$ticketModel = new TicketModel();
				$ticketModel->createEvent(
					$data['organization_code'],
					date('Y-m-d H:i:s', $data['event_time']),
					$data['row_number'],
					$data['place_count'],
					$data['ticket_price']
				);
				
				$this->_helper->redirector('index');
			}
		}
	}
	
	public function dropEventAction()
	{
		$orgCode = $this->_request->getParam('org');
		$eventTime = $this->_request->getParam('time');
		$eventTime = date('Y-m-d H:i:s', $eventTime);
		
		$ticketModel = new TicketModel();
		if ( ! $ticketModel->checkSold($orgCode, $eventTime) )
		{
			$ticketModel->deleteEvent($orgCode, $eventTime);
		}
		else
		{
			$_SESSION['ticket_error'] = 'Не возможно удалить событие, так как на него уже были проданы билеты!';
		}
		
		$this->_helper->redirector('index');
	}
}
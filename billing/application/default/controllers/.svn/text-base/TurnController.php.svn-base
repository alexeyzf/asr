<?php
//require_once ('BaseController.php');
require_once 'Zend/Controller/Action.php';
require_once ('forms/turnForm.php');
require_once('jQuery.php');

class TurnController extends Zend_Controller_Action
{
    public function indexAction()
    {
		$turnModel = new TurnModel();

		$rows = $turnModel->getTurnList();

		$rowsEmptyPorts = $turnModel->getEmptyPorts();

		if($rows)
		{
			$this->view->data = $rows;
		}
		$this->view->emptyPorts = $rowsEmptyPorts;
    }

    public function getListTurnAction()
    {
    	$turnModel = new TurnModel();
    	$rows = $turnModel->getTurnList();

		if($rows)
		{
			$this->view->listData = $rows;
		}
    }

    public function addOnTurnAction()
    {
    	$this->view->errors = MessageHelper::getAllErrors();
        $this->view->infos  = MessageHelper::getAllInfos();

    	$atsModel = new AtsList();

		$data['ats_list'] = $atsModel->getOptions();

        $form = new Form_turnForm();
        $form->populate($data);
        $this->view->form = $form;

    }


    public function addAction()
    {
		$atsID  	   = $this->_request->getParam('ats_id');
		$pcross 	   = $this->_request->getParam('pcross');
		$contact_name  = $this->_request->getParam('contact_name');
		$contact_phone = $this->_request->getParam('contact_phone');

		$turnModel = new TurnModel();

		// ID юзера который делает что то
        $auth 			= Zend_Auth::getInstance();
        $user 			= $auth->getStorage()->read();

        if($atsID and $pcross)
        {
			$flag = $turnModel->insertOnTurn($atsID, $pcross, $contact_name, $contact_phone, $user->id);

			if($flag == 1)
			{
				jQuery('#turn_add_id')->html('<b>Клиент успешно записан в очередь!</b>');
			}
			elseif($flag == 0)
			{
				jQuery('#turn_add_id')->html('<b>При добавлении в очередь возникли проблемы! Попробуйте еще раз.</b>');
			}
        }

        $this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    public function markDeleteAction()
    {
		$ID = $this->_request->getParam('clear_id');
		if($ID)
		{
			$turnModel = new TurnModel();
			$flag = $turnModel->markDelete($ID);

			if($flag == 1)
			{
				$messages[] = " Клиент успешно удален из очереди!";
			}
			elseif($flag == 0)
			{
				$messages[] = " Ошибка удаления!";
			}

			$this->view->mess = $messages;
    		$this->view->url_need = "/turn/index";
			$this->_helper->layout->setLayout('iframe-redirector');
		}
    }
}
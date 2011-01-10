<?php
/**
 * Controller for technical history pages
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('TechHistory.php');
require_once ('forms/AddHistory.php');

class TechHistoryController extends BaseController
{
    public function indexAction()
    {
        $this->view->atsList = ClientHelper::getAtsList();
        $searchBy = $this->_request->getParam('searchBy');

        if ($searchBy)
        {
            $clientHistoryModel = new TechHistory();

            if ($searchBy == 'client')
            {
                $login = $this->_request->getParam('login');
                $this->view->login = $login;
                $logList = $clientHistoryModel->getHistoryByLogin($login);
            }
            elseif ($searchBy == 'phone')
            {
                $number = $this->_request->getParam('number');
                $this->view->number = $number;
                $logList = $clientHistoryModel->getHistoryByPhone($number);
            }
            elseif ($searchBy == 'equipment')
            {
                $ats = $this->_request->getParam('ats');
                $dslam = $this->_request->getParam('dslam');
                $port = $this->_request->getParam('port');

                $this->view->ats = $ats;
                $this->view->dslam = $dslam;
                $this->view->port = $port;

                $logList = $clientHistoryModel->getHistoryByEquipment($ats, $dslam, $port);
                $this->view->dslamList = ClientHelper::getDslamList($ats);

                $portsModel = new Ports();
                $this->view->portList = $portsModel->getDslamPortNumbers($dslam);
            }

            $this->view->logList = $logList;
            $this->view->searchBy = $searchBy;
        }
    }

    public function manualAdditionAction()
    {
    	$phone = $this->_request->getParam('sphone');
		$clientHistoryModel = new TechHistory();

		$translator = Zend_Registry::get('Zend_Translate');

		if($this->_request->isPost())
		{
			$phone = $this->_request->getParam('sphone');

			$logRow = $clientHistoryModel->getHistoryByPhoneLastState($phone);

			$form = new Form_AddHistory();
			$form->populate($logRow[0]);
			$this->view->form = $form;
		}
		else
		{
			$logRow = $clientHistoryModel->getHistoryByPhoneLastState($phone);

			$form = new Form_AddHistory();
			$form->populate($logRow[0]);
			$this->view->form = $form;
		}
		$this->view->errors = MessageHelper::getAllErrors();
        $this->view->infos  = MessageHelper::getAllInfos();
    }

    public  function saveHistroyAdditionAction()
    {
    	$translator = Zend_Registry::get('Zend_Translate');

		if($this->_request->isPost())
		{
			$data = $this->_request->getPost();
	        // ID юзера который делает что то
	        $auth = Zend_Auth::getInstance();
	        $user = $auth->getStorage()->read();

			$data['userid'] = $user->id;

			$model = new TechHistory();
			$model->insertManualHistory($data);

			MessageHelper::addInfo('history-info', $translator->_('historySaved'));

			$this->_redirect('/Tech-History/manual-addition/sphone/'.$data['phone'].'');
			exit();
		}
    }

}
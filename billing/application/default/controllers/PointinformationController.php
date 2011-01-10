<?php
/*
 * Created on 14.08.2009
 * Контроллер отвечает за показ подробной информации о выбранной точке
 * и ее редактирование
 */

require_once ('BaseController.php');
require_once ('forms/Testdays.php');

 class PointinformationController extends BaseController
 {
    public function indexAction()
    {
    	$point_id  = $this->_request->getParam('pointid');
    	$client_id = $this->_request->getParam('clientid');
    	$table     = $this->_request->getParam('tablename');
    	$tid       = $this->_request->getParam('tarif_id');

        $dataPointModel    = new EditPointModel();
        $modelAddPoint     = new AddPoint();
        $trustPaymentModel = new TrustPaymentModel();

        $pointdata 		  = $dataPointModel->selectOldInformationAboutPoint($point_id);
        $flagLoginRewrite = $dataPointModel->showCountCrossServices($point_id);

        $pointdata['tablename'] = $table;
        $pointdata['tarif_id']  = $tid;
        $_SESSION['back_url']   = $_SERVER['REQUEST_URI'];

		$blockedClients = $modelAddPoint->getBlockPointCorp($point_id);

		if($blockedClients)
		{
			$this->view->flag_block = 1;
			$this->view->datablock = $blockedClients;
		}
		else
		{
			$this->view->flag_block = 0;
		}
        $this->view->pointdata = $pointdata;
        $this->view->client_id = $client_id;
        $this->view->point_id  = $point_id;
        $this->view->tablelink = $table;
        $this->view->client_type_id = $pointdata['client_type_id'];
		$this->view->trustRow  = $trustPaymentModel->getTrustPayment($pointdata['u_login']);

        if($flagLoginRewrite > 0)
        {
                $this->view->flag = 1;
        }
        else
        {
                $this->view->flag = 0;
        }

		$this->view->errors = MessageHelper::getAllErrors();
        $this->view->infos  = MessageHelper::getAllInfos();
    }

    public function setpwdcontractAction()
    {
    	$point_id = $this->_request->getPost('point_id');
		$new_pwd  = $this->_request->getPost('new_pwd');
		$u_login  = $this->_request->getPost('u_login');

		$model = new EditPointModel();

		$resutl = $model->setPwdFromContract($new_pwd, $u_login, $new_pwd, $point_id);

		$this->_redirect($_SESSION['back_url']);
    }

    public function setnewloginAction()
    {
		$point_id  = $this->_request->getPost('point_id');
		$new_login = $this->_request->getPost('new_login');

		$editPointModel = new EditPointModel();

		if(!$new_login)
		{
			$this->_redirect($_SESSION['back_url']);
		}

		// Создаем лог
        //AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::POINT_NAME_REWRITE, $_SERVER['REQUEST_URI'], $_SESSION['last_userID']);
        $this->_redirect($_SESSION['back_url']);
    }

    public function atsbonusAction()
    {
    	$data = $this->_request->getPost();

    	$pointModel = new EditPointModel();
    	$stime =  date('Y'). "-". $data['month_s']. "-". "15";

    	$data['day_in_selected_month'] = date('t', strtotime($stime));
		$data['my_date'] = $stime;

		//Логируем
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::ATS_BONUS_ADDED, $_SERVER['REQUEST_URI'], $data['client_id'], $data['point_id']);

    	$pointModel->setAtsBonus($data);
    }

    public function setTestDaysAction()
    {
		$point_id  = $this->_request->getParam('pid');
		$client_id = $this->_request->getParam('cid');

		$editPointModel = new EditPointModel();

		$data['point_id'] = $point_id;
		$data['client_id'] = $client_id;

		$form = new Form_Testdays();
		$form->populate($data);

		if($this->_request->isPost())
		{
			$pid 	 = $this->_request->getParam('point_id');
			$comment = $this->_request->getParam('commente_test');

			$flag = $editPointModel->setTestDaysPeriod($point_id, $comment);

			// Логируем
            AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::TEST_POINT, $_SERVER['REQUEST_URI'], $client_id ,$pid);

			if($flag == 1)
			{
				$this->_redirect("/Clientinfo/index/clientid/{$client_id}/pointid/{$pid}");
			}
		}

		$this->view->form = $form;
    }


    public function movingListAction()
    {
        $model = new PerekrosModel();
        $data = $model->getData();

        $this->view->data = $data;
    }

    public function movingListDoneAction()
    {
        $id = $this->_request->getParam('id');

        if($id)
        {
            $model = new PerekrosModel();
            $model->markDoneID($id);
        }
        $this->_redirect('/Pointinformation/moving-list');
    }

    public function blockPointAction()
    {
    	if($this->_request->isPost())
    	{
			$data = $this->_request->getPost();

			$start = $data['year_off']. "-" . $data['month_off']. "-" . $data['day_off'];

			if($data)
			{
				$model = new AddPoint();
				$model->blockPointCorp($data, $start);
				$this->view->mess = "Записано в задания";
	    		$this->view->url_need = $data['uril'];
				$this->_helper->layout->setLayout('iframe-redirector');
			}
    	}
    }

    public function trustPaymentAction()
    {
    		$trustModel  = new TrustPaymentModel();
			$clientModel = new ClientModel();

    		$tranlsate = Zend_Registry::get('Zend_Translate');

			$username = $this->_request->getParam('username');
			$point_id = $this->_request->getParam('point_id');

            $clientData = $clientModel->getByPointID($point_id);

            if($clientData['ballance'] >= 0)
            {
                MessageHelper::addError('additional-service-errors', $tranlsate->_('ballanceUp'));
                $this->_redirect($_SESSION['back_url']);
            }
            else
            {
                $result = $trustModel->trustPaymentSql($username);
                if(!$result['trust_payment_sql'])
                {
                    MessageHelper::addError('additional-service-errors', $tranlsate->_('alreadyTrustPay'));
                    $this->_redirect($_SESSION['back_url']);
                }
                else
                {
                    MessageHelper::addError('additional-service-errors', $tranlsate->_('paymentIncluded'));
                    $this->_redirect($_SESSION['back_url']);
                }
            }
    }

 }
?>

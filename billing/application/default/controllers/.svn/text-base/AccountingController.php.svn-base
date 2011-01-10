<?php
/**
 * Default controller for accounting department
 *
 */

require_once ('BaseController.php');
require_once ('Zend/Paginator.php');
require_once ('Zend/Paginator.php');
require_once ('Zend/Paginator/Adapter/Array.php');
require_once ('Zend/View/Helper/PaginationControl.php');
require_once ('AsrHelp.php');
require_once ('forms/SearchClient.php');
require_once ('forms/Payment.php');
require_once ('forms/CorrectAccounting.php');
require_once ('AccountingModel.php');
require_once ('ClientModel.php');
require_once ('KassaModel.php');
require_once ('Porttasks.php');
require_once ('RateHelper.php');
require_once ('ServiceHelper.php');

class AccountingController extends BaseController
{
    static $flagtype;
    public function indexAction()
    {
        $client_name = $this->_request->getParam('restoreclient');
        $stypes['type_search_options'] = array(
            'PTS.u_login'      => 'по логину',
        	'CLA.client_id'	  =>  'по р.н',
            'PTS.pcross'       => 'по телефону кроссировки',
            'PTS.contact_name' => 'по конт. телефону',
            'CLA.client_name'  => 'по наименованию'
         );

        if($client_name)
        {
			$stypes['searchword'] = $client_name;
        }

        $form = new Form_SearchClient();


        if ($this->_request->isPost())
        {
            $type = $this->_request->getPost();

            $stypes['type_search'] = $type['type_search'];
            $stypes['searchword'] = $type['searchword'];

        }

        $form->populate($stypes);
        $this->view->form = $form;

         //
        // Результат работы данной модели будет передаваться в объект пагинатора
        $searchAbonDepModel = new SearchAbonDepartment();
        $result_rows = $searchAbonDepModel->searchWithParamToPaginator($type['type_search'], $type['searchword']);

        $num = 10; //кол-во строк которые следует вывести
        $page = $this->_getParam('page'); // Принимаем параметры
        if($page < 1 or empty($page))
        {
            $page = 1;
        }
        $start = $page * $num - $num; // нумерация страниц
        // Создаем пагинатор объект и передаем ему данные из модели SearchAbonDepartment
        $paginator = new Zend_Paginator($result_rows);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($num);
        $paginator->setView($this->view);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('/my_pagination.phtml');
        $this->view->paginator = $paginator;
    }

    public function clientcashAction()
    {
        $client_id = $this->_request->getParam('clientid');
        $point_id  = $this->_request->getParam('pointid');


        $infoModel         = new AccountingModel();
        $clienInfoModel    = new ClientModel();

        $tableArr = array (
            0 => 'adsl',
            1 => 'collacation',
            2 => 'ngn',
            3 => 'hosting',
            4 => 'tasix',
            5 => 'wifi',
            6 => 'vpn',
            7 => 'isdn',
            8 => 'tradtel',
            9 => 'additional_services'
        );
        $serviceData = array();
        $clearArr    = array();


        for($i = 0; $i < count($tableArr); $i++)
        {
        	$tableServices = $clienInfoModel->showServices($client_id, $tableArr[$i]);

        	foreach ($tableServices as $key => $tableService)
        	{
			// ТУТ ОШИБКА!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        		$tableServices[$key]['tarif_price'] = ServiceHelper::getAbonPrice($tableService);
        	}

            array_push($serviceData, $tableServices);

            if($serviceData[$i])
            {
                array_push($clearArr, $serviceData[$i]);
            }
        }

        $data     = $infoModel->selectAllClientsCash($client_id);
        $payments = $infoModel->selectPaymentsCash($client_id, 3);

        $data['client_id'] = $client_id;
        $data['point_id'] = $point_id;

	// Тут плат. форма
        if($clearArr)
        {
        	// Проверку
        	$result = $infoModel->verifyStatusCrossAccounting($client_id);

			if($result > "0")
			{
				$data['servicetype'] = $clearArr;
	            $form = new Form_Payment();
	            $form->populate($data);
	            $this->view->form = $form;
			}
			else
			{
				$this->view->form = 'Клиент подан на снятие!';
			}

        }
        else
        {
            $this->view->form = 'Дата окончания услуг у данного клиента больше чем текущая дата ('.date('Y-m-d').')';
        }


        $this->view->data     = $data;

        $this->view->payments = $payments;
        $this->view->avaible  = $clearArr;
        $this->view->rate     = RateHelper::getRate();
        $_SESSION['back_url'] = $_SERVER['REQUEST_URI'];
    }

    public function startpaymentAction()
    {
        $model             = new AccountingModel();
        $clienInfoModel    = new ClientModel();
        $setOn             = new KassaModel();
        $portTasks         = new Porttasks();
        $hostingTasksModel = new HostingTaskModel();

        $data = $this->_request->getPost();

        $data['currenttime'] = $data['currenttime_year']. "-". $data['currenttime_month']. "-". $data['currenttime_day'];

        $data['commente'] = 'Счет ' . $data['commente'] . ' от ' . $data['currenttime'];

        $result = $model->pay(
                                $data['client_id'],
                                $data['point_id'],
                                $data['payment_type'],
                                $data['summas'],
                                $data['servicetype'],
                                $data['commente']
                            );

        $old_and_newballance = str_replace('{', '', $result[0]['kassa_payments']);
        $old_and_newballance = str_replace('}', '', $old_and_newballance);

        $arrBallance = explode(',', $old_and_newballance);

        $arrPoints = $setOn->getOnPoints($data['client_id']);
        $clientTypeID = $arrPoints[0]['client_type_id'];

        if ($clientTypeID == 1)
        {
        	if($arrBallance[0] < 0 and $arrBallance[1] > -0.01)
        	{
        		$checkBallance = true;
        	}
        	else
        	{
        		$checkBallance = false;
        	}
        }
        else
        {
        	if($arrBallance[0] <= -5 and $arrBallance[1] > -5)
        	{
        		$checkBallance = true;
        	}
        	else
        	{
        		$checkBallance = false;
        	}
        }

        if( $checkBallance )
        {
        	// Если старое значение баланса было минус и новое после платежа стало плюс
        	// то вкл. услугу

            $tableArr = array (
                0 => 'adsl',
                1 => 'collacation',
                2 => 'ngn',
                3 => 'hosting',
                4 => 'tasix',
                5 => 'wifi',
                6 => 'vpn',
                7 => 'isdn',
                8 => 'tradtel',
                9 => 'additional_services',
               10 => 'pintel'
            );
            $serviceData = array();
            $clearArr      = array();

            for($i = 0; $i < count($tableArr); $i++)
            {
                $setOn->setOnService($data['client_id'], $tableArr[$i]);
            }

            for( $i = 0; $i < count($arrPoints); $i++ )
            {
                $pointsArr[]   =  $arrPoints[$i]['point_id'];
                $forIF[]       =  $arrPoints[$i]['client_type_id'];
            }

            if($clientTypeID == 1)
            {
                // Для стримов указание на поднятие порта не должно быть
            }
            else
            {
                $portTasks->addPointsTasks($pointsArr, Porttasks::TASK_TYPE_ON);
                //ServiceHelper::activateClient($data['client_id']);
            }

			// Включаем или отключаем услугу хостинг
            $hostingTasksModel->switchOnAllHostingtByClient($data['client_id']);
        }

        $this->_redirect($_SESSION['back_url']);
    }

    public function todaytransactionsAction()
    {
      $tran_id = $this->_request->getParam('tran_id');
      $auth    = Zend_Auth::getInstance();
      $user    = $auth->getStorage()->read();

      $model = new KassaModel();
      if($tran_id)
      {
        $model->deleteTodayTransaction($tran_id, $user->id);
      }
      $this->_redirect($_SESSION['back_url']);
    }

    public function correctiveAction()
    {
    	$model = new Form_CorrectAccounting();
    	$this->view->form = $model;

    	if($this->_request->isPost())
    	{
    		$summa     		= $this->_request->getPost('rollback_sum');
    		$client_id 		= $this->_request->getPost('client_id');
    		$comment   		= $this->_request->getPost('commente_rollback');
                $trantype   		= $this->_request->getPost('trantype');
    		$summa_dollar   = $this->_request->getPost('rollback_dollar');


                $auth = Zend_Auth::getInstance();
        	$user = $auth->getStorage()->read();


                $model 	     = new AccountingModel();
                $clientModel = new ClientModel();

                $client_type_id = $clientModel->getClientByID($client_id);


                $ballance  = $model->startCorrect($summa, $client_id, $summa_dollar, $user->id, $comment, $trantype);

	        $old_and_newballance = str_replace('{', '', $ballance[0]['buh_correct']);
	        $old_and_newballance = str_replace('}', '', $old_and_newballance);

	        $after   = explode(',', $old_and_newballance);
	        $before  = explode('=', $after[0]);

	        $data[0] = $before;
	        $data[1] = $after;

	        if( $data[0][1] < 0 and $data[1][1] > 0 )
	        {
	        	if($client_type_id['client_type_id'] == 0)
	        	{
                            $clientModel->switchOnAllPorts($client_id, 0);
	        	}
	        }

	        if( $data[0][1] > 0 and $data[1][1] < 0 )
	        {
	        	if($client_type_id['client_type_id'] == 0)
	        	{
                            if(date('d') > $client_type_id['overdraft'])
                            {
                                $clientModel->switchOnAllPorts($client_id, 2);
                            }
	        	}
	        }

    	}
    }

    public function showcorrectAction()
    {
    	$client_id = $this->_request->getPost('client_id');
    	$model = new AccountingModel();
    	$this->view->data = $model->showCorrect($client_id);
    }

    public function dbaseCompareAction()
    {
		$path1 = "SF20101130.dbf";
		$f = fopen($path1);
		var_dump($f);
		exit();
		$db = dbase_open($path1, 0);
		var_dump($db);
		exit();
		if($db)
		{
			$record_numbers = dbase_numrecords($db);
			for ($i = 1; $i <= $record_numbers; $i++)
			{
				$row = dbase_get_record_with_names($db, $i);
				if ($row['ismember'] == 1)
				{
					echo "Member #$i: " . trim($row['name']) . "\n";
				}
			}
		}
    }
}



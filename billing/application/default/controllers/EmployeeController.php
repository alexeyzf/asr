<?php
require_once ('BaseController.php');
require_once ('forms/Addclient.php');
require_once ('EmployeeFormHelper.php');
require_once ('AsrHelp.php');
require_once ('ClientModel.php');
require_once ('InsertClientPointModel.php');
require_once ('ClientContract.php');
require_once ('AbonDepartmentHistoryHelper.php');
require_once ('CollacationModel.php');
require_once ('AdslModel.php');
require_once ('NgnModel.php');
require_once ('forms/ClientInfoModify.php');
require_once ('forms/AddDopPoint.php');
require_once ('GenCrossNumberModel.php');
require_once ('ListServiceModel.php');
require_once ('ClientHelper.php');
require_once ('EditPointModel.php');

class EmployeeController extends BaseController
{
    public function indexAction()
    {
    	$this->view->errors = MessageHelper::getAllErrors();
        $this->view->infos  = MessageHelper::getAllInfos();

        $values['asrtypes'] = EmployeeFormHelper::getASRType();
        $form = new Form_Addclient();
        $form->populate($values);
        $this->view->form = $form;
        $this->view->title = 'Добавление новых клиентов';
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost(); // принимаем пост
            $formDataRschet = $this->_request->getPost('some'); // Rschet номера в отдельном массиве
            if ($form->isValid($formData))
            {
                $this->addClient($formData, $formDataRschet);
            }
        }
    }

    public function addClient($data, $schet, $ignore = false)
    {

    	$translator = Zend_Registry::get('Zend_Translate');

        $clientModel 	   = new ClientModel();
        $insertClientModel = new InsertClientModel();
        $blackListModel    = new BlackListModel();

		$resultBad =  $clientModel->verifyPcross($data['pcross'], $data['country_id']);

		if($resultBad == "bad")
		{
			MessageHelper::addError('employee-errors', $translator->_('DuplicatNumber'));
			$this->_redirect('/Employee/index');
		}

		//проверяем нет ли клиента с таким же ИНН
		if ($data['inn'] && $clientModel->verifyInn($data['inn']))
		{
			MessageHelper::addError('employee-errors', $translator->_('badInn'));
			$this->_redirect('/Employee/index');
		}

     	//проверяем нет ли в архиве клиента с таким же ИНН
        $oldClient = $clientModel->verifyArhivInn($data['inn']);

		if ($data['inn'] && $oldClient && $oldClient['ballance'] <= -5 && ! $ignore)
        {
        	$_SESSION['cl_data'] = $data;
        	$_SESSION['cl_schet'] = $schet;
        	$_SESSION['cl_old_id'] = $oldClient['client_id'];

        	// редиректим на предупреждение
        	$this->_redirect('/employee/client-warning');
        }

    	$schetVerifyer = $insertClientModel->varifyRschetForAddClient($schet);
		if($schetVerifyer)
		{
			MessageHelper::addError('employee-errors', $translator->_('badRschet'));
			$this->_redirect('/Employee/index');
		}

        // Проверяем не находится ли данный номер в ЧС

        $blackListResult = $blackListModel->searchDoublePcross($data['pcross']);
		if($blackListResult == 1)
		{
			MessageHelper::addError('employee-errors', $translator->_('blackList_warning'));
			$this->_redirect('/Employee/index');
		}

        //проверяем нет ли в архиве клиента с таким же номером кроссировки
        $oldClient = $clientModel->verifyArhivPcross($data['pcross'], $data['country_id']);

        if ($data['pcross'] && $oldClient && $oldClient['ballance'] <= -0.01 && ! $ignore)
        {
        	$_SESSION['cl_data'] = $data;
        	$_SESSION['cl_schet'] = $schet;
        	$_SESSION['cl_old_id'] = $oldClient['client_id'];

        	// редиректим на предупреждение
        	$this->_redirect('/employee/client-warning');
        }

		// Опр. тип баланса
		$data['currency'] = ClientHelper::getCurrency($data['is_accounting']);

        $row = $clientModel->createRow($data);

        $_SESSION['last_userID'] = $row->save(); // идентификатор клиента
        $_SESSION['faceid']      = $data['client_type_id']; // Сохр. идентификатор типа лица

        // Если клиент физик то соответственно сохраняем и паспортные данные и т.д.
        if ($data['client_type_id'] == 0)
        {
            // Если клиент не физик то сохраняем юр. адрес, инн, мфо и т.д.
            for ($i = 0; $i < count($schet); $i++)
            {
                if ($schet[$i] != "")
                {
                    // Если клиент юрик, то у него есть рас-ст. Тут мы их добавляем в БД
                    $clientModel->addRschet($_SESSION['last_userID'], $schet[$i]);
                }
            }
        }
        // Теперь работаем с таблицей points
        // Формируем логин

        $login_client = $this->_helper->GenerateData->genCross($data['country_id'], $data['pcross'], $data['client_type_id']);
        $pwd          = $this->_helper->GenerateData->genPass(8);


        $clientPointModel = new InsertClientPointModel(); // Модель для таблицы points
        $row_point = $clientPointModel->createRow(); // Создаем пустую строку в БД

        $row_point->client_id       = $_SESSION['last_userID']; // Далее просто заполняем БД
        $row_point->phone           = $data['phone'];
        $row_point->connect_address = $data['address'];
        $row_point->contact_name    = $data['contact_name'];
        $row_point->pcross          = $data['pcross'];
        $row_point->pcross_owner    = $data['pcross_owner'];
        $row_point->u_login         = $login_client;
        $row_point->u_passwd        = $pwd;
        $row_point->pwd_on_contract = $pwd;
        $row_point->country_id      = $data['country_id'];
        $row_point->sign_name       = $data['sign_name'];
        $row_point->post_sign_name  = $data['post_sign_name'];

        $_SESSION['last_pointID'] = $row_point->save(); // Last unique point_id key
        // Конец points

        // Теперь заносим информацию в таблицу contracts
        // проверяем сессию, и достаем от туда ID менеджера
        $auth    = Zend_Auth::getInstance();
        $manager = $auth->getStorage()->read();

        $clientContractModel = new ClientContract();
        $row_contract        = $clientContractModel->createRow();

        $row_contract->client_id     = $_SESSION['last_userID'];
        $row_contract->manager_id    = $manager->id;
        $_SESSION['last_contractID'] = $row_contract->save();
        // Сохраняем последнее заначение ключа (ID) для вновь созданного контракта
        // конец работы с таблицей contracts

        // Создаем лог
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::ADD_NEW_CLIENT, $_SERVER['REQUEST_URI'], $_SESSION['last_userID'],  $_SESSION['last_pointID']);

        $this->_redirect('/employee/modifyservice/client_id/'.$_SESSION['last_userID'].'/point_id/'.$_SESSION['last_pointID'].'');
    }

    public function modifyAction()
    {
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();

            $values['asrtypes'] = EmployeeFormHelper::getASRType();
            $values += $formData;

            $form = new Form_ClientInfoModify();
            $form->populate($values);
            echo $form;
        }
    }

    public function savemodifyAction()
    {
        if ($this->_request->isPost())
        {
            $formToSave = $this->_request->getPost();

            $update_client_info = new ClientModel();
            $rows = $update_client_info->updateClientInfo($formToSave, $formToSave['client_id'], $formToSave['point_id'], $formToSave['client_type_id']);
            // Логируем
            AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::EDIT_CLIENT_INFO, $_SERVER['REQUEST_URI'], $formToSave['client_id'], $formToSave['point_id']);
        }
    }

    public function modifyserviceAction()
    {
        $post_point['client_id'] = $this->_request->getParam('client_id');
        $post_point['point_id']  = $this->_request->getParam('point_id');

        $this->view->form = EmployeeFormHelper::getServiceList($post_point);
    }

    public function templateAction()
    {
        /**
        *  В данном экшене происходит переход на нужный метод
        *  хелпера tamplate, который в свою очередь (в зависимости от параметра)
        *  вызывает нужный метод для построения и заполнения формы
        */
        if ($this->_request->isPost())
        {

            // Получаем геты от формы
            $serviceData = $this->_request->getPost();
            //$verifyNeedCross = new PointServices();

            $point_id   = $serviceData['point_id'];
            $client_id  = $serviceData['client_id'];
            $need_cross = $serviceData['need_cross'];
            $service_id = $serviceData['servicetype_id'];

            // Получаем ссылку на таблицу
            $tableLinkModel = new TarifListModel();
            $rowlink         = $tableLinkModel->getTableLink($service_id);
            $cross_model     = new EditPointModel();
            $cross_flag      = $cross_model->verifyNeedCross($service_id);
            $servicesCount   = $cross_model->getPointServicesCount($point_id);

            $this->view->tablelink = $rowlink;

            $model = $this->_helper->template->createModel($rowlink);

            if (method_exists($model, 'getAdditionalInfo'))
            {
                $serviceData += $model->getAdditionalInfo();
            }

            if ($servicesCount != 0)
            {
            	$this->view->message = "Точка уже активна";
            }
            else
            {
            	$this->view->form = $this->_helper->template->createForm($rowlink, $client_id, $serviceData);
            }
        }
    }

    public function clearvarsAction()
    {
        unset ($_SESSION['last_userID']);
        unset ($_SESSION['last_contractID']);
        unset ($_SESSION['faceid']);
        unset ($_SESSION['last_pointID']);
        $this->_redirect('/employee/');
    }

    public function deleteserviceAction()
    {
          $pointForDelete = $this->_request->getParams();

          $deleteserviceModel = new PointServices();
          $deleteservice       = $deleteserviceModel->setDeletedFlagService($pointForDelete['point'], $pointForDelete['sid'], $pointForDelete['tarif'], $pointForDelete['tablename']);

          // Логирование
          AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::DELETE_SERVICE_ON_REG, $_SERVER['REQUEST_URI'], $pointForDelete['client_id'] );
          $this->_redirect($_SESSION['back_url']);
    }

    public function clientWarningAction()
    {
    	$clientData = $_SESSION['cl_data'];

    	if (is_array($clientData))
    	{
    		$oldID = $_SESSION['cl_old_id'];
    		$clientModel = new ClientModel();
    		$this->view->data = $clientModel->getArhivClient($oldID);;
    	}
    	else
    	{
    		$this->_redirect('/employee/index');
    	}
    }

    public function clientIgnoreWarningAction()
    {
    	$clientData = $_SESSION['cl_data'];
    	$clientSchet = $_SESSION['cl_schet'];
    	unset($_SESSION['cl_data']);
    	unset($_SESSION['cl_schet']);

    	if (is_array($clientData))
    	{
    		$oldID = $_SESSION['cl_old_id'];
    		unset($_SESSION['cl_old_id']);
    		AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::IGNORE_CLIENT_WARNING, $_SERVER['REQUEST_URI'],  $oldID);
    		$this->addClient($clientData, $clientSchet, true);
    	}
    	else
    	{
    		$this->_redirect('/employee/index');
    	}
    }

    public function clientCancelWarningAction()
    {
    	$oldID = $_SESSION['cl_old_id'];
    	unset($_SESSION['cl_data']);
    	unset($_SESSION['cl_schet']);
    	unset($_SESSION['cl_old_id']);

    	AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::CANCEL_CLIENT_WARNING, $_SERVER['REQUEST_URI'],  $oldID);

    	$this->_redirect('/employee/index');
    }

    public function clientFixWarningAction()
    {
    	$clientData = $_SESSION['cl_data'];
    	$clientSchet = $_SESSION['cl_schet'];
    	unset($_SESSION['cl_data']);
    	unset($_SESSION['cl_schet']);

    	if (is_array($clientData))
    	{
    		$oldID = $_SESSION['cl_old_id'];
    		unset($_SESSION['cl_old_id']);
    		AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::FIX_CLIENT_WARNING, $_SERVER['REQUEST_URI'],  $oldID);
    		$clientModel = new ClientModel();
    		$oldClient = $clientModel->getArhivClient($oldID);
    		$clientData['ballance'] = $oldClient['ballance'];
    		$this->addClient($clientData, $clientSchet, true);
    	}
    	else
    	{
    		$this->_redirect('/employee/index');
    	}
    }
}
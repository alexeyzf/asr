<?php
/**
 * Controller for working with services
 *
 * @author marat
 */

require_once ('AdslModel.php');
require_once ('CollacationModel.php');
require_once ('BaseController.php');
require_once ('forms/ServiceEdit.php');
require_once ('forms/Delivery.php');
require_once ('Zend/Date.php');
require_once ('ListServiceModel.php');
require_once ('FinanceModel.php');
require_once ('TarifListModel.php');
require_once ('ClientModel.php');
require_once ('EditPointModel.php');
require_once ('Porttasks.php');
require_once ('GenerateData.php');
require_once ('forms/AdditionalWriteOffs.php');
require_once ('forms/AtsBonusForm.php');
require_once ('Asr/InvoiceHelper.php');
require_once ('Asr/FinanceHelper.php');
require_once ('AsrHelp.php');
require_once ('helpers/ServiceHelper.php');
require_once('jQuery.php');

class ServiceController extends BaseController
{
	public function saveAction()
	{
        $data = $this->_request->getPost();


        if ($data['id'] == 'new')
        {
        	$data['id'] = 0;
        }

		$form = $this->_helper->template->createForm($data['tablelink'], $data['client_id'], $data);

		if ( $form->isValid($data) )
        {
        	$formData = $form->getValues();
        	$backUrl = "/Clientinfo/index/clientid/{$formData['client_id']}/pointid/{$formData['point_id']}";

		// И ТУТ ОШИБКА!!!!! !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        	$helper = new ServiceHelper();

	       	$result = $helper->saveService($formData);


			// Логируем
            AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::EDIT_APPLICATION_ON_POINT, $_SERVER['REQUEST_URI'], $formData['client_id'], $formData['point_id']);

        	if ($result == 'cross')
        	{
        		$_SESSION['back_url'] = $backUrl;
			$this->_redirect('/tech-application/add/type/cross/point_id/' . $data['point_id']);
        	}
        	elseif ($result == 'main')
        	{
        		$this->_redirect($backUrl);
        	}
        	elseif ($result == 'add_agree')
        	{
        		$clientContractModel = new ClientContract();
        		$model = Zend_Controller_Action_Helper_Template::createModel($formData['tablelink']);
        		$serviceID = $formData['id'];

        		if ( ! $serviceID)
        		{
        			$serviceID = $model->getLastInsertID();
        		}

        		$clientContractModel->addAgree(
        			$formData['client_id'],
        			$formData['startdate'],
        			Zend_Auth::getInstance()->getStorage()->read()->id,
        			$formData['tablelink'],
        			$serviceID);
        		$this->_redirect($backUrl);
        	}
        	elseif ( is_array($result) )
        	{
        		unset($_SESSION['recalc']);
        		$_SESSION['recalc'] = $result;
        		$this->_redirect('/service/recalc');
        	}
        	elseif ($result == 'error')
        	{
        		var_dump('error');
        		exit;
        	}
        }
        else
        {
        	$errors = $form->getErrors();
        	var_dump($errors);
        	exit;
        }
	}

    public function addAction()
    {
    	$data = $this->_request->getPost();

    	$tarifListModel = new TarifListModel();

		$form = $this->_helper->template->createForm($data['tablelink'], $data['client_id'], $data);

		if ( $form->isValid($data) )
        {
        	$formData = $form->getValues();
        	$backUrl = "/Clientinfo/index/clientid/{$formData['client_id']}/pointid/{$formData['point_id']}";

        	$helper = new ServiceHelper();

        	$result = $helper->saveService($formData);

        	if ($result == 'cross')
        	{
        		$_SESSION['back_url'] = $backUrl;
				$this->_redirect('/tech-application/add/type/cross/point_id/' . $data['point_id']);
        	}
        	elseif ($result == 'main')
        	{
        		$this->_redirect($backUrl);
        	}
        	elseif ($result == 'error')
        	{
        		var_dump('error');
        		exit;
        	}
        }
        else
        {
        	$errors = $form->getErrors();
        	var_dump($errors);
        	exit;
        }
    }

    public function editAction()
    {
    	$dynamicUnlimModel = new DynamicUnlimModel();

    	$tableLink  = $this->_request->getParam('tablelink');
    	$clientID   = $this->_request->getParam('client_id');
    	$pointID    = $this->_request->getParam('point_id');
    	$ID 		= $this->_request->getParam('id');

    	$this->view->tablelink = $tableLink;
    	$this->view->clientID  = $clientID;
    	$this->view->serviceID = $ID;

		if ( ! $tableLink )
        {
            $this->_redirect('/auth/login');
        }

    	$clientModel 	  = new ClientModel();
    	$serviceModel 	  = new EditPointModel();
    	$serviceTypeModel = new ServiceType();
    	$serviceTypeID 	  = $serviceTypeModel->getServiceTypeID($tableLink, $pointID);

    	$client = $clientModel->getClientByID($clientID);

    	if ($ID == 'new')
    	{
    		$serviceData = array(
    			'id'				=> $ID,
    			'client_id' 		=> $clientID,
    			'point_id' 			=> $pointID,
    			'servicetype_id' 	=> $serviceTypeID);
    	}
    	else
    	{
	        $serviceData = $serviceModel->getServiceInfo($tableLink, $ID);
	        $model 		 = $this->_helper->template->createModel($tableLink);

	        if ( method_exists($model, 'getAdditionalInfo') )
	        {
	            $serviceData += $model->getAdditionalInfo($ID);
	        }
    	}

		if($serviceData['group_name'] == 'special')
		{
			$dynamicData = $dynamicUnlimModel->getSpeedAndPrice($serviceData['id']);

			$serviceData['speed'] 		= $dynamicData['speed'];
			$serviceData['tarif_price'] = $dynamicData['tarif_price'];
		}

        $tarifForm = $this->_helper->template->createForm($tableLink, $clientID, $serviceData);
        $tarifForm->setAction('/service/save');

   		if ( $client['client_type_id'] == 0 )
    	{
    		$this->view->isCorp = true;
    		$services = $serviceModel->getServices($pointID, $tableLink);

    		$first = true;
    		$contractNumber = 1;
    		$contracts = array();

    		$lastServiceID = 0;

    		foreach ($services as $service)
    		{
    			if ($first)
    			{
    				$contracts[$service['id']] = 'Договор';
    				$first = false;
    			}
    			else
    			{
    				$contracts[$service['id']] = "Доп соглашение {$contractNumber}";
    				$contractNumber++;
    			}
    		}

    		$this->view->contracts = $contracts;
    	}

        $this->view->tarifForm = $tarifForm;

        $additionlFrom = new Form_AdditionalWriteOffs();
        $additionlFrom->populate($serviceData);
        $this->view->formAdditional = $additionlFrom;

        // Addon
        $notesModel = new NotesModel();
        $listNotes  = $notesModel->getListNotes($pointID);
        $this->view->listNotes = $listNotes;
        // END addon

        $this->view->tableLink =  $tableLink;
        $this->view->serviceID = $ID;
        $this->view->pointID = $pointID;
        $_SESSION['back_url'] = $_SERVER['REQUEST_URI'];
    }

    public function recalcAction()
    {
    	if ($this->_request->isPost())
    	{
    		$recalcAction = $this->_request->getPost('recalc_action');
    		$data = $_SESSION['recalc'];

    		$pointModel = new EditPointModel();
    		$clientServiceModel = new ClientServicesModel();
    		$clientID = $pointModel->getClientID($data['point_id']);

    		if ($recalcAction != 'recalc')
    		{
    			unset($_SESSION['recalc']);
    			$this->_redirect("/clientinfo/index/clientid/{$clientID}");
    		}

    		$clientModel = new ClientModel();
    		$clientServiceModel = new ClientServicesModel();
    		$financeModel = new FinanceModel();

    		try
    		{
	    		$financeModel->startTransaction();
	    		$clientModel->unforceServices($clientID, $data['year'], $data['point_id']);

	    		$changedService = null;

	    		foreach ($data['all_services'] as $services)
	    		{
	    			foreach ($services as $service)
	    			{
	    				$clientServiceModel->saveDates($service['tablename'], $service['id'],
	    					$service['startdate'], $service['enddate'], true, $service['tarif_id']);

	    				if ($service['old_tarif_id'])
	    				{
	    					$changedService = $service;
	    				}
	    			}
	    		}

	    		$amount = 0;

	    		foreach ($data['compare'] as $row)
	    		{
	    			$amount += $row['recalc_amount'] - $row['invoice_amount'];
	    		}

	    		if ($amount < 0)
	    		{
	    			$tranType = FinanceHelper::RecalcPlus;
	    			$amount = -$amount;
	    		}
	    		else
	    		{
	    			$tranType = FinanceHelper::RecalcMinus;
	    		}

				$userID = Zend_Auth::getInstance()->getStorage()->read()->id;
	    		$financeModel->addTransaction($clientID, 0, $tranType, $amount,  $userID, $data['point_id']);

	    		$asrHelp = new AsrHelp();
	    		$tranName =  $asrHelp->getAsrTypeName(13, $tranType);

	    		$invoiceID = InvoiceHelper::getInvoiceID($financeModel->getAdapter(), $clientID, date('Y-m-d'));
	    		InvoiceHelper::insertOtherTran($financeModel->getAdapter(), date('Y-m-d'), $invoiceID,
	    										$data['point_id'], $tranType, $tranName, $amount);

	    		if ($changedService)
	    		{
	    			$recalcModel = new Recalcs();
	    			$recalcModel->save($clientID, $changedService, $amount, $userID);
	    		}

	    		$financeModel->commitTransaction();

	    		unset($_SESSION['recalc']);
	    		$this->_redirect("/clientinfo/index/clientid/{$clientID}/pointid/{$service['point_id']}");
    		}
    		catch(Exception $ex)
    		{
    			print $ex;
    			$financeModel->rollBackTransaction();
    			exit;
    		}
    	}
    	else
    	{
    		// TODO
    		//var_dump($_SESSION['recalc']);
    		//exit();
			$data = $_SESSION['recalc'];

			$this->view->data = $data['compare'];
			$this->view->services = $data['all_services'];
			$this->view->pointID = $data['point_id'];
    	}
    }

    public function saveStreamParamsAction()
    {
        $data = $this->saveTarif();

        $tableLink = $this->_request->getParam('tablelink');
        $ID  = $data['id'];

        $point_model = new EditPointModel();
        $serviceData = $point_model->getServiceInfo($tableLink, $ID);

        $auth = Zend_Auth::getInstance();
        $manager = $auth->getStorage()->read();

        $clientContractModel = new ClientContract();
        $contract = $clientContractModel->createRow();
        $contract->client_id          = $serviceData['client_id'];
        $contract->manager_id         = $manager->id;
        $contract->contract_type_id = 2;
        $contractID = $contract->save();

        $this->view->serviceData = $serviceData;
        $this->view->tableLink = $tableLink;
        $this->view->id = $ID;
        $this->view->contractID = $contractID;
    }

    public function printaddagreeAction()
    {
        require_once ('AdditionalAgreePdf.php');

        $tableLink  = $this->_request->getParam('t');
        $ID  	    = $this->_request->getParam('i');
        $contractID = $this->_request->getParam('c');
        $type_agree = $this->_request->getParam('type');


        $pointServiceModel = new EditPointModel();
        $serviceData = $pointServiceModel->getServiceInfo($tableLink, $ID);

        if ($serviceData['tablename'] != 'adsl'
        	&& $serviceData['tablename'] != 'wifi')
		{
        	$serviceData['tarif_price'] = ServiceHelper::getAbonPrice($serviceData);
		}

        $clientContractModel = new ClientContract();
        if ($contractID)
        {
        	$contractRow = $clientContractModel->getContractByID($contractID);
        }
        else
        {
        	$contractRow = $clientContractModel->getContractByService($serviceData['tablename'], $serviceData['id']);

        	if ( ! $contractRow || ! $contractRow['contract_id'] )
        	{
        		$contractRow = $clientContractModel->getContractByDate($serviceData['client_id'], $serviceData['startdate']);
        	}
        }

        $clientModel = new ClientModel();
        $clientInfo = $clientModel->getClientInfo($serviceData['client_id']);
        $clientInfo['start_service'] = $serviceData['startdate'];

        $rschets    = $clientModel->rschetClient($serviceData['client_id']);

        $connector = '';
        foreach ($rschets as $rschet)
        {
            $rschetText .= $connector . $rschet['schet'];
            $connector = ',';
        }

        $clientInfo[0]['rschets']   = $rschetText;

	    $clientInfo[0]['bank_name'] = $clientInfo[0]['bank_name'];
        $pdfHelper = new AdditionalAgreePdfHelper(Zend_Registry::get('rekvizits'));
        $pdfHelper->setClientInfo($clientInfo[0]);
        $serviceData['bank_name'] = $clientInfo[0]['bank_name'];
        $pdfHelper->startPrint($contractRow, $serviceData, $type_agree);
    }

    public function deliveryinvoiceAction()
    {
        $infoModel    = new ClientModel();
		$invoiceTarif = new TarifListModel();

        $data = $this->_request->getPost();

        if($data['status'] == 0)
        {
        	$tarif_id = $invoiceTarif->getTarifDeliveryInvoice();
        	$data['tarif_id'] = $tarif_id;

        	$this->view->services = $infoModel->getAllDeliveryInvoice($data['client_id']);

            $form = new Form_Delivery();
            $form->populate($data);
            $this->view->form = $form;
        }
        else
        {
            $result = $infoModel->deleteDeliveryInvoice($data);

            InvoiceHelper::removeDelivery($infoModel->getAdapter(), $data['client_id'], date('Y-m-d'));
            InvoiceHelper::insertDelivery($infoModel->getAdapter(), $data['client_id'], date('Y-m-d'));

            $endMonth = date('Y-m-t');
			$startNextMonth = date('Y-m-d', strtotime('+1 day ' . $endMonth));
            SchetHelper::deleteRow($infoModel->getAdapter(), $data['client_id'], $startNextMonth, 9999, 2);

            $this->_redirect($_SESSION['back_url']);
        }
    }

    public function savedeliveryinvoiceAction()
    {
        // ID юзера который делает что то
        $auth = Zend_Auth::getInstance();
        $user = $auth->getStorage()->read();

        $model = new ClientModel();
        $data = $this->_request->getPost();
        $data['userid'] = $user->id;

        $result = $model->setDeliveryInvoice($data);

        $tranType = FinanceHelper::OuterService;
        $asrHelp = new AsrHelp();
    	$tranName =  $asrHelp->getAsrTypeName(13, $tranType);

    	InvoiceHelper::insertDelivery($model->getAdapter(), $data['client_id'], date('Y-m-d'));
    	$endMonth = date('Y-m-t');
		$startNextMonth = date('Y-m-d', strtotime('+1 day ' . $endMonth));
		SchetHelper::deleteRow($model->getAdapter(), $data['client_id'], $startNextMonth, 9999);
    	SchetHelper::insertAmount($model->getAdapter(), $data['client_id'], $startNextMonth, 9999, 2);

        $this->_redirect($_SESSION['back_url']);
    }

    public function showStatAction()
    {
    	$pointID = $this->_request->getParam('p');
    	$table = $this->_request->getParam('t');
    	$ID = $this->_request->getParam('i');

    	$this->view->table = $table;
    	$this->view->ID = $ID;

    	$serviceTypeModel = new ServiceType();
	    $serviceType = $serviceTypeModel->getServiceType($table, $ID);

	    $startDay = '01';
	    $startMonth = date('m', strtotime('-1 month'));
	    $startYear = date('Y', strtotime('-1 month'));
	    $endDay = '01';
	    $endMonth = date('m');
	    $endYear = date('Y');

	    $phoneServiceCallsModel = new PhoneServicesCalls();
		$phoneServiceTarifModel = new PhoneServicesTarifs();

    	if ( $this->_request->isPost() )
    	{
    		$startDay = $this->_request->getParam('start_day');
    		$startMonth = $this->_request->getParam('start_month');
    		$startYear = $this->_request->getParam('start_year');
    		if ($startDay < 10)
    		{
    			$startDay = '0' . $startDay;
    		}
    		$startDate = "{$startYear}-{$startMonth}-{$startDay}";

    		$endDay = $this->_request->getParam('end_day');
    	    if ($endDay < 10)
    		{
    			$endDay = '0' . $endDay;
    		}
    		$endMonth = $this->_request->getParam('end_month');
    		$endYear = $this->_request->getParam('end_year');
    		$endDate = "{$endYear}-{$endMonth}-{$endDay}";

    		if ($serviceType == 3000)
	    	{
	    		$radacctModel = new Radacct();
	    		$data = $radacctModel->getTrafficStats($pointID, $startDate, $endDate);
	    	}
	    	elseif ($serviceType == 7000)
	    	{
	    		$mysqlModel = new MysqlTraffic('mysqlAdapter');
	    		$table = $mysqlModel->getCorpTableName($pointID);
	    		$data = $mysqlModel->getTraffic($table, $startDate, $endDate);
	    	}
	    	elseif ($serviceType == 7030
	    		|| $serviceType == 7060)
	    	{
	    		$isAll = $this->_request->getParam('all_numbers');
				$number = '';

	    		if ( ! $isAll )
	    		{
	    			$number = $this->_request->getParam('number');
	    		}

	    		$this->view->number = $number;
	    		$this->view->isAll = $isAll;

	    		if ( ! $number )
	    		{
	    			$numbers = $phoneServiceCallsModel->getNumbers($serviceType, $pointID);
	    		}
	    		else
	    		{
	    			$numbers = array($number);
	    		}

	    		$calls = $phoneServiceCallsModel->getCalls($startDate, $endDate, $numbers);
	    		$tarifs = $phoneServiceTarifModel->getList($startDate, $endDate);

	    		$reportModel = new ReportModel();
	    		$data = $reportModel->getCalls($calls, $tarifs);
	    		$keys = array();

	    		foreach ($data as $key => $item)
	    		{
	    			$keys[$key] = $item['abonent1'];
	    		}

	    		array_multisort($keys, SORT_ASC, $data);
	    	}
    	}

    	if ($serviceType == 3000 || $serviceType == 7000)
	    {
	    	$actionName = 'show-traffic-stat';
	    }
	    elseif ($serviceType == 7030 || $serviceType == 7060)
	    {
	    	$numbers = $phoneServiceCallsModel->getNumbers($serviceType, $pointID);

	    	foreach ($numbers as $number)
	    	{
	    		$numberOptions[$number] = $number;
	    	}

	    	$this->view->numbers = $numberOptions;

	    	$actionName = 'show-phone-calls-stat';
	    }

	    $this->view->startDay = $startDay;
    	$this->view->startMonth = $startMonth;
    	$this->view->startYear = $startYear;

    	$this->view->endDay = $endDay;
    	$this->view->endMonth = $endMonth;
    	$this->view->endYear = $endYear;

	$this->view->data = $data;
	$this->render($actionName);
    }

    public function writeOffsAction()
    {
    	$auth = Zend_Auth::getInstance();
        $user = $auth->getStorage()->read();

    	// Доп. Списания
		if($this->_request->isPost())
		{
			$dataForm = $this->_request->getPost();

			$model = new ClientModel();
			$dataForm['userid'] = $user->id;
			$model->startAdditionalOffs($dataForm);

			$invoiceID = InvoiceHelper::getInvoiceID($model->getAdapter(), $dataForm['client_id'], date('Y-m-d'));
			InvoiceHelper::insertOtherTran($model->getAdapter(), date('Y-m-d'), $invoiceID, $dataForm['point_id'],
										FinanceHelper::OuterService, InvoiceHelper::OUTER_SERVICE_LABEL, $dataForm['amount']);

		    //Логируем
            AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::WRITE_OFFS, $_SERVER['REQUEST_URI'], $dataForm['client_id'], $dataForm['point_id']);

			$this->_redirect($_SESSION['back_url']);
		}
    }

    public function addNotesAction()
    {
        if($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            $auth = Zend_Auth::getInstance();
            $user = $auth->getStorage()->read();
            $data['uid'] = $user->id;

            $notesModel = new NotesModel();
            $notesModel->addNotes($data);
            $this->_redirect($_SESSION['back_url']);
        }
    }

    public function deleteModemAction()
    {
    	$modemModel = new ModemModel();
    	$financeModel = new FinanceModel();
    	$clientModel = new ClientModel();

    	$modemID = $this->_request->getParam('id');
    	$modem = $modemModel->getByID($modemID);
    	$clientID = $modem['client_id'];
    	$pointID = $modem['point_id'];

    	$transactions = $financeModel->getTransactions($clientID, 1011, null, null, null, $pointID);

    	if ( is_array($transactions) && is_array($transactions[0]) )
    	{
    		$tran = $transactions[0];

    		$auth = Zend_Auth::getInstance();
            $user = $auth->getStorage()->read();

    		$tranData['trantype'] = 90;
    		$tranData['summa'] = $tran['summa'];
    		$tranData['client_id'] = $clientID;
    		$tranData['servicetype'] = $tran['servicetype'];
    		$tranData['userid'] = $user->id;
    		$tranData['summas'] = $tran['summas'];
    		$tranData['point_id'] = $pointID;

    		try {
	    		$financeModel->startTransaction();

	    		$financeModel->insert($tranData);
	    		$modemModel->delete("modem_id = {$modemID}");
	    		$clientModel->increaseBallance($clientID, $tran['summa']);
	    		AbonDepartmentHistoryHelper::addAbonLog(
	    			AbonDepartmentHistoryHelper::REMOVE_MODEM,
	    			$_SERVER['REQUEST_URI'],
	    			$clientID,
	    			$pointID);

	    		$financeModel->commitTransaction();
    		}
    		catch(Exception $ex) {
    			print $ex;
    			$financeModel->rollBackTransaction();
    		}
    	}

    	$this->_redirect($_SESSION['back_url']);
    }

    public function addModemAction()
    {
    	$modemModel   = new ModemModel();
    	$financeModel = new FinanceModel();
    	$clientModel  = new ClientModel();

    	$data  = $this->_request->getParams();

		if($data['modem_id_var'])
		{
			//update
			if($data['modem_serial_var'] != "" and $data['modem_price_var'] != "")
			{
				$modemModel->updateModem($data['modem_serial_var'], $data['modem_price_var'], $data['modem_id_var']);
				jQuery('div#modem_info')->html('&nbsp;&nbsp; <b>Данные о модеме успешно сохранены</b>');
			}

		}
		else
		{
			//insert
			if($data['modem_serial_var'] != "" and $data['modem_price_var'] != "")
			{
				$modemModel->addNewModem($data);
				jQuery('div#modem_info')->html('&nbsp;&nbsp; <b>Модем записан!</b>');
			}
			else
			{
				jQuery('div#modem_info')->html('&nbsp;&nbsp; <b>Внимание!!! Заполните все поля</b>');
			}
		}

		$this->getResponse()->appendBody(jQuery::getResponse());
        exit;
    }

    public function atsBonusAction()
    {
    	$clientID = $this->_request->getParam('client_id');
    	$clientModel = new EditPointModel();
    	$points = $clientModel->getClientPoints($clientID);
    	$form = new AtsBonusForm();
    	$this->view->form = $form;

    	if ($this->_request->isPost())
    	{
    		$data = $this->_request->getPost();
    		$data['points'] = $points;

    		if ($form->isValid($data))
    		{
    			$formData = $form->getValues();
    			$clientServiceModel = new ClientServicesModel();
    			$services = $clientServiceModel->getPointServices($formData['point_id'], $formData['startdate'], $formData['enddate']);

    			$startDate = strtotime($formData['startdate']);
    			$endDate = strtotime($formData['enddate']);
    			$total = 0;

    			if ( ! is_array($services) )
    			{
    				$this->_redirect("/clientinfo/index/clientid/{$clientID}");
    			}

    			foreach ($services as $item)
    			{
    				if ($item['startdate'] <= $formData['startdate']
    					&& $item['enddate'] >= $formData['enddate'])
    				{
    					$day = ($endDate - $startDate) / 86400 + 1;
    					$sum = $item['tarif_price'] / date('t', $startDate) * $day;
    				}
    				elseif ($item['startdate'] > $formData['startdate']
    					&& $item['enddate'] >= $formData['enddate'])
    				{
    					$serviceStartDate = strtotime($item['startdate']);
    					$day = ($endDate - $serviceStartDate) / 86400 + 1;
    					$sum = $item['tarif_price'] / date('t', $serviceStartDate) * $day;
    				}
    				elseif ($item['startdate'] <= $formData['startdate']
    					&& $item['enddate'] < $formData['enddate'])
					{
    					$serviceEndDate = strtotime($item['enddate']);
    					$day = ($serviceEndDate - $startDate) / 86400 + 1;
    					$sum = $item['tarif_price'] / date('t', $serviceEndDate) * $day;
					}
					else
					{
						$serviceStartDate = strtotime($item['startdate']);
						$serviceEndDate = strtotime($item['enddate']);
    					$day = ($serviceEndDate - $serviceStartDate) / 86400 + 1;
    					$sum = $item['tarif_price'] / date('t', $serviceStartDate) * $day;
					}

					$total += $sum;
    			}

    			$auth = Zend_Auth::getInstance();
            	$user = $auth->getStorage()->read();
    			$atsBonusModel = new AtsBonus();
    			$bonusID = $atsBonusModel->add($clientID, $formData['point_id'], $formData['startdate'], $formData['enddate'], $total, $formData['notes'], $user->id);

    			$financeModel = new FinanceModel();

    			if (count($services) && $services[0]['client_type_id'] === 0)
    			{
    				$financeModel->addTransaction($clientID, $services[0]['servicetype_id'], FinanceHelper::RecalcPlus, $total, $user->id, $formData['point_id'], 'Пересчет ' . $bonusID);
    				$invoiceID = InvoiceHelper::getInvoiceID($financeModel->getAdapter(), $clientID, date('Y-m-d')) ;
    				InvoiceHelper::insertOtherTran($financeModel->getAdapter(), date('Y-m-d'), $invoiceID,
    											$formData['point_id'], FinanceHelper::RecalcPlus,
    											'Снятие при пересчете ' . $bonusID, $total);
    			}

    			$this->_redirect("/clientinfo/index/clientid/{$clientID}");
    		}
    	}
    	else
    	{
    		$data['points'] = $points;
    		$form->populate($data);
    	}
    }
}

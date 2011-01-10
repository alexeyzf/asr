<?php
/*
 * Контроллер позволяет просмотреть инфу о клиенте
 * после поиска
 */
require_once ('BaseController.php');

require_once ('ClientModel.php');

require_once ('ClientHelper.php');
require_once ('ServiceHelper.php');
require_once ('FormHelper.php');
require_once ('Asr/FinanceHelper.php');
require_once ('Asr/InvoiceHelper.php');

require_once ('forms/Addclient.php');
require_once ('forms/Overdraft.php');
require_once ('forms/Makeaccount.php');
require_once ('forms/Startdate.php');
require_once ('forms/ChangeReservedTarifForm.php');
require_once ('forms/EngineerCall.php');

require_once ('Arhivservices.php');
require_once ('AddPoint.php');
require_once ('TarifListModel.php');
require_once ('ClientModel.php');
require_once ('SchetPdf.php');

class ClientinfoController extends BaseController
{
    public function indexAction()
    {
        $client_id = $this->_request->getParam('clientid');
        $point_id  = $this->_request->getParam('pointid');

        // Все расчетники клиента
        $this->view->rschet = ClientHelper::selectRschet($client_id);

        $clienInfoModel  = new ClientModel();
        $labelModel      = new PointStatuses();
        $allPoints       = new AddPoint();

        $dataClient     = $clienInfoModel->getPointInfo($client_id);
        $point_list     = $allPoints->getInfo($point_id, $client_id);


        $tableArr = array
        (
            0 => 'adsl',
            1 => 'collacation',
            2 => 'ngn',
            3 => 'hosting',
            4 => 'tasix',
            5 => 'wifi',
            6 => 'vpn',
            7 => 'isdn',
            8 => 'tradtel',
            9 => 'dialup',
            10 => 'pintel'
        );

        $serviceData = array();
        $clearArr      = array();

        $expectedData= array();
        $expectedArr = array();

        $flag = 1;

        for($i = 0; $i < count($tableArr); $i++)
        {
            array_push($serviceData, $clienInfoModel->showServices($client_id, $tableArr[$i], 2));
            array_push($expectedData, $clienInfoModel->showServices($client_id, $tableArr[$i], $flag));

            if($serviceData[$i])
            {
                array_push($clearArr, $serviceData[$i]);
            }
            if($expectedData[$i])
            {
                array_push($expectedArr, $expectedData[$i]);
            }
        }

        $statusInvoiceD = $clienInfoModel->getDeliveryInvoice($client_id, $dataClient[0]['point_id']);

        if($statusInvoiceD)
        {
            $this->view->statusInvoice = 1;
        }
        else
        {
            $this->view->statusInvoice = 0;
        }

        $_SESSION['local_back_url'] = $_SERVER['REQUEST_URI'];
        $_SESSION['back_url']         = $_SERVER['REQUEST_URI'];

        $form = new Form_Overdraft();
        $form->populate($dataClient);
        $this->view->form = $form;

        // Данные о клиенте
        $this->view->dataclient  = $dataClient;
        $this->view->servicedata = $clearArr;
        $this->view->pointlist   = $point_list;

        $this->view->expectedList =$expectedArr;

    }

    public function makeaccountAction()
    {
    	$clientID = $this->_request->getParam('client_id');
    	$form = new Form_Makeaccount();
    	$this->view->form = $form;

    	$clientModel = new ClientModel();

    	if ($this->_request->isPost())
    	{
    		$formData = $this->_request->getPost();

    		if ($form->isValid($formData))
    		{
    			$documentType = intval($form->getValue('document_type'));
    			$startMonth = $form->getValue('start_month');
    			$startYear = $form->getValue('start_year');

    			$schetsModel = new Schets();

    			$clientInfo = $clientModel->getClientInfo($clientID);
    			if (is_array($clientInfo))
    			{
    				$clientInfo = $clientInfo[0];
    			}

    			$clientInfo['bank'] = $clientInfo['bank_name'];
    			$clientInfo['city'] = $clientInfo['town'];

        		$rschets = $clientModel->rschetClient($clientID);

		        $connector = '';
        		foreach ($rschets as $rschet)
        		{
           			$rschetText .= $connector . $rschet['schet'];
		      		$connector = ',';
        		}

        		$clientInfo['rschets'] = $rschetText;

    			switch ($documentType)
    			{
    			case 1: //Счет
					$schetDetails = $schetsModel->getData($clientID, $startMonth, $startYear);

					if ( ! $schetDetails )
					{
                       	// Нету счета, что означает что у
                        // клиента первый счет и он новый
                        $schetDetails = $schetsModel->getNewSchet($clientID, $startMonth, $startYear);
                    }

					$withoutPrepay = $form->getValue('without_prepay');

					$clientModel = new ClientModel();
    				$rschets = $clientModel->rschetClient($clientID);
                    $isTreasury = false;
                    $rekvizits = Zend_Registry::get('rekvizits');

					foreach($rschets as $rschet)
					{
						if ($rschet == $rekvizits->treasury_account)
						{
							$isTreasury = true;
							break;
						}
					}

                    $schetPdfHelper = new SchetPdfHelper($rekvizits, $withoutPrepay, $isTreasury);
                    $schetPdfHelper->setClientInfo($clientInfo);

                    $number = $schetDetails[0]['number'];

                    if (! $number)
                    {
                    	$number = date('y/m', strtotime("01/{$startMonth}/{$startYear}"));
					}

                    $schetPdfHelper->getPdf(
                    	$clientInfo['contract_number'],
                        $clientInfo['dateagree'],
                        $number,
                        $schetDetails[0]['lastdate'],
                        $schetDetails,
                        $clientInfo['ballance'],
                        $clientID,
                        1);
				break;

                case 2:
                    $invoiceModel = new InvoiceModel();
                    $clientModel = new ClientModel();
                    $ballanceLogModel = new BallanceLogs();

                    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                    $pdf->SetFont('arial', '', 8);
                    $pdf->AddPage();
                    $pdf->setPrintHeader(false);
                    $pdf->setPrintFooter(false);

                    $dataArr = $invoiceModel->getInvoice($startMonth, $startYear, $clientID);
                    $invoice = $dataArr[0];

                    $beforeDate = date('Y-m-d', strtotime("{$startYear}-{$startMonth}-01"));
                    $afterDate = date('Y-m-d', strtotime("+1 month {$startYear}-{$startMonth}-01"));
                    $ballanceBeforeData = $ballanceLogModel->getBallanceLogs($beforeDate);
                    $ballanceAfterData = $ballanceLogModel->getBallanceLogs($afterDate);
                    $ballanceData = array();
                    $ballanceData['before']['date'] = $beforeDate;
                    $ballanceData['before']['amount'] = $ballanceBeforeData[$clientID];
                    $ballanceData['after']['date'] = $afterDate;
                    $ballanceData['after']['amount'] = $ballanceAfterData[$clientID];

                    if ( ! $invoice )
                    {
						$clientName = $clientModel->getClientName($clientID);
                        $data['client_name'] = $clientName;
                        $form->populate($data);
                        $this->view->message = "Счет-фактуры нет по данному клиенту за данный месяц";
                    }
                    else
                    {
                    	if ( $invoice['bank_id'] )
                        {
                        	$invoice['bank']     = $clientModel->getBankName($invoice['bank_id']);
                        }

                        if ( $invoice['country_id'] )
                        {
                        	$invoice['city']     = $clientModel->getCityName($invoice['country_id']);
                        }

                        $rschets = $clientModel->rschetClient($clientID);
                    	$isTreasury = false;
                    	$rekvizits = Zend_Registry::get('rekvizits');

						foreach($rschets as $rschet)
						{
							if ($rschet == $rekvizits->treasury_account)
							{
								$isTreasury = true;
								break;
							}
						}

                        $invoice['rschets']  = $rschets[0];

                        $content .= SchetFactura::getPdfSchetFakturaHeader($invoice);

                        $data_for_client = $invoiceModel->getServiceForSchet($invoice['invoice_id']);
                        $content .= SchetFactura::getBodyPdf($data_for_client, $ballanceData, $isTreasury, $invoice['lastdate']);
                        $content .= SchetFactura::getFooterPdf($data_for_client, $invoice['lastdate'], $isTreasury, $clientID);
                        //print $htmlcontent;
                        $pdf->writeHTML($content, true, 0, true, 0);
                        $pdf->Output("client_{$clientID}_invoice_{$startMonth}_{$startYear}.pdf", 'I');
                        exit ();
                    }
                break;

				case 3: //Акт сверки
					$ballanceLogModel = new BallanceLogs();
					$transactionModel = new FinanceModel();
					$invoiceModel = new InvoiceModel();
					$ballance = $ballanceLogModel->getBallanceOnDate("01.01.{$startYear}", $clientID);
					$transactions = $transactionModel->getSortedTransactions($clientID, $startYear, $startMonth);
					$invoices = $invoiceModel->getClientAmounts($clientID, $startYear, $startMonth);

					$revisionActPdfHelper = new RevisionActPdfHelper(Zend_Registry::get('rekvizits'));
					$revisionActPdfHelper->setClientInfo($clientInfo);
					$revisionActPdfHelper->getPdf($startYear, $ballance[0], $ballance[1], $transactions, $invoices, $startMonth);
				break;

				default:
				break;
    			}
    		}
    	}
    	else
    	{
    		$clientName = $clientModel->getClientName($clientID);
    		$data['client_name'] = $clientName;
    		$form->populate($data);
    	}
    }

    public function setAsEmployeeAction()
    {
        $clientID = $this->_request->getParam('c');
        $pointID = $this->_request->getParam('p');

        $model = new ClientModel();
        $model->markAsEmployee($clientID);

        // Логируем
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::AS_EMPLOYEE, $_SERVER['REQUEST_URI'], $clientID, $pointID);

        $this->_redirect("/clientinfo/index/clientid/{$clientID}/pointid/{$pointID}");
    }

    public function setAsDonateAction()
    {
        $clientID = $this->_request->getParam('c');
        $pointID = $this->_request->getParam('p');

        $model = new ClientModel();
        $model->markAsDonate($clientID);

        // Логируем
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::AS_DONATE, $_SERVER['REQUEST_URI'], $clientID, $pointID);


        $this->_redirect("/clientinfo/index/clientid/{$clientID}/pointid/{$pointID}");
    }

    public function blockstreamAction()
    {
    	if($this->_request->isPost())
    	{
    		$model = new ClientModel();

			$flag      = $this->_request->getPost('flag');
	        $client_id = $this->_request->getPost('client_id');
	        $day_off   = $this->_request->getPost('day_off');
	        $month_off = $this->_request->getPost('month_off');
	        $year_off  = $this->_request->getPost('year_off');
	        $pointID   = $this->_request->getPost('point_id');

	        $date = $year_off. "-". $month_off. "-". $day_off;
	        $model->setBlock($date, $client_id, $flag);

	        $pointInfo = $model->getInfo($pointID);

            TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::POINT_BLOCKED);
            AbonDepartmentHistoryHelper::addAbonLog($pointInfo['statuscross_label'], $_SERVER['REQUEST_URI'], $pointInfo['client_id']);

	        $this->_redirect($_SESSION['back_url']);
    	}
    }

    /**
     * Снятия за вызов специалиста
     */
    public function engineerAction()
    {
    	$form = new Form_EngineerCall();
    	$this->view->form = $form;
    	$clientID = $this->_request->getParam('client_id');
    	$clientModel = new ClientModel();
    	$client = $clientModel->fetchRow("client_id = {$clientID}");
    	$this->view->clientName = stripslashes($client['client_name']);

    	if ($this->_request->isPost())
    	{
    		if ($form->isValid($this->_request->getPost()))
    		{
    			$data = $form->getValues();

    			$auth    = Zend_Auth::getInstance();
        		$manager = $auth->getStorage()->read();
        		$manager = $manager->id;
        		$clientTypeID = $client['client_type_id'];
        		$pointID = $data['point_id'];

				// нужно переделать. Вызов менеджера

				if($data['call'] == 5)
				{
					$data['call'] = 10;
				}
        		$amount = $data['call'];

        		$clientServiceModel = new ClientServicesModel();
        		$service = $clientServiceModel->getService("point_id = {$pointID}");

        		if ($service['servicetype_id'])
        		{
        			$financeModel = new FinanceModel();

        			try
        			{
        				$financeModel->startTransaction();

        				$financeModel->addTransaction($clientID, $service['servicetype_id'], FinanceHelper::EngineerCall,
        						$amount, $manager, $pointID, '');
						if($clientTypeID != 1)
						{
						    $invoiceID = InvoiceHelper::getInvoiceID($financeModel->getAdapter(), $clientID, date('Y-m-d'));
        					InvoiceHelper::insertOtherTran($financeModel->getAdapter(), date('Y-m-d'), $invoiceID, $pointID,
        					FinanceHelper::EngineerCall, InvoiceHelper::OUTER_SERVICE_LABEL, $amount);
						}
        				$financeModel->commitTransaction();
        			}
        			catch(Exception $ex)
        			{
        				print $ex;
        				$financeModel->rollBackTransaction();
        				exit;
        			}
        		}

		        $this->_redirect($_SESSION['back_url']);
    		}
    	}
    	else
    	{
    		$form->populate(array('client_id' => $clientID));
    	}

        /*$clientID       = $this->_request->getParam('client_id');
        $clientTypeID   = $this->_request->getParam('client_type_id');
        $call           = $this->_request->getParam('call');

        // user_id
        $model   = new ClientModel();

        $auth    = Zend_Auth::getInstance();
        $manager = $auth->getStorage()->read();
        $manager = $manager->id;

        try
        {
            $model->startTransaction();
            $model->callEngineer($clientID, $clientTypeID, $call, $manager);
            $model->commitTransaction();
        }
        catch(Exception $ex)
        {
            $model->rollbackTransaction();
            print $ex;
            exit;
        }

        $this->_redirect($_SESSION['back_url']);*/
    }

    public function balancecarryingAction()
    {
    	/**
    	 *  Перенос баланса
    	 */

        $data = $this->_request->getPost();

        $model = new AddPoint();


        $auth    = Zend_Auth::getInstance();
        $manager = $auth->getStorage()->read();
        $manager = $manager->id;

		// Логируем
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::BALLANCE_CARRYING, $_SERVER['REQUEST_URI'], $data['client_id'], $data['point_id']);

      	if($data['u_login'] != "")
    	{
      		$result = $model->ballanceCarryingOver($data['client_id'], $manager, trim($data['u_login']));
    	}
    }

    public function dropuncrossAction()
    {
    	$point_id = $this->_request->getPost('point_id');
		$model    = new EditPointModel();
		$data     = $model->deleteDataPoint($point_id);
		$this->_redirect($_SESSION['back_url']);
    }

    public function perekrosDemandsAction()
    {
        if($this->_request->isPost())
        {
            $data = $this->_request->getPost();

            $model = new PerekrosModel();
            $model->insertData($data);

            // Логируем
            AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::POINT_ADDED_TO_PEREKROS, $_SERVER['REQUEST_URI'], $data['client_id'], $data['point_id']);

            $this->_redirect($_SESSION['back_url']);
        }
    }

    public function returnForConnectionAction()
    {
    	if($this->_request->isPost())
    	{
    		$data = $this->_request->getPost();

			// ID юзера который делает что то
        	$auth = Zend_Auth::getInstance();
        	$user = $auth->getStorage()->read();

			$data['userid'] = $user->id;

			$clientModel = new ClientModel();

			try
			{
				$row = $clientModel->getAndReturnConnectTransactionsSumm($data['client_id'], $data);
			}
			catch (Exception $e)
			{
			    echo 'Exception: ',  $e->getMessage(), "\n";
			}

			if($row == 1)
			{
				$messages[] = "Нечего возвращать!";
			}
			elseif($row == 2)
			{
				$messages[] = "Транзакция успешно возвращена на баланс!";
			}
			elseif($row == 3)
			{
				$messages[] = "Возврат уже осуществлялся!";
			}
			elseif($row == 4)
			{
				$messages[] = "Точка уже подключена! Операция отменена!";
			}

			$this->view->mess = $messages;
    		$this->view->url_need = $_SESSION['back_url'];
			$this->_helper->layout->setLayout('iframe-redirector');
    	}
    }

    public function changeFromReservedAction()
    {
    	$tarifListModel = new TarifListModel();

    	$data = $this->_request->getParams();
    	if($this->_request->isPost() and $data['unikey'] != "")
    	{
    		$pointID   = $data['point_id'];
    		$serviceID = $data['service_id'];
    		$tarifID   = $data['tarif_id'];
    		$startdate = $data['startdate_year']. '-'.$data['startdate_month'].'-'.$data['startdate_day'];

			ServiceHelper::activateStreamFromReserved($pointID, $serviceID, $tarifID, $startdate);
			$this->_redirect($_SESSION['back_url']);
    	}
    	else
    	{
    		$flag = $tarifListModel->getServiceFuture($data['tarif_id'], $data['service_id'], 'adsl', $data['point_id']);
			$data['flag_activ'] = $flag;

			$form = new Form_ChangeReservedTarifForm();
        	$form->populate($data);
			$this->view->form = $form;
    	}
    }

}

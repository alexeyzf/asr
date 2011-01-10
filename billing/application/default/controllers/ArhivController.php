<?php
require_once ('BaseController.php');
require_once ('Arhivservices.php');
require_once ('InvoiceModel.php');
require_once ('Asr/FinanceHelper.php');

class ArhivController extends BaseController
{
    public function indexAction()
    {
        $client_id = $this->_request->getParam('client_id');
        $point_id  = $this->_request->getParam('point_id');
        $arhiv = new Arhivservices();

        $arrTables = array (
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

        $arr = array();

        for($i=0; $i<count($arrTables); $i++)
        {
            array_push($arr,$arhiv->getArhivService($client_id, $point_id, $arrTables[$i]));
        }

        $this->view->arhiv = $arr;
    }

    public function switchserviceAction()
    {

        $client_id = $this->_request->getParam('client_id');
        $point_id  = $this->_request->getParam('point_id');
        $tablelink = $this->_request->getParam('tablename');
        $id        = $this->_request->getParam('sid');
        $tarif_id  = $this->_request->getParam('tarif');
        $flag      = $this->_request->getParam('flag');

        $restoreModel = new Arhivservices();
        $del_row = $restoreModel->setOnOffService($tablelink, $point_id, $tarif_id, $id, $client_id, $flag);

    	//Логируем
		AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::SERVICE_OFF, $_SERVER['REQUEST_URI'], $client_id, $point_id);

        $this->_redirect($_SESSION['back_url']);
    }

    public function deletePointCrossProblemAction()
    {
    	$backUrl = $this->_request->getParam('back_url');

    	if ($this->_request->isPost())
    	{
    		$pointID = $this->_request->getParam('point_id');

    		$pointModel = new EditPointModel();
    		$point = $pointModel->fetchRecordByID($pointID);
    		$service = $pointModel->getFirstCrossService($pointID);

    		$financeModel = new FinanceModel();
    		$registerTransactions = $financeModel->getTransactions($point['client_id'], FinanceHelper::Register, $service['servicetype_id']);

    		if (is_array($registerTransactions) && $registerTransactions[0])
    		{
    			$returnAmount = $registerTransactions[0]['summa'];

    			if ($returnAmount > 0)
    			{
    				$clientModel = new ClientModel();
    				$clientModel->increaseBallance($point['client_id'], $returnAmount);
    				$financeModel->addTransaction($point['client_id'],
					$service['servicetype_id'],
					FinanceHelper::RegisterReturn,
					$returnAmount,
					Zend_Auth::getInstance()->getStorage()->read()->id,
					$pointID);

    				$data['resolved_code'] = '0';
    				$data['point_id'] = $pointID;
    				$pointModel->updateCrossState($data);
    				$clientModel   = new ClientModel();
    				TechHistoryHelper::addLogFromPoint($clientModel->getInfo($pointID), TechHistoryHelper::STATUS_CHANGED);

    				/*$pointModel->deleteDataPoint($pointID);
    				$pointModel->closeIpByPoint($pointID);*/
    			}
    		}
    	}

    	$this->_redirect($backUrl);
    }

    public function setProblemStatuscrossAction()
    {
		if($this->_request->isPost())
		{
			$pointID = $this->_request->getPost('point_id');

			$arhivModel = new Arhivservices();
			$arhivModel->setStatuscrossUncross($pointID);
			$this->_redirect('/clientproblem/index');
		}
    }

    public function terminatecontractAction()
    {
        /**
        *  Метод расторгает контракт с клиентом
        *  @param $client_id (POST) - ID клиента
        */
        $client_id = $this->_request->getPost('client_id');
        $point_id = $this->_request->getPost('point_id');
        $client_type_id = $this->_request->getPost('client_type_id');

        $dropModel     = new Arhivservices();
        $invoiceModel  = new InvoiceModel();

        $count     = $dropModel->quantityOfServices($client_id);

        if( $count > 0 )
        {
            $this->view->trable = "Ай яй яй! А ведь у нашего клиента все
                                еще есть активные услуги! Сначала
                                отключите данные услуги!";
        }
        else
        {

        if($client_type_id == 0)
        {
            $tableArr = array (
                7000 => 'adsl',
                7050 => 'collacation',
                7040 => 'hosting',
                5000 => 'ngn',
                3100 => 'tasix',
                7020 => 'wifi',
                8000 => 'vpn',
                7030 => 'isdn',
                7060 => 'tradtel',
                9999 => 'additional_services',
                1000 => 'dialup'
            );

            //$month = intval(date('n')) - 1;
			$month = date('n', strtotime('-1 month'));
			$lastDayInMonth = date('t', strtotime('-1 month'));


            $startdateM = date('Y-') . $month . '-01';
            $enddateM   = date('Y-') . date('m') . '-01';
            $lastdate   = date('Y-') . $month.'-' . $lastDayInMonth;



                $services = array();
                foreach($tableArr as $key => $value)
                {
                    $serviceObj = $invoiceModel->getServiceByID($client_id, $value);

                    for( $s = 0; $s <count($serviceObj); $s++ )
                    {
                        $services[] = $serviceObj[$s];
                        $logins[$serviceObj[$s]['u_login']] = $serviceObj[$s]['u_login'];
                    }
                }
                $traf = array();

                /**
                *  Так как не все услуги клиента могут присутствовать в таблице RADACCT
                *  мы смотрим если запрос getServiceByID ничего не вернул " if($logins == NULL) " значит
                *  у данного клиента нет вообще ни одной услуги. И соответственно пропускаем этого клиента
                *  и переходим на следующего
                */

                if($logins == NULL)
                {

                }
                else
                {

                    foreach ($logins as $login)
                    {
                        $traf[$login] = $invoiceModel->getTraffic($login, $startdateM, $enddateM);
                    }

                    // Номер счёт фактуры

                    if($services[0]['client_id'] == "")
                    {

                    }
                    else
                    {

                    $invoiceID = $invoiceModel->insertInvoice($services[0]['client_id'], $services[0]['contract_number'], $lastdate);

                    foreach ($services as $service)
                    {
                        $serviceType = ($service['servicetype_id'] == 7000 ? 3000 : $service['servicetype_id']);

                        $service['month']             = $this->getMonthRus(intval($month));
                        $service['last_invoice_id']   = $invoiceID;
                        if (is_array($traf[$service['u_login']][$serviceType]))
                        {
                            $inputTrafic         = $traf[$service['u_login']][$serviceType]['input'];
                            $outputTrafic         = $traf[$service['u_login']][$serviceType]['output'];
                            $service['input']     = $service['traffic'] - $inputTrafic;
                            $service['output']     = $service['traffic'] - $outputTrafic;
                            $service['month']   = $this->getMonthRus(intval($month));

                            $excess = 0;
                            if( $service['input'] < 0 )
                            {
                                $excess  =  -1 * $service['input'] * ($invoiceModel->getTariffExcess($service['tarif_id']));
                            }

                            if( $service['output'] < 0 )
                            {
                                $excess += -1 * $service['output'] * ($invoiceModel->getTariffExcess($service['tarif_id']));
                            }
                            $service['excess'] = $excess;
                        }

                        $invoiceModel->insertInvoiceDetails($service);

                    }
                    }

                }
        }
        	// ХИСТОРИ ADDON
			$modelArhiv = new ClientsArhiv();
			$points 	= $modelArhiv->getPointsByClientID($client_id);

			foreach($points as $value)
			{
				TechHistoryHelper::addLogFromPointID($value['point_id'], TechHistoryHelper::CONTRACT_TERMINATED);
			}
			// ADDON END

            $rowForDrop  = $dropModel->dropClientInArhiv($client_id);

			//Логируем
            AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::TERMINATE_CONTRACT, $_SERVER['REQUEST_URI'], $client_id, $point_id);

            $this->_redirect('/Searchclient');
        }
    }

    public function lifeupclientAction()
    {
        $client_id = $this->_request->getParam('client_id');
        $point_id  = $this->_request->getParam('point_id');

        $this->view->point_id  = $point_id;
        $this->view->client_id = $client_id;

        $lifeupModel = new Arhivservices();
        $restoringClientAndPoint = $lifeupModel->restoreClientAndAllPoints($client_id, $point_id);

        $this->view->client_name = $restoringClientAndPoint;
    }


    public function getMonthRus($monthNumber)
    {

        $arr_of_month = array (
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        );

        return $arr_of_month[$monthNumber];
    }

}
?>

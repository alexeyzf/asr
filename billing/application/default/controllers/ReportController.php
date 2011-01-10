<?php
require_once ('BaseController.php');
require_once ('ReportModel.php');
require_once ('forms/DatePeriod.php');
require_once ('forms/MonthDatePeriod.php');
require_once ('Zend/Date.php');
require_once 'helpers/NovaHelper.php';
require_once 'forms/RegionAndMonthSelectionForm.php';
require_once 'models/TarifListModel.php';

class ReportController extends BaseController
{
    public function indexAction()
    {

    }

    public function allreportAction()
    {
    	$reportModel = new ReportModel();

    	$report_type    = $this->_request->getPost('report_type');

		$arr = $reportModel->getReport($report_type);

		if($arr)
		{
			$this->view->data 		 = $arr;
			$this->view->report_type = $report_type;
		}
		else
		{
			$arr[0]['title'] = 'По данному отчету нет записей';
			$this->view->data = $arr;
			$this->view->report_type = $report_type;
		}
    }

    public function shareUserReportAction()
    {
        if($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            $reportModel = new ReportModel();
            $result = $reportModel->getShareStatistic($data);

            $this->view->data = $result;
        }
    }

    public function totalReportAction()
    {
        if($this->_request->isPost())
        {
            $kitModel       = new KitModel();
            $tarifListModel = new TarifListModel();


            $year  		= $this->_request->getParam('year');
            $month 		= $this->_request->getParam('month');
            $flag 		= $this->_request->getParam('flag');


            if (in_array($flag, array('stream', 'private')))
            {
                $resultData = $kitModel->getClientsByTarifAllStream($year, $month, $flag);
                $arr = array();
                for($i = 0; $i <count($resultData); $i++)
                {
                    $arr[$resultData[$i]['tarif_id']][] = $resultData[$i];
                }
            }
            else
            {
                $resultData = $kitModel->getClientsByStreamNovaTarif($year, $month);
                $arr = array();
                foreach ($resultData as $row)
                {
                    $row['tarif_name'] = NovaHelper::getNameForDownSpeed($row['speed']);
                    $arr[NovaHelper::getNameForDownSpeed($row['speed'])][] = $row;
                }
            }

			$this->view->dataArr = $arr;
			$this->view->month = $month;
			$this->view->year = $year;
	    }
	    $this->view->month = date('m');
        $this->view->year = date('Y');
    }

    public function generateReestrAction()
    {
    	if ( $this->_request->isPost() )
    	{
    		$reportModel = new ReportModel();
    		$type = $this->_request->getParam('type');

    		$startMonth = $this->_request->getParam('start_month');
    		$startYear = $this->_request->getParam('start_year');
    		$startDate = "{$startYear}-{$startMonth}-01";

    		$endMotnh = $this->_request->getParam('end_month');
    		$endYear = $this->_request->getParam('end_year');
    		$endDate = date('Y-m-t', strtotime("{$endYear}-{$endMotnh}-01"));

    		$this->view->startDate = $startDate;
    		$this->view->endDate = $endDate;

    		switch ($type)
    		{
    			case 'by-service':
    				$this->view->data = $reportModel->getReestrByService($startDate, $endDate);
    				$this->render('reestr-by-service');
    				break;
    			case 'by-client':
    				$this->view->data = $reportModel->getReestrByClient($startDate, $endDate);
    				$this->render('reestr-by-client');
    				break;
    			case 'by-client-service':
    				$this->view->data = $reportModel->getReestrByServiceNClient($startDate, $endDate);
    				$this->render('reestr-by-client-service');
    				break;
    			case 'by-client-group-service':
    				$this->view->data = $reportModel->getReestrByServiceNClient($startDate, $endDate, 'servicename');
    				$this->render('reestr-by-client-group-service');
    				break;
    		}
    	}
    }

    public function generateIncomeAction()
    {
    	if ( $this->_request->isPost() )
    	{
    		$reportModel = new ReportModel();
    		$type = $this->_request->getParam('type');

    		$startDay = $this->_request->getParam('start_day');
    		$startMonth = $this->_request->getParam('start_month');
    		$startYear = $this->_request->getParam('start_year');
    		$maxDay = date('t', strtotime("{$startYear}-{$startMonth}-01"));

    		if ($startDay > $maxDay)
    		{
    			$startDay = $maxDay;
    		}

    		$startDate = date('Y-m-d', strtotime("{$startDay}.{$startMonth}.{$startYear}"));

    		$endDay = $this->_request->getParam('end_day');
    		$endMonth = $this->_request->getParam('end_month');
    		$endYear = $this->_request->getParam('end_year');
    		$maxEndDay = date('t', strtotime("{$endYear}-{$endMonth}-01"));

    		if ($endDay > $maxEndDay)
    		{
    			$endDay = $maxEndDay;
    		}

    		$endDate = date('Y-m-d', strtotime("{$endDay}.{$endMonth}.{$endYear}"));

    		$this->view->startDate =  $startDate;
    		$this->view->endDate = $endDate;

    		$reportModel = new ReportModel();

    		switch ($type)
    		{
    			case 'by-type':
    				$incomes = $reportModel->getIncomesByTypes($startDate, $endDate);

    				foreach ($incomes as $type => $item)
    				{
    					if ( $item['trantype'] == 3
    						|| $item['trantype'] == 77
							|| $item['trantype'] == 6
							|| $item['trantype'] == 9
							|| $item['trantype'] == 12
							|| $item['trantype'] == 20
							|| $item['trantype'] == 40
							|| $item['trantype'] == 44
							)
						{
							$incomes1[$type] = $item;
						}
						elseif ($item['trantype'] == 1
							|| $item['trantype'] == 25)
						{
							$incomes3[$type] = 	$item;
						}
						else
						{
							$incomes2[$type] = $item;
						}
    				}

					foreach($incomes1 as $value)
					{
						if( $value['trantype'] == "3"
							or $value['trantype'] == "6"
							or $value['trantype'] == "77"
							or $value['trantype'] == "44")
						{
							$summasUZS = $summasUZS + $value['amount_uzs'];
							$summasUSD = $summasUSD + $value['amount_usd'];
						}
					}

					$this->view->addSummasUZS = $summasUZS;
					$this->view->addSummasUSD = $summasUSD;

    				$this->view->incomes1 	  = $incomes1;
    				$this->view->incomes2 	  = $incomes2;
    				$this->view->incomes3 	  = $incomes3;

    				$this->render('incomes-by-type');
    				break;

    			case 'by-client':
    				$this->view->data = $reportModel->getIncomesByClients($startDate, $endDate);
    				$this->render('incomes-by-client');
    				break;

    			case 'by-service':
    				$this->view->data = $reportModel->getIncomesByServices($startDate, $endDate);
    				$this->render('incomes-by-service');
    				break;

    			case 'by-paynet':
    				$this->view->data = $reportModel->getIncomesByPaymentSystems($startDate, $endDate, 9, 2006);
    				$this->view->pay_type = ' через PAYNET';
    				$this->render('incomes-by-client');
    				break;

    			case 'by-kb':
    				$this->view->data = $reportModel->getIncomesByPaymentSystems($startDate, $endDate, 6, 1001);
    				$this->view->pay_type = ' через выносные кассы КБ';
    				$this->render('incomes-by-client');
    				break;

    			case 'by-mobliss':
    				$this->view->data = $reportModel->getIncomesByPaymentSystems($startDate, $endDate, 12, 2005);
    				$this->view->pay_type = ' через Mobliss';
    				$this->render('incomes-by-client');
    				break;


    		}
    	}
    }


    public function overdraftAction()
    {
        $model = new ReportModel();
        $this->view->data = $model->getOverdraftClients();
    }


    public function incomesByTypeDetailsAction()
    {
    	$reportModel = new ReportModel();
    	$asrHelp = new AsrHelp();

    	$type = $this->_request->getParam('type');
    	$startDate = $this->_request->getParam('start');
    	$endDate = $this->_request->getParam('end');

		// Addon
		$startDate = $startDate. " 00:00:00";
		$endDate   = $endDate. " 23:59:59";
		// End

    	$this->view->typeName = $asrHelp->getAsrTypeName(AsrHelp::FINANCIAL_TRANSACTIONS_TYPE, $type);
    	$this->view->startDate = $startDate;
    	$this->view->endDate = $endDate;
    	$this->view->data = $reportModel->getIncomesDetails($type, $startDate, $endDate);
    }

    public function portsReportAction()
    {
        $reportModel = new ReportModel();

        $this->view->hubs = $reportModel->getHubIDs(); // ID узлов
        $this->view->atss = $reportModel->getAtsIDs(); // ID АТС-ок
    }

    public function unpayedPortsAction()
    {
    	$clientServiceModel = new ClientServicesModel();
    	$services = $clientServiceModel->getServices(0, 1);

    	$all = array();
    	$unpayed = array();
    	$result = array();

    	foreach ($services as $service)
    	{
    		if ($service['ballance'] >= -5)
    		{
    			continue;
    		}

    		if ($service['port_state'])
    		{
    			array_push($all, $service);
    		}

    		if ($service['vip'] || $service['is_employee']
    			|| $service['is_donate'] || $service['overdraft'] == 31)
    		{
    			array_push($unpayed, $service);
    		}
    	}


    	foreach ($all as $row)
    	{
    		$found = false;

    		foreach ($unpayed as $urow)
    		{
    			if ($urow['tablename'] == $row['tablename']
    				&& $urow['service_id'] == $row['service_id'])
    			{
    				$found = true;
    			}
    		}

    		if ( ! $found )
    		{
    			array_push($result, $row);
    		}
    	}

    	$this->view->unpayed = $unpayed;
    	$this->view->result = $result;
    }

    private function matchInvoiceDetails($source, $old, $new)
    {
        $olds = array();
        $news = array();
        foreach ($source as $row)
        {
            if ($row['lastdate'] == $old)
            {
                $olds[] = $row;
            }
            else
            {
                $news[] = $row;
            }
        }
        $result = array();
        // сопоставляем строки с одинаковыми servicename и total $notUseTotal -> false
        // сопоставляем строки с одинаковыми servicename $notUseTotal -> true
        $pairs = array();
        foreach ($olds as $oldIndex => $oldRow)
        {
            foreach (array(false, true) as $useTotal)
            {
                foreach ($news as $newIndex => $newRow)
                {
                    if ($newRow['servicename'] == $oldRow['servicename'] && ($newRow['total'] == $oldRow['total'] and $useTotal))
                    {
                        $alreadyUsed = false;
                        for ($i = 0; $i < count($pairs[0]); $i++)
                        {
                            if ($pairs[0][$i] == $oldIndex || $pairs[1][$i] == $newIndex)
                            {
                                $alreadyUsed = true;
                                break;
                            }
                        }
                        if (!$alreadyUsed)
                        {
                            $pairs[0][] = $oldIndex;
                            $pairs[1][] = $newIndex;
                            break;
                        }
                    }
                }
            }
        }
        //var_dump($pairs); exit;
        for ($i = 0; $i < count($pairs[0]); $i++)
        {
            $old = $olds[$pairs[0][$i]];
            $new = $news[$pairs[1][$i]];
            $result[] = array(
                'client_id' => $old['client_id'],
                'client_name' => $old['client_name'],
                'service_name' => $old['servicename'],
                'old' => array('usd' => $old['total'], 'uzs' => $old['total_uzs']),
                'new' => array('usd' => $new['total'], 'uzs' => $new['total_uzs'])
                );
        }
        // заполняем оставшиеся строки справа
        foreach ($olds as $index => $old)
        {
            if (!in_array($index, $pairs[0]))
            {
                $result[] = array(
                    'client_id' => $old['client_id'],
                    'client_name' => $old['client_name'],
                    'service_name' => $old['servicename'],
                    'old' => array('usd' => $old['total'], 'uzs' => $old['total_uzs']),
                    'new' => array('usd' => 0.0, 'uzs' => 0.0)
                );
            }
        }
        // заполняем оставшиеся строки слева
        foreach ($news as $index => $new)
        {
            if (!in_array($index, $pairs[1]))
            {
                $result[] = array(
                    'client_id' => $new['client_id'],
                    'client_name' => $new['client_name'],
                    'service_name' => $new['servicename'],
                    'old' => array('usd' => 0.0, 'uzs' => 0.0),
                    'new' => array('usd' => $new['total'], 'uzs' => $new['total_uzs'])
                );
            }
        }
        return $result;
    }

    public function compareInvoicesAction()
    {
    	if ($this->_request->isPost())
    	{

	    	$monthFirst  = $this->_request->getParam('month_first');
	    	$monthSecond = $this->_request->getParam('month_second');
	    	$yearFirst   = $this->_request->getParam('year_first');
	    	$yearSecond  = $this->_request->getParam('year_second');

	    	$is_detaled  = $this->_request->getParam('is_detaled');

	    	$lastDateFirst = date('Y-m-t', strtotime("01.{$monthFirst}.{$yearFirst}"));
	    	$lastDateSecond = date('Y-m-t', strtotime("01.{$monthSecond}.{$yearSecond}"));

	    	$invoiceModel = new InvoiceModel();

	    	$this->view->monthFirst = $monthFirst;
	    	$this->view->monthSecond = $monthSecond;
	    	$this->view->yearFirst = $yearFirst;
	    	$this->view->yearSecond = $yearSecond;
            $this->view->lastDateFirst = $lastDateFirst;
			$this->view->lastDateSecond = $lastDateSecond;

	    	$data = array();
			if($is_detaled)
			{
				$clientIDs = $invoiceModel->getAllCorpClientsInvoices();
				foreach($clientIDs as $key => $value)
				{
                    $data = $invoiceModel->getInvoicesFor2Month($lastDateFirst, $lastDateSecond, $value['client_id']);
                    $dataRows[$value['client_id']] =
                        $this->matchInvoiceDetails($data, $lastDateFirst, $lastDateSecond);
				}
				$this->view->data = $dataRows;
				$this->render("compare-invoices-details");
			}
			else
			{
				$firstInvoices  = $invoiceModel->getInvoicesTotalAmounts($lastDateFirst);
	    		$secondInvoices = $invoiceModel->getInvoicesTotalAmounts($lastDateSecond);

		    	foreach ($firstInvoices as $clientID => $invoice)
		    	{
		    		$data[$clientID]['client_name'] = $invoice['client_name'];
		    		$data[$clientID]['address'] = $invoice['address'];
		    		$data[$clientID]['phone'] = $invoice['phone'];
		    		$data[$clientID]['first_amount'] = $invoice['amount'];
		    		$data[$clientID]['first_amount_uzs'] = $invoice['amount_uzs'];
		    		$data[$clientID]['second_amount'] = $secondInvoices[$clientID]['amount'];
		    		$data[$clientID]['second_amount_uzs'] = $secondInvoices[$clientID]['amount_uzs'];
		    	}
			}
	    	$this->view->data = $data;
    	}
    	else
    	{
    		$this->view->monthFirst = date('m', strtotime('-1 month'));
	    	$this->view->monthSecond = date('m', strtotime('-2 months'));
	    	$this->view->yearFirst = date('Y', strtotime('-1 month'));
	    	$this->view->yearSecond = date('Y', strtotime('-2 months'));
    	}
    }

    public function getDiscountClientsAction()
    {
		$reportModel = new ReportModel();


		if($this->_request->isPost())
		{
			$type  = $this->_request->getPost('ctype');
			$year  = $this->_request->getPost('year');
			$month = $this->_request->getPost('month');

			$this->view->year = $year;
    		$this->view->month = $month;
    		$this->view->ctype = $type;

			if ( ! $type || ! $year || ! $month)
			{
				return;
			}

    		$startDate = date('Y-m-d', strtotime("01/{$month}/{$year}"));
    		$endDate = date('Y-m-t', strtotime("01/{$month}/{$year}"));

    		$this->view->countDays = date('t', strtotime("01/{$month}/{$year}"));

			$data = $reportModel->getPrivateClients($type, $startDate, $endDate);

			$this->view->data = $data;
			$this->view->countDays = date('t', strtotime("01.{$month}.{$year}"));
		}

    }

    public function getWifiClientsAction()
    {
    	$reportModel = new ReportModel();

    	if ($this->_request->isPost())
    	{
    		$year = $this->_request->getParam('year');
    		$month = $this->_request->getParam('month');

    		$this->view->year = $year;
    		$this->view->month = $month;

    		if ( ! $year || ! $month)
			{
				return;
			}

    		$startDate = date('Y-m-d', strtotime("01/{$month}/{$year}"));
    		$endDate = date('Y-m-t', strtotime("01/{$month}/{$year}"));

    		$this->view->countDays = date('t', strtotime("01/{$month}/{$year}"));

    		$this->view->data = $reportModel->getWififClients($startDate, $endDate);
    	}
    }

    public function clientDiscountAction()
    {
		$reportModel = new ReportModel();
		$arr = array();

		if($this->_request->isPost())
		{
			$year  = $this->_request->getPost('year');
			$month = $this->_request->getPost('month');

			$this->view->countDays = date('t', strtotime("01.{$month}.{$year}"));
			$this->view->year = $year;
    		$this->view->month = $month;

			if ( ! $year || ! $month)
			{
				return;
			}

    		$startDate = date('Y-m-d', strtotime("01/{$month}/{$year}"));
    		$endDate = date('Y-m-t', strtotime("01/{$month}/{$year}"));

			$this->view->data = $reportModel->getReportDiscountClientsOldService($startDate, $endDate);
		}
    }

    public function clientDiscountOldAction()
    {
		$reportModel = new ReportModel();
		$data = $reportModel->getDiscountClientsOLD();

		$this->view->data = $data;
    }

    public function kitReportAction()
    {
            if($this->_request->isPost())
            {
                $kitModel       = new KitModel();
                $tarifListModel = new TarifListModel();



                $is_stream      = $this->_request->getParam('is_stream');

                $year  		= $this->_request->getParam('year');
                $month 		= $this->_request->getParam('month');
                $tarif_id       = $this->_request->getParam('tarif_id');

                $allTarifs[0]['tarif_id']  = $tarif_id;

                $resultData    = $kitModel->getClientsByTarif($year, $month, $allTarifs , $is_stream);

                $tarifSpeed    = $tarifListModel->getServiceTarifsReport('adsl', $is_stream, $tarif_id);

                $nowClientsArr = array();

                foreach($resultData as $value)
                {
                    array_push($nowClientsArr, $value);
                }

                $date = $dateNeed = $year."-". $month."-01";

                $this->view->countDays = date('t', strtotime($date));

                $this->view->speed = $tarifSpeed['speed'];
                $this->view->data = $resultData;
                $this->view->nowClients = $nowClientsArr;
                $this->view->tarifData = $tarifSpeed;
	    }
    }

    public function opicalReportAction()
    {
        $reportModel = new ReportModel();

        $data = $reportModel->getopticalClients();

        $this->view->getClients = $data;

    }

    public function modemSellsAction()
    {
        if( $this->_request->isPost() )
        {
            $year = $this->_request->getPost('year');
			$ctype = $this->_request->getPost('ctype');

            $modemModel = new ModemModel();

            $data = $modemModel->getModemStream($year, $ctype);

            $this->view->data = $data;
            $this->view->year = $year;
            $this->view->ctype = $ctype;
        }
    }

    public function realisationCardAction()
    {
    	if($this->_request->isPost())
    	{
            $data = $this->_request->getPost();

            $reportModel = new ReportModel();
            $result = $reportModel->getRealisationClients($data);

            $this->view->data = $result;
    	}

    }

	public function dynamicIncomesAction()
	{
		$year = date('Y');
		$is_stream = 0;

		if($this->_request->isPost())
		{
			$year 	   = $this->_request->getPost('year');
			$is_stream = $this->_request->getPost('is_stream');

			$this->view->year = $year;
			$this->view->isStream = $is_stream;

			$year = $year."-01-01";
			$reportModel = new ReportModel();
			$resultArr = $reportModel->getDynamicRegistry($year, $is_stream);

			$this->view->result    = $resultArr;
			$this->view->is_stream = $is_stream;

		}
	}

	public function showFinanceReportAction()
	{
		$form = new Form_DatePeriod();
		$this->view->form = $form;

		if ( $this->_request->isPost() )
		{
			$data = $this->_request->getPost();

			if ( $form->isValid($data) )
			{
				$reportModel = new ReportModel();


				$start = $data['startdate_year']. "-". $data['startdate_month'] . "-" . $data['startdate_day'];
				$end   = $data['enddate_year']. "-". $data['enddate_month'] . "-" . $data['enddate_day'];
				$invoiceDate = date("Y-m-t", strtotime($start));

				$dataInvoices = $reportModel->financeReportStats($start, $end, $invoiceDate);
				$this->view->data = $dataInvoices;
			}
		}
	}

    private function getMonthsArr($startMonth, $finishMonth, $endDate)
    {
        $startMonthParts = explode('-', $startMonth);
    	$endMonthParts = explode('-', $finishMonth);
    	$passedMonths = ((int)$endMonthParts[0]*12 + (int)$endMonthParts[1]) -
    					((int)$startMonthParts[0]*12 + (int)$startMonthParts[1]);

        $year = (int)$startMonthParts[0];
        $month = (int)$startMonthParts[1];
        for ($i = 0; $i <= $passedMonths; $i++)
        {
            $monthStr = sprintf('%02d', $month);
            $lastDate = date('t', strtotime("{$year}-{$month}-01"));
            $result[] = array("{$year}-{$monthStr}-01",
                        $endDate<=$lastDate?"{$year}-{$monthStr}-{$endDate}":"{$year}-{$monthStr}-{$lastDate}");
            $month++;
            if ($month > 12)
            {
                $month = 1;
                $year++;
            }
        }
        return $result;
    }

    private function getCorrectDate($month, $day)
    {
        $date = strtotime($month . '-01');
        if (date('t', $date) >= $day)
        {
            return $month . '-' . $day;
        }
        return $month . '-' . date('t', $date);
    }

    public function crossedLegalClientsAction()
    {
    	// Получаем всех скроссированных корпов
    	$reportModel = new ReportModel();

    	$this->view->data = $reportModel->getCrossedLegalClients(0);
    }

    public function incomeCompareReportAction()
    {
        $form = new Form_MonthDatePeriod();
		$this->view->form = $form;

		if ( $this->_request->isPost() )
		{
			$data = $this->_request->getPost();

			if ( $form->isValid($data) )
			{
				$formData = $form->getValues();
                $months = $this->getMonthsArr(
                        $formData['start_month'],
                        $formData['finish_month'],
                        $formData['day_number']);
                $model = new ReportModel();

                $startDate = $formData['start_month'] . '-01';
                $endDate = $this->getCorrectDate($formData['finish_month'], $formData['day_number']);
                $tranTypes = array('corp' => $model->getUsedTransactionsType($startDate, $endDate, 0),
                                   'stream' => $model->getUsedTransactionsType($startDate, $endDate, 1),
                                   'cards' => $model->getUsedTransactionsType($startDate, $endDate, 4));
                $result = array('corp' => array(), 'stream' => array(), 'cards' => array());
                foreach (array ('corp' => 0, 'stream' => 1, 'cards' => 4) as $clientTypeName => $clientTypeId)
                {
                    foreach ($months as $period)
                    {
                        $resultRow = array();
                        foreach ($tranTypes[$clientTypeName] as $type)
                        {
                            $resultRow[$type['trantype']] = 0.0;
                        }
                        foreach ($model->getIncomesByTypes($period[0], $period[1], $clientTypeId) as $key => $row)
                        {
                            $resultRow[$row['trantype']] = $row['amount_usd'];
                        }
                        $result[$clientTypeName]["{$period[0]}-{$period[1]}"] = $resultRow;
                    }
                }
                $this->view->data = array('types' => $tranTypes, 'trans' => $result, 'months' => $months);
			}
		}
    }

    public function clientsByRegionAction()
    {
        $form = new Form_RegionAndMonthSelection();
        $this->view->form = $form;

        if ( $this->_request->isPost() )
		{
			$data = $this->_request->getPost();
			if ($form->isValid($data))
			{
                $this->view->region = $data['region'];

                $startDate = "{$data['month_year']}-{$data['month_month']}-01";
                $endData = "{$data['month_year']}-{$data['month_month']}-" . date('t', strtotime($startDate));

                $this->view->dates = array('startdate' => $startDate, 'enddate' => $endData);

                $reportModel = new ReportModel();
                foreach ($reportModel->getTarifInfoByRegions($startDate, $endData, $data['region']) as $row)
                {
                    $result[$row['client_type_id']][] = $row;
                }
                $this->view->data = $result;
            }
        }
    }

    public function clientsByRegionAndTarifAction()
    {
        $region = $this->_request->getParam('region');
        $tarif = $this->_request->getParam('tarif');
        $startDate = $this->_request->getParam('startdate');
        $endDate = $this->_request->getParam('enddate');
        
        $model = new ReportModel();
        $this->view->data = $model->getClientsByRegionAndTarif($region, $tarif, $startDate, $endDate);
    }
}

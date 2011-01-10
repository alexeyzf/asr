<?php
require_once ('BaseController.php');
require_once ('ReportModel.php');
require_once ('TarifListModel.php');
require_once ('forms/Reportdateform.php');
require_once ('forms/DatePeriod.php');
require_once ('forms/MonthDatePeriod.php');
require_once ('UtilsHelper.php');
require_once ('models/ClientModel.php');

class ClientreportsController extends BaseController
{
    private function cmp($a, $b)
    {
    	$a = strtotime($a['startdate']);
		$b = strtotime($b['startdate']);
		if ($a == $b)
		{
			return 0;
		}
		return $a > $b ? 1 : -1;
    }

    public function indexAction()
    {
		$form        = new Form_Reportdateform();
		$modelReport = new ReportModel();

		$this->view->form = $form;
		$clientID         = $this->_request->getParam('client_id');

		if ($this->_request->isPost())
		{
			$postData = $this->_request->getPost();

			if ($form->isValid($postData))
			{
				$startDate = date('Y-m-d', strtotime($form->getValue('startdate')));
				$endDate   = date('Y-m-d', strtotime($form->getValue('enddate')));
				$is_stream = $form->getValue('is_stream');

				$data = $modelReport->getReportOnInclusions($startDate, $endDate, $is_stream);

				for($i = 0; $i < count($data); $i++)
				{
					if($data[$i]['tablename'] == "collacation")
					{

						$amountCollacationPrice = $modelReport->getCollacationPrice($data[$i]['service_id']);
						$data[$i]['tarif_price'] = $amountCollacationPrice;
					}
				}
                foreach ($data as $row)
                {
                    $groupedData[$row['client_id']][] = $row;
                }
                foreach (array_keys($groupedData) as $key)
                {
                    usort($groupedData[$key], array($this, 'cmp'));
                }
                /*foreach ($groupedData as $row)
                {
                    $count = 0;
                    foreach ($row as $service)
                    {
                        if ($count != 0 && isset($service['modem_price']) && $service['modem_price'] != 0)
                        {
                            var_dump($row);
                        }
                        $count++;
                    }
                }
                exit;*/
				$this->view->dataResult = $groupedData;
			}
		}
    }

    public function switchoffAction()
    {
    	$form        = new Form_Reportdateform();
		$modelReport = new ReportModel();

		$this->view->form = $form;
		$clientID         = $this->_request->getParam('client_id');

		if ($this->_request->isPost())
		{
			$postData = $this->_request->getPost();

			if ($form->isValid($postData))
			{
				$startDate = date('Y-m-d', strtotime($form->getValue('startdate')));
				$endDate   = date('Y-m-d', strtotime($form->getValue('enddate')));
				$is_stream = $form->getValue('is_stream');
				//var_dump($is_stream);
				//exit();
				//$this->view->dataResult = $modelReport->getReportOnInclusions($startDate, $endDate, $is_stream);
				$uncrossed = $modelReport->getReportOffInclusions($startDate, $endDate, $is_stream);

				foreach ($uncrossed as $key => $point)
				{
					$uncrossed[$key]['date'] = strtotime($point['date']);
					$uncrossed[$key]['client_dateagree'] = strtotime($point['client_dateagree']);
					$days = ($uncrossed[$key]['date'] - $uncrossed[$key]['client_dateagree']) / 60 / 60 / 24;
					$uncrossed[$key]['interval'] = $this->getIntervalString($days);
					$uncrossed[$key]['price'] = $modelReport->getPointLastAbonPrice($point['point_id']);
				}

				$this->view->uncrossed = $uncrossed;

				$pointsForUncross = $modelReport->getPointsForUncross($is_stream);
				$waitingForUncross = array();
				$acceptedForUncross = array();

				foreach ($pointsForUncross as $point)
				{
					if ( ! $point['ballance_change'] )
					{
						$point['ballance_change'] = 'now';
					}

					$point['ballance_change'] = strtotime($point['ballance_change']);
					$point['client_dateagree'] = strtotime($point['client_dateagree']);
					$days = ($point['ballance_change'] - $point['client_dateagree']) / 60 / 60 / 24;
					$point['interval'] = $this->getIntervalString($days);
					$point['price'] = $modelReport->getPointLastAbonPrice($point['point_id']);

					if ($point['statuscross'] == -1)
					{
						array_push($waitingForUncross, $point);
					}
					else
					{
						array_push($acceptedForUncross, $point);
					}
				}

				$this->view->waitingForUncross = $waitingForUncross;
				$this->view->acceptedForUncross = $acceptedForUncross;
			}
		}

    }

    private function getIntervalString($days)
    {
    	$years = floor($days / 365);
		$months = floor(($days - $years * 365) / 30);
		$days = floor($days - $years * 365 - $months * 30);

		$intervalString = '';

		if ($years > 0)
		{
			if ($years == 1)
			{
				$intervalString = '1 год ';
			}
			elseif ($years < 5)
			{
				$intervalString = $years . ' года ';
			}
			else
			{
				$intervalString = $years . ' лет ';
			}
		}

		if ($months > 0)
		{
			if ($months == 1)
			{
				$intervalString .= '1 месяц ';
			}
			elseif ($months < 5)
			{
				$intervalString .= $months . ' месяца ';
			}
			else
			{
				$intervalString .= $months . ' месяцев ';
			}
		}

		if ($days > 0)
		{
			if ($days == 1 || $days == 21)
			{
				$intervalString .= '1 день ';
			}
			elseif ($days < 5
				|| ($days < 25 && $days > 20))
			{
				$intervalString .= $days . ' дня ';
			}
			else
			{
				$intervalString .= $days . ' дней ';
			}
		}

		return $intervalString;
    }

	public function showTotalSwitchReportAction()
	{
		$form = new Form_MonthDatePeriod();
		$this->view->form = $form;
		$modelReport = new ReportModel();

		if ($this->_request->isPost())
		{
			$postData = $this->_request->getPost();
			if ($form->isValid($postData))
			{
				$formData = $form->getValues();
                $months = UtilsHelper::getMonthsArr(
                        $formData['start_month'],
                        $formData['finish_month'],
                        $formData['day_number']);

                $tarifList = new TarifListModel();
                $streamTarifs = $tarifList->getActiveTarifsByNewConnections(1, $months[0][0], $months[count($months)-1][1]);
                $corpTarifs = $tarifList->getActiveTarifsByNewConnections(0, $months[0][0], $months[count($months)-1][1]);

                $reportModel = new ReportModel();
                $data = array();

                foreach ($months as $month) 
                {
                    $temp = array();
                    foreach ($reportModel->getInclusionsSummaryReport($month) as $row)
                    {
                        $temp[$row['client_id']][] = $row;
                    }
                    foreach (array_keys($temp) as $key)
                    {
                        usort($temp[$key], array($this, 'cmp'));
                    }
                    $paymentRow = array('corp' => array('abon' => 0.0, 'reg' => 0.0, 'modem' => 0.0),
                                        'stream' => array('abon' => 0.0, 'reg' => 0.0, 'modem' => 0.0));
                    $amountRow = array('corp' => array(), 'stream' => array());
                    foreach ($streamTarifs as $row)
                    {
                        $amountRow['stream'][$row['service_name']] = 0;
                    }
                    foreach ($corpTarifs as $row)
                    {
                        $amountRow['corp'][$row['service_name']] = 0;
                    }
                    foreach ($temp as $clientServices)
                    {
                        $firstStartDate = $clientServices[0]['startdate'];
                        $clientType = $clientServices[0]['client_type_id'] == 0 ? 'corp' : 'stream';
                        foreach ($clientServices as $service)
                        {
                            if ($service['startdate'] != $firstStartDate)
                            {
                                continue;
                            }
                            
                            $paymentRow[$clientType]['abon'] += $service['tarif_price'];
                            $paymentRow[$clientType]['reg'] += $service['reg_pay'];
                            $paymentRow[$clientType]['modem'] += $service['modem_price'];

                            $amountRow[$clientType][$service['service_name']] += 1;
                        }
                    }
                    $data["{$month[0]} - {$month[1]}"] = array('payment' => $paymentRow, 'amount' => $amountRow);
                }
                //var_dump($data); exit;
                $this->view->data = array('data' => $data, 'streamTarifs' => $streamTarifs, 'corpTarifs' => $corpTarifs);
            }
        }
	}

    public function uncrossReportAction()
    {
        $form = new Form_Reportdateform();
		$this->view->form = $form;

		if ($this->_request->isPost())
		{
			$postData = $this->_request->getPost();

			if ($form->isValid($postData))
			{
				$startDate = date('Y-m-d', strtotime($form->getValue('startdate')));
				$endDate   = date('Y-m-d', strtotime($form->getValue('enddate')));
				$is_stream = $form->getValue('is_stream');
                
                $model = new ClientModel();
                $this->view->data = $model->getDataForUncrossReasonReport($startDate, $endDate, $form->getValue('is_stream'));
                $this->view->clientType = $form->getValue('is_stream');
            }
        }
    }
}
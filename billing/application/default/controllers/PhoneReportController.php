<?php
require_once 'BaseController.php';
require_once('forms/DatePeriod.php');

class Filter
{
	private $_usedNumbers = null;

	public function __construct($useDnumbers)
	{
		$this->_usedNumbers =  $useDnumbers;
	}

	public function filterNotIncluded($var)
	{
		foreach ($this->_usedNumbers as $value)
		{
			if ($value['number'] == $var)
			{
				return false;
			}
		}

		return true;
	}
}

class PhoneReportController extends BaseController
{

	/**
	 * Отображает список доступных отчетов
	 * @return void
	 */
	public function indexAction()
	{

	}

	/**
	 * Отображает отчет по направлениям
	 * @return void
	 */
	public function directionAction()
	{
    	$form = new Form_DatePeriod();
    	$this->view->form = $form;

    	if ($this->_request->isPost())
    	{
    		$postData = $this->_request->getPost();

    		if ($form->isValid($postData))
    		{
    			$startDate = $form->getValue('startdate');
    			$endDate = $form->getValue('enddate');

    			$phoneServicesCalls = new PhoneServicesCalls();
    			$phoneServicesTarifs = new PhoneServicesTarifs();
    			$calls = $phoneServicesCalls->getCalls($startDate, $endDate);
    			$tarifs = $phoneServicesTarifs->getList($startDate, $endDate);

    			$reportModel = new ReportModel();
    			$this->view->data = $reportModel->calulateCalls($calls, $tarifs);

    			$unresolvedNumbers = $reportModel->getUnresolvedNumbers();
    			$this->view->unresolvedNumbers = $unresolvedNumbers;

    			$this->view->unresolvedCalls = $reportModel->calulateCalls($calls, $tarifs, 'abonent1');

    			$this->view->quantCalls = $reportModel->calculateQuantCalls($calls, $tarifs);
    		}
    	}
	}

	/**
	 * Отображает отчет по регионам
	 * @return void
	 */
	public function regionAction()
	{
    	$form = new Form_DatePeriod();
    	$this->view->form = $form;

    	if ($this->_request->isPost())
    	{
    		$postData = $this->_request->getPost();

    		if ($form->isValid($postData))
    		{
    			$startDate = $form->getValue('startdate');
    			$endDate = $form->getValue('enddate');

    			$phoneServicesCalls = new PhoneServicesCalls();
    			$phoneServicesTarifs = new PhoneServicesTarifs();
    			$calls = $phoneServicesCalls->getCalls($startDate, $endDate);
    			$tarifs = $phoneServicesTarifs->getList($startDate, $endDate);

    			$reportModel = new ReportModel();
    			$this->view->data = $reportModel->calulateCalls($calls, $tarifs, 'ut_name');
    		}
    	}
	}

	/**
	 * Отображает детальный отчет по всем преговорам
	 * @return unknown_type
	 */
	public function callsAction()
	{
    	$form = new Form_DatePeriod();
    	$this->view->form = $form;

    	if ($this->_request->isPost())
    	{
    		$postData = $this->_request->getPost();

    		if ($form->isValid($postData))
    		{
    			$startDate = $form->getValue('startdate');
    			$endDate = $form->getValue('enddate');

    			$phoneServicesCalls = new PhoneServicesCalls();
    			$phoneServicesTarifs = new PhoneServicesTarifs();
    			$calls = $phoneServicesCalls->getCalls($startDate, $endDate);
    			$tarifs = $phoneServicesTarifs->getList($startDate, $endDate);

    			$reportModel = new ReportModel();
    			$this->view->data = $reportModel->getCalls($calls, $tarifs);
    		}
    	}
	}

	public function numberCallsAction()
	{
		$number = $this->_request->getParam('number');
    	$year = $this->_request->getParam('year');
    	$month = $this->_request->getParam('month');

    	$this->view->number = $number;
    	$this->view->year = $year;
    	$this->view->month = $month;

    	$startDate = "{$year}-{$month}-01";
    	$endDate = "{$year}-{$month}-31";

    	$phoneServicesCalls = new PhoneServicesCalls();
    	$phoneServicesTarifs = new PhoneServicesTarifs();
    	$reportModel = new ReportModel();

    	$tarifs = $phoneServicesTarifs->getList($startDate, $endDate);
    	$calls = $phoneServicesCalls->getCalls($startDate, $endDate, array($number));
    	$calls = $reportModel->getCalls($calls, $tarifs);

    	$this->view->calls = $calls;
	}

 	public function unresolvedNumbersAction()
    {
    	if ($this->_request->isPost())
    	{
    		$year = $this->_request->getParam('year');
    		$this->view->year = $year;

    		$phoneServicesCalls = new PhoneServicesCalls();
    		$phoneServicesTarifs = new PhoneServicesTarifs();
    		$reportModel = new ReportModel();

    		$startDate = "{$year}-01-01";
    		$endDate = "{$year}-12-31";

    		$unresolvedCalls = $reportModel->getUnresolvedCalls($startDate, $endDate);

    		$monthCalls = array();

    		for ($i  = 1; $i <= 12; $i++)
    		{
    			$monthTime = strtotime("01.{$i}.{$year}");
    			$startDate = date('Y-m-01', $monthTime);
    			$endDate = date('Y-m-t', $monthTime);
    			$tarifs = $phoneServicesTarifs->getList($startDate, $endDate);
    			$monthCalls[$i] = $reportModel->calulateCalls($unresolvedCalls[$i], $tarifs, 'abonent1');
    		}

    		$data = array();

    		foreach ($monthCalls as $month => $calls)
    		{
    			foreach ($calls as $abonent => $call)
    			{
    				$data[$abonent][$month] = $call;
    			}
    		}

    		ksort($data);

    		$this->view->data = $data;
    	}
    }

	public function uncalculateCallsAction()
    {
    	$form = new Form_DatePeriod();
    	$this->view->form = $form;

    	if ($this->_request->isPost())
    	{
    		$postData = $this->_request->getPost();

    		if ($form->isValid($postData))
    		{
    			$startDate = $form->getValue('startdate');
    			$endDate = $form->getValue('enddate');

    			$phoneServicesCalls = new PhoneServicesCalls();
    			$phoneServicesTarifs = new PhoneServicesTarifs();
    			$calls = $phoneServicesCalls->getCalls($startDate, $endDate);
    			$tarifs = $phoneServicesTarifs->getList($startDate, $endDate);

    			$reportModel = new ReportModel();
    			$allCalls = $reportModel->getCalls($calls, $tarifs);

    			$uncalculatedCalls = array();

    			foreach ($allCalls as $call)
    			{
    				if ( ! $call['price'] && strlen("{$call['abonent2']}") > 4
    					&& substr($call['abonent2'], 0, 1) == '8')
    				{
    					$code = substr($call['abonent2'], 0, 3);

    					if ($code == '810')
    					{
    						$code = substr($call['abonent2'], 0, 6);
    					}
    					else
    					{
    						$code = substr($call['abonent2'], 0, 4);
    					}

    					$uncalculatedCalls[$code] += $call['minutes_count'];
    				}
    			}

    			$this->view->data = $uncalculatedCalls;
    		}
    	}
    }

    public function numberAnalizAction()
    {
    	$phoneModel = new PhoneServicesCalls();

		if($this->_request->isPost())
		{
			$year  = $this->_request->getParam("year");
			$month = $this->_request->getParam("month");

			$start = $year. "-". $month. "-01 00:00:00";

			$end   = date("Y-m-t", strtotime($start)). " 23:59:59";

			$this->view->data = $phoneModel->getAnalizNumbersReport($start, $end);
		}
    }

    public function searchByPhoneAction()
    {
    	$phoneModel = new PhoneServicesCalls();

    	if($this->_request->isPost())
    	{
    		$number  = $this->_request->getParam("number");

    		if($number)
    		{
				$this->view->result = $phoneModel->getSearchByNumber($number);
    		}
    	}
    }

    public function attachedNumbersAction()
    {
    	$phoneModel = new PhoneServicesCalls();

    	if($this->_request->isPost())
    	{
    		$stype = $this->_request->getParam("stype");
    		$ctype = $this->_request->getParam("ctype");

			$arr = array();

			$data = $phoneModel->getPhones($stype, $ctype);

			foreach($data as $key => $value)
			{
				$arr[$value['client_id']][] = $value;
			}

			$this->view->result = $arr;

    		$this->view->stype = $stype;
    		$this->view->ctype = $ctype;
    	}
    }



    public function freeNumbersAction()
    {
		$phoneModel = new PhoneServicesCalls();
		if(!$this->_request->isPost())
		{
			$dataDB = $phoneModel->getBusyNumbers();

			$arr = array();

			for($counter = 1130000; $counter < 1134999; $counter++)
			{
				array_push($arr, $counter);
			}

			$filter = new Filter($dataDB);
			$arr = array_filter($arr, array($filter, 'filterNotIncluded'));

			$this->view->result = $arr;
		}
    }
}
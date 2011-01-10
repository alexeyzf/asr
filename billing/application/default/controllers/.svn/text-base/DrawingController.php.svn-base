<?php
require_once('BaseController.php');
require_once('Zend/pchart/pChart/pData.class');
require_once('Zend/pchart/pChart/pChart.class');

class DrawingController extends BaseController
{
	public function init()
	{
		$this->_tahomaPath = realpath("tahoma.ttf");
	}

	private $_tahomaPath;
	
	public function showTrafficFlowAction()
	{
		$ip = $this->_request->getParam('ip');
    	$model = new CorpTraffic();
			
		$bytes_in  = array();
		$bytes_out = array();
		
		$startTime = strtotime('-30 hours');
		$endTime = strtotime('now');
		
		$data = $model->getRowsForDraw($ip, date('Y-m-d H:m:s', $startTime), date('Y-m-d H:m:s', $endTime));
		$DataSet = new pData;
		$minutes5Data = array();
		$first  = true;
		
		foreach($data as $item)
		{
			if ($first) 
			{
				$startTime = strtotime($item['date_time']);
				$first = false;
			} 
			
			$minutes5Data[strtotime($item['date_time'])] = $item;
		}
		
		$max = 50;
		
		for ($time = $startTime; $time <= $endTime; $time += 300)
		{
			if ($minutes5Data[$time])
			{
				$speedIn = $minutes5Data[$time]['input'] * 8 / 5 / 60 / 1024;
				$speedOut = $minutes5Data[$time]['output'] * 8 / 5 / 60 / 1024;
				
				if ($speedIn > $max) {
					$max = ( round($speedIn / 50, 0) + 1) * 50;
				}
				elseif ($speedOut > $max) {
					$max = ( round($speedOut / 50, 0) + 1) * 50;
				}
			}
			else
			{
				$speedIn = 0;
				$speedOut = 0;
			}
			
			//print date('H', $time) . ': ' . $speedIn . ' ' . $speedOut . '<br />';
			
			$DataSet->AddPoint($speedIn,"Serie1", date('H', $time));
			$DataSet->AddPoint($speedOut,"Serie2", date('H', $time));
		}
		//exit;
		
		$DataSet->AddAllSeries();
		$DataSet->SetAbsciseLabelSerie();
		$DataSet->SetSerieName("Входящий","Serie1");
		$DataSet->SetSerieName("Исходящий","Serie2");

		// Initialise the graph
		$Test = new pChart(900, 330);
		$Test->setFontProperties($this->_tahomaPath, 8);
		$Test->setGraphArea(50, 30, 685, 300);
		$Test->drawFilledRoundedRectangle(7, 7, 805, 323, 5, 240, 240, 240);
		$Test->drawRoundedRectangle(5, 5, 807, 325, 5, 230, 230, 230);
		$Test->drawGraphArea(255, 255, 255, TRUE);
		$Test->setFixedScale(0, $max);
		$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), 10, 150, 150, 150, TRUE, 0, 2, false, 12);
		 
		
		// Draw the 0 line
		$Test->setFontProperties($this->_tahomaPath,6);
		$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
		//$Test->drawGrid(4,false,230,230,230,50);
	    // Finish the graph
		$Test->setFontProperties($this->_tahomaPath,12);
		$Test->drawTitle(50, 22, $ip, 50, 50, 50, 585);
		
		$Test->setFontProperties($this->_tahomaPath,10);
		$Test->drawLegend(700,30,$DataSet->GetDataDescription(),255,255,255);
 		$Test->Stroke();
 		exit;
	}

    public function indexAction()
    {
    	$pid = $this->_request->getParam('pid');
    	$model = new CorpTraffic();
    	$this->view->pointID = $pid;
		$ips = explode(' ', $model->getIP($pid));
		$this->view->ips = $ips; 
    }
}

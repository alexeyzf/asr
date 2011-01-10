<?php
require_once ('BaseController.php');

class CompareportsController extends BaseController
{

    public function indexAction()
    {
		$compareModel 	  = new CompareModel();
		$corpTrafficModel = new CorpTraffic();

		$start = date('Y-m-d');
		$end   = date('Y-m-t');

		$data = $compareModel->getAllPorts();

		$arrDownASR = array();

		for($i = 0; $i < count($data); $i++)
		{
                        if($data[$i]['speed'] == "1" or $data[$i]['speed'] == "0")
                        {
                            continue;
                        }

			if($data[$i]['state'] == 1 and $data[$i]['ballance'] < -5 and $data[$i]['overdraft'] < date('j'))
			{
				// Клиенты у кого порт поднят но по фин. деятельности он должен быть отпущен
				$data[$i]['result_compare_ports'] = 1;
			}

                        if($data[$i]['state'] == 1 and $data[$i]['speed'] != "")
			{
				// Клиенты у кого порт поднят но по фин. деятельности он должен быть отпущен
				//$data[$i]['result_compare_speed'] = 1;
				$lastSpeedState = $compareModel->getLastPortSpeed($data[$i]['client_id'], $data[$i]['dslam_id'], $data[$i]['number']);

				if($lastSpeedState != $data[$i]['speed'] and $lastSpeedState != "")
				{
					$data[$i]['result_compare_speed'] = 1;
                                        $data[$i]['last_speed'] = $lastSpeedState;
				}
			}
		}


		$counter = 0;
		for($z = 0; $z < count($data); $z++)
		{
			if($data[$z]['result_compare_ports'] == 1)
			{
				$arr[$counter] = $data[$z];
				$counter++;
			}
		}
		$this->view->arr = $arr;


		$counter = 0;
		for($z = 0; $z < count($data); $z++)
		{
			if($data[$z]['result_compare_speed'] == 1)
			{
				$arrSpeed[$counter] = $data[$z];
				$counter++;
			}
		}
		$this->view->arrSpeed = $arrSpeed;
    }
}
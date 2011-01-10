<?php
require_once ('BaseController.php');
require_once ('TarifListModel.php');

class TarifhistoryController extends BaseController
{

    public function indexAction()
    {
		$point_id  = $this->_request->getParam('point_id');
		$tablename = $this->_request->getParam('tablename');
		$flag = $this->_request->getParam('flag');

		$modelTarif = new TarifListModel();
		$data       = $modelTarif->getTarifChangeHistory($point_id, $tablename);
		for($i = 0; $i< count($data); $i++)
		{
			//$date_start = new Zend_Date($data[$i]['startdate']);
			$data[$i]['startdate'] =  date('Y.m.d', strtotime($data[$i]['startdate']));

			//$date_end = new Zend_Date($data[$i]['enddate']);
			$data[$i]['enddate'] =    date('Y.m.d', strtotime($data[$i]['enddate']));
		}

		$this->view->dataTarif = $data;
		if($flag == 1)
		{
			$this->_helper->layout->setLayout('tarif-change');
		}
    }
}
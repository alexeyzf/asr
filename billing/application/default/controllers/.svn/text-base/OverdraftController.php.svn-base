<?php
require_once('BaseController.php');
require_once('OverdraftModel.php');

class OverdraftController extends BaseController
{
	public function indexAction()
	{
		$overdraftModel = new OverdraftModel();
                $porttasksModel = new Porttasks();

		$client_id = $this->_request->getPost('client_id');
		$on_day    = $this->_request->getPost('overdraft');
		$over_type = $this->_request->getPost('overdraft_type');

		if($client_id != "")
		{
			$result = $overdraftModel->setOverdraft($client_id, $on_day, $over_type);

            $setOn = new KassaModel();

            $arrPoints = $setOn->getOnPoints($client_id);

            for( $i = 0; $i < count($arrPoints); $i++ )
            {
                $pointsArr[]   =  $arrPoints[$i]['point_id'];

                // Логируем
				AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::OVERDRAFT_ADDED, $_SERVER['REQUEST_URI'], $client_id, $arrPoints[$i]['point_id']);
            }

            $porttasksModel->addPointsTasks($pointsArr, 0);
			$this->_redirect($_SESSION['back_url']);
		}
	}

	public function deleteoverdraftAction()
	{
		$overdraftModel = new OverdraftModel();

		$client_id = $this->_request->getParam('client_id');

		if($client_id)
		{
			$overdraftModel->deleteOverdraft($client_id);
			$this->_redirect($_SESSION['back_url']);
		}
	}
}
?>
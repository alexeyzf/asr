<?php
/*
 * Created on 18.08.2009
 * Контроллер который работает с проблемными клиентами,
 * у которых проблемы с кроссом
 */

require_once ('BaseController.php');
require_once ('PointStatuses.php');
require_once ('PointHelper.php');
require_once ('EditPointModel.php');
require_once ('ClientModel.php');
require_once ('TechHistoryHelper.php');
require_once ('DistrictDistributionHelper.php');

class ClientproblemController extends BaseController
{
    public function indexAction()
    {
        $problemListModel = new PointStatuses();
        $listCross           = new AddPoint();

        $clientList       = $problemListModel->getProblemClients();

        $pointStatusesModel = new PointStatuses();
        $statuses_cross     = $pointStatusesModel->getCrossOptions();
        $statuses_uncross   = $pointStatusesModel->getUncrossOptions();

        $clientListCross   = PointHelper::getCrossList(array_keys($statuses_cross));
        $clientListUncross = PointHelper::getCrossList(array_keys($statuses_uncross));

        $this->view->problemlist = DistrictDistributionHelper::distribute($clientList);
        $this->view->optionsView = $pointStatusesModel->getOptions(' resolved_flag = 2 ');

        $this->view->clientlistcross   = $clientListCross;
        $this->view->clientlistuncross = $clientListUncross;
    }

    public function resolveproblemAction()
    {
    	$data_from = $this->_request->getPost();
    	$model 		 = new EditPointModel();
    	$clientModel = new ClientModel();

		$result = $model->updateCrossState($data_from, 1);
        $pointInfo = $clientModel->getInfo($data_from['point_id']);

        TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::STATUS_CHANGED);
        AbonDepartmentHistoryHelper::addAbonLog($pointInfo['statuscross_label'], $_SERVER['REQUEST_URI'], $pointInfo['client_id']);

		$this->_redirect($_SESSION['back_url']);
    }
}
?>
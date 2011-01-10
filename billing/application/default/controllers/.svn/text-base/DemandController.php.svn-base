<?php
/**
 * Controller for tech application pages
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('PointHelper.php');
require_once('TechHistoryHelper.php');
require_once('AbonDepartmentHistoryHelper.php');
require_once ('ListServiceModel.php');
require_once ('EditPointModel.php');
require_once 'AsrHelp.php';

class DemandController extends BaseController
{

    public function indexAction()
    {
        $helper = new AsrHelp();
        $statusses = array(PointHelper::STATUS_CROSS_WAIT_UNCROSS, PointHelper::STATUS_CROSS_ACCEPTED_UNCROSS);

        $points = PointHelper::getPoints($statusses, 'phone_hub_name', 1);
        $this->view->points = $points;
        $this->view->reasons = $helper->getAsrTypes(array('23'));
    }

    /**
     * Action for save modifications from index page
     */
    public function saveAction()
    {
        if ($this->_request->isPost())
        {
            $type = $this->_request->getParam('type');
            $points = $this->_request->getParam('points');

            $pointModel = new AddPoint();
            $clientModel = new ClientModel();

            try
            {
                $clientModel->startTransaction();

                foreach ($points as $pointID => $pointData)
                {
                    if ( $pointData['is_changed'] )
                    {
                        unset($pointData['is_changed']);
                        if (!$pointData['leaving_reason'])
                        {
                            $pointData['leaving_reason'] = null;
                        }
                        $pointModel->saveChanges($pointData, $pointID);
                        $pointInfo = $clientModel->getInfo($pointID);
                        TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::APPLICATION_CHANGED);
                        AbonDepartmentHistoryHelper::addAbonLog($pointInfo['statuscross_label'], $_SERVER['REQUEST_URI'], $pointInfo['client_id']);
                    }
                }
                $clientModel->commitTransaction();
            }
            catch(Exception $ex)
            {
                $clientModel->rollbackTransaction();
                print $ex;
                exit;
            }
        }

        $this->_redirect("/demand/index/type/{$type}");
    }

    /**
     * Action for add application from edit client page
     */
    public function addAction()
    {
        $backUrl = $_SESSION['back_url'];
        $type = $this->_request->getParam('type');
        $pointID = $this->_request->getParam('point_id');

        //Addon
		$modelEditPoint  = new EditPointModel();

        if ($type == 'cross')
        {
				$data['statuscross'] = PointHelper::STATUS_CROSS_WAIT_CROSS;
        }
        elseif ($type == 'uncross')
        {
            $data['statuscross'] = PointHelper::STATUS_CROSS_WAIT_UNCROSS;
        }

        $pointModel = new AddPoint();
        $clientModel = new ClientModel();

        try
        {
            $clientModel->startTransaction();

            $pointModel->saveChanges($data, $pointID);

            $pointInfo = $clientModel->getInfo($pointID);

            TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::APPLICATION_CREATED);
            AbonDepartmentHistoryHelper::addAbonLog($pointInfo['statuscross_label'], $_SERVER['REQUEST_URI'], $pointInfo['client_id']);

            $clientModel->commitTransaction();
        }
        catch (Exception $ex)
        {
            $clientModel->rollbackTransaction();
            print $ex;
            exit;
        }

        $this->_redirect($backUrl);
    }

    /**
     * Откат заявки на раскросс
     */
    public function rollbackUncrossAction()
    {
        $pointID = $this->_request->getPost('point_id');
        $newNote = $this->_request->getPost('notes');
        $data 	 = $this->_request->getPost();

        $clientModel = new ClientModel();
        $pointModel  = new EditPointModel();

        $pointModel->updateCrossState($data); // изменем статус кроссировки
        $pointInfo = $clientModel->getInfo($pointID);
		$pointInfo['notes'] = $newNote;

        if ($pointInfo['client_type_id'] == 1)
        {
        	$pointService = $pointModel->getInfo($pointID, $pointInfo['tablename']);

        	$t = date('Y-m-01', strtotime('+1 month'));

        	if($t == $pointService['paidto']) // Значит абон плата в тек мес снималась.
        	{
				$isPayed = true;
        	}
        	else
        	{
        		$isPayed = false;
        	}
        	$pointModel->continueServices($pointID, $isPayed); //продлеваем даты услуг
        	$portTaskModel = new Porttasks();
        	$portTaskModel->addPointsTasks(array($pointID), Porttasks::TASK_TYPE_ON); //поднимаем порт
        }
        elseif ($pointInfo['client_type_id'] == 0)
        {
        	$pointModel->continueService($pointID, 'additional_services');

        	if ($pointID)
        	{
        		$result = ServiceHelper::demandActivateService($pointID);
        	}
        }

        //логируем
		AbonDepartmentHistoryHelper::addAbonLog(TechHistoryHelper::DEMAND_ROllBACK, $_SERVER['REQUEST_URI'], $pointInfo['client_id']);
        TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::DEMAND_ROllBACK);
        $this->_redirect('/Demand/');
    }

    public function blackListClientAction()
    {
		$debtorModel = new DebtorModel();

		$data = $debtorModel->getAllPointsInBlackList();

		if($data)
		{
			$this->view->data = $data;
		}
    }

}
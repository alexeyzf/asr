<?php
/**
 * Controller for tech application pages
 *
 * @author marat
 */

require_once ('BaseController.php');
require_once ('PointHelper.php');
require_once ('TechHistoryHelper.php');
require_once ('AbonDepartmentHistoryHelper.php');
require_once ('ListServiceModel.php');

class TechApplicationController extends BaseController
{
    /**
     * Action for list of application page
     */
    public function indexAction()
    {
        $type = $this->_request->getParam('type');

        if ($type)
        {
            if ($type == 'cross')
            {
                $statusses = array(PointHelper::STATUS_CROSS_WAIT_CROSS, PointHelper::STATUS_CROSS_ACCEPTED_CROSS);
            }
            elseif ($type == 'uncross')
            {
                $statusses = array(PointHelper::STATUS_CROSS_WAIT_UNCROSS, PointHelper::STATUS_CROSS_ACCEPTED_UNCROSS);
            }

            $points = PointHelper::getPoints($statusses, '', 0);
            $this->view->points = $points;
            $this->view->type = $type;
        }
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

        $this->_redirect("/tech-application/index/type/{$type}");
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
            $modelEditPoint->logStatusCross($pointID);
        }

        $pointModel    = new AddPoint();
        $clientModel   = new ClientModel();
        $portTaskModel = new Porttasks();

        try
        {
            $clientModel->startTransaction();

            $pointModel->saveChanges($data, $pointID);

            $pointInfo = $clientModel->getInfo($pointID);

			if($type == 'uncross')
			{
				$portTaskModel->addTask($pointInfo, Porttasks::TASK_TYPE_OFF);
			}

            TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::APPLICATION_CREATED);
            AbonDepartmentHistoryHelper::addAbonLog($pointInfo['statuscross_label'], $_SERVER['REQUEST_URI'], $pointInfo['client_id'], $pointID);

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
}
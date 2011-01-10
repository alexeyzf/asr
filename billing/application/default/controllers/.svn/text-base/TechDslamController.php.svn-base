<?php
/**
 * Conntroller for tech dslams
 *
 * @author marat
 */

require_once ('BaseController.php');
require_once ('DslamList.php');
require_once ('ClientHelper.php');
require_once ('Ports.php');
require_once ('DslamTypes.php');
require_once ('Ports.php');
require_once ('forms/Techdslam.php');
require_once ('forms/PortStatus.php');
require_once ('AsrHelp.php');

class TechDslamController extends BaseController
{
    /**
     * Action for list of dslams page
     */
    public function indexAction()
    {
        $dslamModel = new DslamList();
        $portModel = new Ports();
        $dslamTypesModel = new DslamTypes();

        if ($this->_request->isPost())
        {
            $atsID = $this->_request->getParam('ats_id');
            $onlyEmpty = $this->_request->getParam('only_empty');

            $dslams = $dslamModel->fetchAllByAts($atsID);
            $dslamTypes = $dslamTypesModel->getOptions();

            $dslamIDs = array();
            foreach ($dslams as $dslam)
            {
                $dslamIDs[] = $dslam['id'];
            }

            $ports = $portModel->getPortList($dslamIDs);

            foreach ($dslams as $key => $dslam)
            {
                foreach ($ports as $port)
                {
                    if ($port['dslam_id'] == $dslam['id'])
                    {
                        if ($onlyEmpty && $port['status'] == Ports::OCCUPED_STATUS)
                        {
                            continue;
                        }

                        if ($port['status'] < 0)
                        {
                            $port['pcross'] = $portModel->getStatusLabel($port['status']);
                        }

                        $dslams[$key]['ports'][] = $port;
                    }
                }
            }

            $this->view->dslamList = $dslams;
            $this->view->dslamTypes = $dslamTypes;
            $this->view->atsID = $atsID;
            $this->view->only_empty = $onlyEmpty;
        }

        $this->view->atsList = ClientHelper::getAtsList();
    }

    public function setPortStatusAction()
    {
		$port_id = $this->_request->getParam('port_id');

		$form = new Form_PortStatus();
		$data['port_id'] = $port_id;
		$form->populate($data);
		$this->view->form = $form;

		if($this->_request->isPost())
		{
			$portID      = $this->_request->getParam('port_id');
			$status_type = $this->_request->getParam('status_type');

			if($portID)
			{
				$portmodel = new Ports();
				$portmodel->changeStatus($portID, $status_type);
			}
		}
    }

    public function configureAction()
    {
        $dslamModel = new DslamList();
        $portsModel = new Ports();

        $dslamID = $this->_request->getParam('id');
        $dslam = $dslamModel->fetchRecordByID($dslamID);

        $ports = $portsModel->getDslamPorts($dslamID);
        $this->view->dslam = $dslam;
        $this->view->ports = $ports;
    }

    public function saveconfigAction()
    {
        if ( $this->_request->isPost() )
        {
            $portsModel = new Ports();
            $ports = $this->_request->getPost('ports');

            foreach ($ports as $portID => $portData)
            {
                $portsModel->saveChanges($portData, $portID);
            }
        }

        $dslamID = $this->_request->getParam('dslam_id');
        // Создаем лог
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::CONFIG_DSLAM, $_SERVER['REQUEST_URI'], $_SESSION['last_userID'],  $_SESSION['last_pointID']);

        $this->_redirect("/tech-dslam/configure/id/{$dslamID}");
    }

    public function modifyAction()
    {
        $dslamModel = new DslamList();
        $portModel = new Ports();
        $dslamID = $this->_request->getParam('id');

        if ($this->_request->isPost())
        {
            $data = $this->_request->getPost();

            $currentPortsCount = $info['ports_count_min'] = $portModel->getDslamPortsCount($dslamID);
            $form = $this->createForm($info);

            if ($form->isValid($data))
            {
                $dslamID = $dslamModel->saveChanges($form->getValues(), $dslamID);
                $maxPortNumber = $portModel->getDslamMaxPortNumber($dslamID);
                $newPortsCount = $data['ports_count'];

                if ($newPortsCount > $currentPortsCount)
                {
                    while ($currentPortsCount < $newPortsCount)
                    {
                        $portData['dslam_id'] = $dslamID;
                        $portData['number'] = ++$maxPortNumber;
                        $portModel->saveChanges($portData);

                        $currentPortsCount++;
                    }
                }
				// Создаем лог
        		AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::EDIT_DSLAM, $_SERVER['REQUEST_URI'], $_SESSION['last_userID'],  $_SESSION['last_pointID']);

                $this->_redirect('/tech-dslam/index');
            }
            else
            {
                $this->view->form = $form;
            }
        }
        else
        {
            $dslamInfo = $dslamModel->fetchRecordByID($dslamID)->toArray();
            $this->view->isNew = false;

            if ( ! $dslamID )
            {
                $dslamInfo['ats_id'] = $this->_request->getParam('ats_id');
                $this->view->isNew = true;
            }

            $form = $this->createForm($dslamInfo);
            $this->view->form = $form;
        }
    }

    /**
     * Creates form for modify page and populate it with values
     *
     * @return Form_Techdslam
     */
    private function createForm($dslamInfo = array())
    {
        $values = $dslamInfo;
        $asrTypeModel = new AsrHelp();
        $asrTypes = $asrTypeModel->getAsrTypeOptions(array(AsrHelp::CLIENT_DSLAM_TYPE, AsrHelp::DSLAM_TYPE));

        $values['client_type_options'] = $asrTypes[AsrHelp::CLIENT_DSLAM_TYPE];
        $values['type_options'] = $asrTypes[AsrHelp::DSLAM_TYPE];

        $form = new Form_Techdslam();
        $form->populate($values);

        return $form;
    }

    public function deleteAction()
    {
        $dslamModel = new DslamList();
        $portModel = new Ports();
        $dslamID = $this->_request->getParam('id');
        $ports = $portModel->getDslamPorts($dslamID);

        foreach ($ports as $port)
        {
            if ($port['status'] == Ports::OCCUPED_STATUS)
            {
                $this->view->error = 'Не возможно удалить DSLAM. На нем есть занятые порты';
                return;
            }
        }

        $dslamModel->markToDelete($dslamID);
        // Создаем лог
		AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::DELETE_DSLAM, $_SERVER['REQUEST_URI'], $_SESSION['last_userID'],  $_SESSION['last_pointID']);

        $this->_redirect('/tech-dslam/index');
    }
}
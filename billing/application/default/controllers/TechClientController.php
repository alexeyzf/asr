<?php
/**
 * Controller for Tech clients
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('ClientModel.php');
require_once('AddPoint.php');
require_once('ClientHelper.php');
require_once('TechHistoryHelper.php');
require_once('AbonDepartmentHistoryHelper.php');
require_once('forms/Techclient.php');
require_once('AsrHelp.php');
require_once('PointStatusesHelper.php');
require_once('EditPointModel.php');

class TechClientController extends BaseController
{
    /**
     * Action for search client
     */
    public function searchAction()
    {
        $param = $this->_request->getParam('param');
        $value = $this->_request->getParam('value');

        if ($param)
        {
        	if ($param == 'login')
        	{
        		$param = 'points.u_login';
        	}
        	elseif ($param == 'name')
        	{
        		$param = 'client_name';
        	}
        	elseif ($param == 'phone')
        	{
        		$param = 'points.pcross';
        	}
        	elseif ($param == 'address')
        	{
        		$param = 'points.connect_address';
        	}
        	elseif ($param == 'ats')
        	{
        		$param = 'ats_list.name';
        	}
        	else
        	{
        		$param = 'client_name';
        	}

	        $page = $this->_request->getParam('page');

	        $model = new ClientModel();

	        $paginatorAdapter = $model->searchToPaginatorDbAdapter($param, $value);
	        $paginator = new Zend_Paginator($paginatorAdapter);
	        $paginator->setItemCountPerPage(25);
	        $paginator->setCurrentPageNumber($page);
	        $paginator->setView($this->view);
	        Zend_View_Helper_PaginationControl::setDefaultViewPartial('/my_pagination.phtml');

	        $this->view->param = $param;
	        $this->view->value = $value;
	        $this->view->clients = $paginator;

        	if ($page == 0)
	        {
	        	$page = 1;
	        }

	        $this->view->start = ($page - 1) * 25 + 1;
        }
    }

    /**
     * Action for modfy client connection info
     */
    public function modifyAction()
    {
        $model 		  = new ClientModel();
        $tarifListModel   = new TarifListModel();
        $techHistoryModel = new TechHistory();

        $translator = Zend_Registry::get('Zend_Translate');

    	$this->view->errors = MessageHelper::getAllErrors();
        $this->view->infos  = MessageHelper::getAllInfos();

        if ($this->_request->isPost())
        {
            $clientID    = $this->_request->getPost('client_id');
            $pointID     = $this->_request->getPost('point_id');
            $isPerekross = $this->_request->getPost('isPerekross');


            $data['point_id']    = $pointID;
            $data['pcross']      = $this->_request->getPost('pcross');
            $data['client_type_id']      = $this->_request->getPost('client_type_id');
            $data['pcross_type'] = $this->_request->getPost('pcross_type');
            $data['ats_id']      = $this->_request->getPost('ats_id');
            $data['dslam_id']    = $this->_request->getPost('dslam_id');
            $data['port_id']     = $this->_request->getPost('port_id');
            $data['country_id']  = $this->_request->getPost('country_id');

            if($isPerekross == "0")
            {
                unset($data['client_type_id']);

                $searchPcross = $techHistoryModel->verifyPcrossNumber($data['port_id']);

                if($searchPcross != "")
                {
                        $this->view->pcross = $searchPcross;

                        $this->render('pcross-error-kind'); return;
                }

                $form = $this->createForm($data);

                if ($form->isValid($data))
                {
                    $pointModel = new AddPoint();

                    $oldPoint = $pointModel->fetchRecordByID($pointID);
                    $oldPortID = $oldPoint->port_id;

                    $data['statuscross'] = PointHelper::STATUS_CROSS_TO_CROSS;
                    $data['engineer_id'] = Zend_Auth::getInstance()->getStorage()->read()->id;
                    $pointModel->saveChanges($data, $pointID);

                    $portsModel  = new Ports();
                    $clientModel = new ClientModel();

                    if ($oldPortID && $oldPortID != $data['port_id'])
                    {
                        $oldPortData['last_using_date'] = date('Ymd');
                        $oldPortData['status'] = "0";
                        $portsModel->saveChanges($oldPortData, $oldPortID);
                        $pointInfo = $clientModel->getInfo($pointID);
                        TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::DETACH_PORT_ACTION);
                        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::POINT_DETACH_PORT, $_SERVER['REQUEST_URI'], $pointInfo['client_id']);
                    }

                    $newPortData['status'] = 1;
                    $portsModel->saveChanges($newPortData, $data['port_id']);
                    $pointInfo = $clientModel->getInfo($pointID);
                    TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::ATTACH_PORT_ACTION);
                    AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::POINT_ATTACH_PORT, $_SERVER['REQUEST_URI'], $pointInfo['client_id']);

                    $this->_redirect("/tech-client/modify/id/{$pointID}");
                }
                else
                {
                    $info['tarif_name'] = $tarifListModel->getTarifNameFromPoint($data['point_id']);

                    $info['pcross']   = $data['pcross'];
                    $info['ats_id']   = $data['ats_id'];
                    $info['dslam_id'] = $data['dslam_id'];
                    $info['port_id']  = $data['port_id'];
                    $form->populate($info);

                    $this->view->form = $form;
                    $this->view->pointID = $pointID;
                    $this->view->clientID = $clientID;
                    $this->view->countryID = $info['country_id'];
                    $this->view->tarif_name = $info['tarif_name'];
                }
            }
            elseif($isPerekross == "1")
            {
                // Смена номера в пределах 1ой АТС
                $portModel     = new Ports();
                $clientModel   = new ClientModel();
                $portTaskModel = new Porttasks();

                $data['new_login'] = $this->_helper->GenerateData->genCross($data['country_id'], $data['pcross'], $data['client_type_id']);

                // Сохраняем старое значение кроса
                $oldPointData = $clientModel->getInfo($pointID);
                $portTaskModel->addTask($oldPointData, Porttasks::TASK_TYPE_OFF);

                $data['old_atsID']   = $oldPointData['ats_id'];
                $data['old_dslamID'] = $oldPointData['dslam_id'];
                $data['old_pcross']  = $oldPointData['pcross'];

                $result = $portModel->startPerekros($data);

                $errors = array(1 => 'portIsAlreadryOccupied',
                                2 => 'numberAlreadyOccupied',
                                3 => 'pointNotFound',
                                4 => 'dataIsTheSame',
                                5 => 'portIsAlreadryOccupied',
                                6 => 'errorFlagPort');

                if (in_array((int)$result, array_keys($errors)))
                {
                    MessageHelper::addError('port-errors', $translator->_($errors[(int)$result]));
					$this->_redirect('/tech-client/modify/id/'.$pointID.'');
                }

                $t = $this->perekrosLetter($data, $oldPointData);

            }
            elseif($isPerekross == "2")
            {
            	$data['new_login'] = $this->_helper->GenerateData->genCross($data['country_id'], $data['pcross'], $data['client_type_id']);

  				$model = new ClientModel();
  				$result = $model->changeLoginByTech($data);

				if($result == "1")
				{
					TechHistoryHelper::addLogFromPointID($pointID, TechHistoryHelper::LOGIN_CHANGED);
					MessageHelper::addError('port-infos', $translator->_('loginChangedSuccessful'));
					$this->_redirect('/tech-client/modify/id/'.$pointID.'');
				}
				else
				{
					MessageHelper::addError('port-infos', $translator->_('loginChangedError'));
					$this->_redirect('/tech-client/modify/id/'.$pointID.'');
				}
            }
            elseif($isPerekross == "3")
            {
            	/**
            	 *  TODO
            	 */
            	//$data['new_login'] = $this->_helper->GenerateData->genCross($data['country_id'], $data['pcross'], $data['client_type_id']);

  				$model = new ClientModel();
  				$result = $model->changeLoginByTech($data , 1);

				if($result == "1")
				{
					TechHistoryHelper::addLogFromPointID($pointID, TechHistoryHelper::LOGIN_CHANGED);
					MessageHelper::addError('port-infos', $translator->_('loginChangedSuccessful'));
					$this->_redirect('/tech-client/modify/id/'.$pointID.'');
				}
				else
				{
					MessageHelper::addError('port-infos', $translator->_('loginChangedError'));
					$this->_redirect('/tech-client/modify/id/'.$pointID.'');
				}
            }
        }
        else
        {
            $id = $this->_request->getParam('id');

            $info = $model->getInfo($id);

            $info['tarif_name'] = $tarifListModel->getTarifNameFromPoint($info['point_id'], $info['tablename']);
            $this->view->form      = $this->createForm($info);
            $this->view->pointID   = $info['point_id'];
            $this->view->clientID  = $info['client_id'];
            $this->view->countryID = $info['country_id'];
            $this->view->service   = $info['tablename'];
            $this->view->tarif_name = $info['tarif_name'];
        }
    }

    /**
     * Creates Zend_Form for modify action
     *
     * @param array $clientInfo - Client data to populate form with
     * @return Zend_Form
     */
    private function createForm($clientInfo = array())
    {
        /**
         * Getting data for select options
         */
        $clientModel  = new ClientModel();

        if($clientInfo['client_type_id'] or $clientInfo['point_id'])
        {
        	$last_session = $clientModel->getLastSession($clientInfo['client_type_id'], $clientInfo['point_id']);
        }

        $atsOptions   = ClientHelper::getAtsList();
        $dslamOptions = ClientHelper::getDslamList($clientInfo['ats_id']);
        $portsOptions = ClientHelper::getPortList($clientInfo['dslam_id']);

        $values = $clientInfo;
        $values['ats_options']       = $atsOptions;
        $values['dslam_options']     = $dslamOptions;
        $values['port_options']      = $portsOptions;
        $values['service_name']      = $clientInfo['tablename'];
        $values['ballance_status']   = number_format($clientInfo['ballance'], 2, ',', ' ');
        $values['last_session_date'] = date('d.m.Y H:m:s', strtotime($last_session));

        $asrTypesModel = new AsrHelp();
        $asrTypes      = $asrTypesModel->getAsrTypeOptions();

        $values['letter_kind_options']      = $asrTypes[AsrHelp::LETTER_KIND_TYPE];
        $values['letter_type_options']      = $asrTypes[AsrHelp::LETTER_TYPE_TYPE];
        $values['letter_sent_way_options']  = $asrTypes[AsrHelp::LETTER_SENT_WAY_TYPE];
        $values['letter_sent_date_options'] = $asrTypes[AsrHelp::LETTER_SENT_DATE_TYPE];

        $portsModel = new Ports();
        $port       = $portsModel->fetchRecordByID($clientInfo['port_id'])->toArray();

        $port['port_status'] = $portsModel->getStatusLabel($port['status']);

        if ($values['pcross_type'] == PointHelper::PHONE_TYPE_ORDINAL)
        {
            $port['pair_number'] = $port['line_number1'] . '/' . $port['line_number2'];
        }
        else
        {
            $port['pair_number'] = $port['line_number2'];
        }

        $values += $port;

        $pointServiseModel = new EditPointModel();
        $serviceInfo       = $pointServiseModel->getFirstCrossService($values['point_id']);

        $serviceInfo['service_status'] =  $serviceInfo['penable'] ? 'Включена' : 'Выключена';
        $values += $serviceInfo;

        $form = new Form_Techclient();
        $form->populate($values);

        return $form;
    }

    public function formletterAction()
    {
        if ($this->_request->isPost())
        {
            $pointID        = $this->_request->getParam('point_id');
            $atsID          = $this->_request->getParam('ats_id');
            $letterKind     = $this->_request->getParam('letter_kind');
            $letterType     = $this->_request->getParam('letter_type');
            $letterSentWay  = $this->_request->getParam('letter_sent_way');
            $letterSentDate = $this->_request->getParam('letter_sent_date');

            $letterModel = new LettersToAts();
            $letterID    = $letterModel->createLetter($pointID, $atsID, $letterType, $letterKind, $letterSentWay, $letterSentDate);

            $clientModel = new ClientModel();
            $pointInfo   = $clientModel->getInfo($pointID);

            $pointInfo['letter_to_ats_id'] = $letterID;
            TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::LETTER_FORMED);

            $rekvizits = Zend_Registry::get('rekvizits');
            $letter = $letterModel->getLetterByID($letterID);
            $phoneHubModel = new PhoneHubList();
            $phoneHubData = $phoneHubModel->fetchRecordByID($pointInfo['phone_hub_id'])->toArray();

            if ($letterKind == LettersToAts::LETTER_KIND_CROSS)
            {
                require_once('CrossLetterRtf.php');
                $rtfHelper = new CrossLetterRtfHelper($rekvizits->company_name, $rekvizits->gen_name_main, $pointInfo['client_name'], $pointInfo['client_type_id']);
                $rtfHelper->generate($letter['number'], $phoneHubData, $pointInfo, $letterType == LettersToAts::LETTER_TYPE_INSTEAD_LOST);
            }
            elseif ($letterKind == LettersToAts::LETTER_KIND_UNCROSS)
            {
                require_once('UncrossLetterRtf.php');
                $rtfHelper = new UncrossLetterRtfHelper($rekvizits->company_name, $rekvizits->gen_name_main, $pointInfo['client_name'], $pointInfo['client_type_id']);
                $rtfHelper->generate($letter['number'], $phoneHubData, $pointInfo, $letterType == LettersToAts::LETTER_TYPE_INSTEAD_LOST);
            }
        }
    }

    /**
     * Action for uncross point
     */
    public function uncrossAction()
    {
        $pointID = $this->_request->getParam('point_id');
    }


    public function perekrosLetter($data, $oldPointInfo)
    {

            $pointID     = $data['point_id'];
            $atsID       = $data['ats_id'];
            $atsID_old   = $data['old_atsID'];

            $letterSentDate = 3;
            $letterSentWay  = 1;
            $letterKind     = 3;
            $letterType     = 1;

            // Новое письмо
            $letterModel = new LettersToAts();
            $letterID_old    = $letterModel->createLetter($pointID, $atsID_old, $letterType, $letterKind, $letterSentWay, $letterSentDate);
            $letterID        = $letterModel->createLetter($pointID, $atsID, $letterType, $letterKind, $letterSentWay, $letterSentDate);

            $clientModel = new ClientModel();
            $pointInfo   = $clientModel->getInfo($pointID);

            // Логируем фосримирование письма (Старого)
            $pointInfo['letter_to_ats_id'] = $letterID_old;
            //$reason_str =  "перекрос c ". $data['old_pcross']. " на ". $data['pcross']. " (На раскрос)";
            //TechHistoryHelper::addLogFromPoint($oldPointInfo, TechHistoryHelper::LETTER_FORMED, $reason_str);

            $rekvizits = Zend_Registry::get('rekvizits');

            // Формируем письмо на крос (Старое)
            $letter_old    = $letterModel->getLetterByID($letterID_old);
            $letter        = $letterModel->getLetterByID($letterID);

            $phoneHubModel = new PhoneHubList();
            $phoneHubData_old  = $phoneHubModel->fetchRecordByID($oldPointInfo['phone_hub_id'])->toArray();
            $phoneHubData      = $phoneHubModel->fetchRecordByID($pointInfo['phone_hub_id'])->toArray();

            if($data['pcross'] == $oldPointInfo['pcross'] and $data['ats_id'] == $data['old_atsID'])
            {
                $reason_str =  "перекрос c ". $data['old_pcross']. " на ". $data['pcross']. " (На раскрос)";
                TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::LETTER_FORMED, $reason_str);

                require_once('PerekrossLetterRtf.php');
                $rtfHelperPere = new PerekrossLetterRtf($rekvizits->company_name, $rekvizits->gen_name_main, $oldPointInfo['client_name'], $oldPointInfo['client_type_id']);
                $rtfHelperPere->generate($letter_old['number'], $phoneHubData_old, $pointInfo, $letterType == LettersToAts::LETTER_TYPE_INSTEAD_LOST, $oldPointInfo);
            }

            if($data['pcross'] != $oldPointInfo['pcross'] and $data['ats_id'] == $data['old_atsID'])
            {
                $reason_str =  "перекрос c ". $data['old_pcross']. " на ". $data['pcross']. " (На раскрос)";
                TechHistoryHelper::addLogFromPoint($oldPointInfo, TechHistoryHelper::LETTER_FORMED, $reason_str);

                require_once('TwoLettresRtf.php');
                $rtfHelperPere = new TwoLettresRtf($rekvizits->company_name, $rekvizits->gen_name_main, $oldPointInfo['client_name'], $oldPointInfo['client_type_id']);
                $rtfHelperPere->generate($letter_old['number'], $letter['number'], $phoneHubData_old, $pointInfo, $letterType == LettersToAts::LETTER_TYPE_INSTEAD_LOST, $oldPointInfo);
            }

            if($data['ats_id'] != $data['old_atsID'])
            {
                $reason_str =  "перекрос c ". $data['old_pcross']. " на ". $data['pcross']. " (На раскрос)";
                TechHistoryHelper::addLogFromPoint($oldPointInfo, TechHistoryHelper::LETTER_FORMED, $reason_str);

                require_once('PereezdLetterRtf.php');
                $rtfHelper = new PereezdLetterRtf($rekvizits->company_name, $rekvizits->gen_name_main, $oldPointInfo['client_name'], $oldPointInfo['client_type_id']);
                $rtfHelper->generate($letter_old['number'], $letter['number'], $phoneHubData_old, $phoneHubData,  $pointInfo, $letterType == LettersToAts::LETTER_TYPE_INSTEAD_LOST, $oldPointInfo);
            }
    }
}

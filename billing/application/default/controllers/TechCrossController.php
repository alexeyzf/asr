<?php
/**
 * Controller for tech cross page
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('PointHelper.php');
require_once('PointStatuses.php');
require_once('Porttasks.php');
require_once('AbonDepartmentHistoryHelper.php');

class TechCrossController extends BaseController
{
    public function indexAction()
    {
        $type = $this->_request->getParam('type');

        if ($type)
        {
            $pointStatusesModel = new PointStatuses();

            if ($type == 'cross')
            {
                $statuses = $pointStatusesModel->getCrossOptions();
                $doneStatus = $pointStatusesModel->getCrossDoneStatus();
                $letterKinds = array(LettersToAts::LETTER_KIND_CROSS, LettersToAts::LETTER_KIND_RECROSS);
            }
            elseif ($type == 'uncross')
            {
                $statuses = $pointStatusesModel->getUncrossOptions();
                $doneStatus = $pointStatusesModel->getUncrossDoneStatus();
                $letterKinds = array(LettersToAts::LETTER_KIND_UNCROSS);
            }

            $orderBy = $this->_request->getParam('order_by');

			if($orderBy == "")
			{
				$orderBy = "ats";
			}

            $points = PointHelper::getCrossList(array_keys($statuses), $orderBy);

            $lettersToAtsModel = new LettersToAts();
            $letters = $lettersToAtsModel->getLastLettersByKind($letterKinds);

            foreach ($points as $key => $point)
            {
            	$points[$key]['letter_number'] = $letters[$point['point_id']]['letter_number'];
            	$points[$key]['letter_sent_date'] = $letters[$point['point_id']]['letter_sent_date'];
            	$points[$key]['letter_receive_date'] = $letters[$point['point_id']]['letter_receive_date'];
            }

            $this->view->pointList = $points;

            $statuses[$doneStatus->code] = $doneStatus->label;

            $this->view->statuses = $statuses;
            $this->view->type = $type;
        }
    }

    public function saveAction()
    {
        if ($this->_request->isPost())
        {
            $pointModel    = new AddPoint();
            $clientModel   = new ClientModel();
            $portTaskModel = new Porttasks();
            $portsModel    = new Ports();
            $atsModel      = new AtsList();

            $engineerCallsModel = new EngineerCalls();
            $points = $this->_request->getPost('points');

            foreach ($points as $pointID => $pointData)
            {
                if ( $pointData['is_changed'] )
                {
                    unset($pointData['is_changed']);
					$pointData['last_modified_date'] = date('Y-m-d H:m:s');
                    $pointModel->saveChanges($pointData, $pointID);


                    $pointInfo = $clientModel->getInfo($pointID);

                    TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::STATUS_CHANGED);
                    AbonDepartmentHistoryHelper::addAbonLog($pointInfo['statuscross_label'], $_SERVER['REQUEST_URI'], $pointInfo['client_id']);

                    if ($pointData['statuscross'] == PointHelper::STATUS_CROSS_CROSS_DONE)
                    {
                    	$crossService = $clientModel->getCrossServiceForPoint($pointID);

                    	if ($crossService['vlan'])
                    	{
                    		$pointInfo['vlan'] = $crossService['vlan'];
                    	}


						// Логируем статистику
						PointHelper::loggingHubsPorts($pointInfo['ats_id'], 25);

                        // Добавляем задание на поднятие порта
                        // Если клиент Юр. лицо до ничего не делаем так как указания на порт для корп. клиентов
                        // Должны будут кидатся только когда клиенту будет присвоен IP и все остальные рек.
                        if($pointInfo['client_type_id'] == "0")
                        {
                            // Добавляем в список вызовов
                            $engineerCallsModel->addCall($pointID);

                            // Занимаем порт
                            $portsModel->changeStatus($pointInfo['port_id'], Ports::OCCUPED_STATUS);
                        }
                        else
                        {
                            // тут для стримов. Если атс на 1мб
                            $is_expanded = $atsModel->verifyAts($pointInfo['ats_id']);
                            if($is_expanded == "t")
                            {
                                 $speed_profile = "1024/512";
                            }
                            else
                            {
                                 $speed_profile = $pointInfo['portspeed'];
                            }

                            // И еще раз на всякий проверим
                            $pointInfo['portspeed'] = $speed_profile;
                            $portTaskModel->addTask($pointInfo, Porttasks::TASK_TYPE_ON);
                        }


                        /*if ($pointData['client_type_id'] == ClientModel::CLIENT_TYPE_PHYSICAL)
                        {
                            //Снимаем регистрационную плату
                            $serviceModel = new ListServiceModel();
                            $financeModel = new FinanceModel();
                            $regPay = $serviceModel->getCrossRegPay($pointID);

                            if ( is_array($regPay) )
                            {
                                $financeModel->addTransaction($pointInfo['client_id'], $regPay[0], FinanceHelper::Register, $regPay[1], 0);
                            }
                        }*/ //Пока такой функционал не нужен
                    }
                    elseif ($pointData['statuscross'] == PointHelper::STATUS_CROSS_UNCROSS_DONE)
                    {
                    	// Логируем статистику
						PointHelper::loggingHubsPorts($pointInfo['ats_id'], -25);

						// пишем хистори
						TechHistoryHelper::addLogFromPointID($pointInfo['point_id'], TechHistoryHelper::CONTRACT_TERMINATED);

                        $portTaskModel->addTask($pointInfo, Porttasks::TASK_TYPE_OFF);
                        $portsModel->changeStatus($pointInfo['port_id'], Ports::EMPTY_STATUS);
                        $editPointModel = new EditPointModel();

						$editPointModel->deleteDataPoint($pointID);
						$editPointModel->closeIpByPoint($pointID);
                    }
                }
            }

            $type = $this->_request->getPost('type');
            $this->_redirect('/tech-cross/index/type/' . $type);
        }
    }
}
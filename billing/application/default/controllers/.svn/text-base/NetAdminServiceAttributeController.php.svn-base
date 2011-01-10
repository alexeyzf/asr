<?php
/**
 * Controller for tech service attributes pages
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('AddPoint.php');
require_once('ServiceAttributes.php');
require_once('Porttasks.php');
require_once ('forms/Task.php');
require_once ('helpers/MessageHelper.php');

class NetAdminServiceAttributeController extends BaseController
{
    public function indexAction()
    {
        $serviceAttributesModel = new ServiceAttributes();
        $orderBy = $this->_request->getParam('orderBy');

        $this->view->list = $serviceAttributesModel->getNotSetList($orderBy, 0);
    }

    public function modifyAction()
    {
    	$translator = Zend_Registry::get('Zend_Translate');
		$this->view->errors = MessageHelper::getAllErrors();
        $this->view->infos  = MessageHelper::getAllInfos();

        $serviceAttributesModel = new ServiceAttributes();
        $pointIpAddressesModel  = new PointIpAddresses();
        $pointInfoModel 		= new ClientModel();
        $portTaskModel 			= new Porttasks();

        $pointID 	 = $this->_request->getParam('point_id');
        $serviceType = $this->_request->getParam('service_type_id');

        $allData 	   = $serviceAttributesModel->getInfo($pointID, $serviceType);
        $dbIpAddresses = $pointIpAddressesModel->getPointIpAddresses($pointID);

        $this->view->ipAddresses = $dbIpAddresses;
        if( $this->_request->isPost() )
        {
            $attributesValues = $this->_request->getParam('attributes');
            $ipAddresses      = $this->_request->getParam('ip_addresses');

			$flagValid = $this->vlaidateIP($dbIpAddresses, $ipAddresses);

			if($flagValid)
			{
				MessageHelper::addError('ip-errors', $translator->_('ipexist'));
				$this->_redirect("/net-admin-service-attribute/modify/point_id/{$pointID}/service_type_id/{$data['service_type_id']}");
			}

            foreach ($allData['attributes'] as $attr)
            {
                $value = $attributesValues[$attr['type']];
                $data[$attr['column_name']] = $value;
            }

            $data['service_type_id'] = $allData['data']['servicetype_id'];
            $data['admin_id'] = Zend_Auth::getInstance()->getStorage()->read()->id;

            $serviceAttributesModel->updateAttributes($pointID, $data);

            foreach ($dbIpAddresses as $ip)
            {
            	if (array_search($ip, $ipAddresses) === false)
            	{
            		$pointIpAddressesModel->setEndDate($pointID, $ip);
            	}
            }

            foreach ($ipAddresses as $ip)
            {
            	if (array_search($ip, $dbIpAddresses) === false)
            	{
            		$pointIpAddressesModel->addIP($pointID, $ip);
            	}
            }

            // Записываем имя админа кто выставил данные
            $serviceAttributesModel->updatePointService(
                $allData['data']['tarif_id'],
                $pointID,
                Zend_Auth::getInstance()->getStorage()->read()->id
            );

            $this->_redirect("/net-admin-service-attribute/modify/point_id/{$pointID}/service_type_id/{$data['service_type_id']}");
        }
        else
        {
            $this->view->data       = $allData['data'];
            $this->view->attributes = $allData['attributes'];
            $this->view->ipAddresses = $dbIpAddresses;
        }
    }

	public function addtaskAction()
	{
		/**
		 *  Данные для таблицы porttasks и не только!
		 */

		$point_id  = $this->_request->getParam('pid');
		$tablename = $this->_request->getParam('tablename');

		// model для портов
		$model = new Porttasks();
		$data = $model->getForPortTasksData($point_id, $tablename);

        $form = new Form_Task();
        $form->populate($data);

        $this->view->arrIP = $data['ip_address'];
        $this->view->form_data = $form;
	}

	public function savetaskAction()
	{
		$data = $this->_request->getPost();

        $auth = Zend_Auth::getInstance();
        $user = $auth->getStorage()->read();

		$model 		  = new Porttasks();
		$modelHosting = new HostingTasks();

		if(!$data['domain_addres'])
		{
			$arr = explode(',', $data['ip_address']);
			$data['ip_address'] = implode(' ', $arr);
			$model->insertTask($data);

			$pointModel = new EditPointModel();
			$crossService = $pointModel->getFirstCrossService($data['point_id']);

			if ( $data['ctype'] == 0 && ! $crossService['is_forced'] )
			{
				$data['task_type']	= 2;
				$data['startdate'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
				$model->insertTask($data);
			}

			//Логируем
	        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::FRAMED_PORT_TASK, $_SERVER['REQUEST_URI'], $data['client_id'], $data['point_id']);
		}
		else
		{
			$data['managerid'] = $user->id;

			$modelHosting->insertHostingTask($data);
		}
		$this->_redirect($_SESSION['back_url']);
	}

	public function showPortHistoryAction()
	{
		$porttasksModel = new Porttasks();

		$start = date("Y-01-01 00:00:00");
		$end   = date("Y-12-31 23:59:59");

		$clientID = $this->_request->getParam("client_id");
		if($clientID)
		{
			$this->view->data = $porttasksModel->taskList($start, $end, $clientID);
			$this->_helper->layout->setLayout('show-port-history');
		}
	}

	public function vlaidateIP($oldIpArray, $newIpArray)
	{
		$serviceAttributesModel = new ServiceAttributes();

		$clear_arr = array();

		foreach($newIpArray as $item)
		{
			if($item != "")
			{
				array_push($clear_arr, $item);
			}
		}

		if(count($clear_arr) <= count($oldIpArray))
		{
			return false;
		}
		else
		{
			for($i = 0; $i < count($clear_arr); $i++)
			{
				foreach($oldIpArray as $row)
				{
					if(trim($clear_arr[$i]) == $row)
					{
						unset($clear_arr[$i]);
					}
				}
			}

			foreach($clear_arr as $value)
			{
				if($value == "VPN")
				{
					continue;
				}
				$flag = $serviceAttributesModel->duplicateIPaddress($value);
				if($flag)
				{
					return true;
				}
			}
			return false;
		}
	}



}
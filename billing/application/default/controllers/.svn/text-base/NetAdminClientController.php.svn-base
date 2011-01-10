<?php
/**
 * Controller for net admin clients pages
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('ServiceAttributes.php');

class NetAdminClientController extends BaseController
{
    public function searchAction()
    {
    	$portModel = new SwitchPortsModel();

    	$this->view->vipports = $portModel->getControlCenterMessages();

        $param = $this->_request->getParam('by');
        $value = $this->_request->getParam('value');

        if ($param)
        {
            $model = new ServiceAttributes();
            $pointIpAddressModel = new PointIpAddresses();
            $this->view->param = $param;
            $this->view->value = $value;

            if ($param == 'ip_address')
            {
            	$param = 'point_id::character varying';
            	$pointID = $pointIpAddressModel->getPointByIpAddress($value);

            	$value = $pointID;
            }

            if(!$value)
            {
                return;
            }
            else
            {
                $clients = $model->search($param, $value);
            }
            $dynamicUnlimModel = new DynamicUnlimModel();

			for($i=0; $i < count($clients); $i++)
			{
				$tarifDynamicSpeed = $dynamicUnlimModel->getSpeedAndPrice($clients[$i]['service_id']);
				if($tarifDynamicSpeed)
				{
					$clients[$i]['tarif_speed'] = $tarifDynamicSpeed['speed'];
				}
			}

            $this->view->clients = $clients;
        }
    }
}
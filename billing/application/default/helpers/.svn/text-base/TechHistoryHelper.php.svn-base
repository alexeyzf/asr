<?php
/**
 * Helper for log client history
 *
 * @author marat
 */

require_once('TechHistory.php');
require_once('ClientModel.php');
require_once('ClientHelper.php');

class TechHistoryHelper
{
    const ATTACH_PORT_ACTION 	= 'Прикрепление порта';
    const DETACH_PORT_ACTION 	= 'Открепление порта';
    const APPLICATION_CREATED 	= 'Заявка создана';
    const POINT_BLOCKED 		= 'Точка блокируется';
    const APPLICATION_CHANGED 	= 'Заявка отредактирована';
    const TARIF_CHANGE_ACTION 	= 'Смена тарифа';
    const STATUS_CHANGED 		= 'Изменение статуса';
    const LETTER_FORMED 		= 'Письмо сформировано';
    const DEMAND_ROllBACK 		= 'Откат заявки (ОП)';
    const PHONE_CHANGE 			= 'Смена номера';
    const CONTRACT_TERMINATED 	= 'Договор расторгнут';
    const LOGIN_CHANGED 		= 'Смена логина';

    public static function addLog($data, $action)
    {
        $auth = Zend_Auth::getInstance();
        $user = $auth->getStorage()->read();
        $data['user_id'] = $user->id;
        $data['date'] = new Zend_Db_Expr('now()');
        $data['action'] = $action;

        $clientHistoryModel = new TechHistory();
        $clientHistoryModel->insert($data);
    }

    public static function addLogFromPoint($pointInfo, $action, $reason = null)
    {
        $logData['client_name'] = $pointInfo['client_name'];
        $logData['client_login'] = $pointInfo['u_login'];
        $logData['client_type'] = $pointInfo['type_name'];
        $logData['phone'] = $pointInfo['pcross'];
        $logData['speed'] = $pointInfo['portspeed'];
        $logData['ats_id'] = $pointInfo['ats_id'];
        $logData['dslam_id'] = $pointInfo['dslam_id'];
        $logData['status'] = $pointInfo['statuscross'];
        $logData['reason'] = $pointInfo['notes'];

        if ( $pointInfo['port_id'] )
        {
            $logData['port_number'] = $pointInfo['port_number'];
            $logData['frame_number'] = $pointInfo['frame_number'];
            $logData['pair_number'] = $pointInfo['line_number1'] . '/' . $pointInfo['line_number2'];
        }

        // Причина
        if($reason)
        {
            $logData['reason'] = $reason;
        }

        self::addLog($logData, $action);
    }

    public static function addLogFromPointID($pointID, $action)
    {
    	$clientModel = new ClientModel();
    	$pointInfo = $clientModel->getInfo($pointID);
    	self::addLogFromPoint($pointInfo, $action);
    }
}
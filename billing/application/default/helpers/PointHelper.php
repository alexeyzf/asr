<?php
/**
 * Helper to work with points
 *
 * @author marat
 */

require_once('AddPoint.php');

class PointHelper
{
    /**
     * Status cross is missed - client has been added, but button cross has
     * not been pushed
     */
    const STATUS_CROSS_EMPTY = 0;

    /**
     * Point is waiting to be crossed - button cross has been pushed during
     * editing client
     */
    const STATUS_CROSS_WAIT_CROSS = 1;

    /**
     * Point has been accepted to be crossed by line department
     */
    const STATUS_CROSS_ACCEPTED_CROSS = 2;

    /**
     * Point has gone to cross - point has been filled out with data and
     * letter for cross has been printed
     */
    const STATUS_CROSS_TO_CROSS = 3;

    /**
     * Letter for cross has been sent to ATS
     */
    const STATUS_CROSS_LETTER_SENT = 4;

    /**
     * Letter for cross has been received in ATS
     */
    const STATUS_CROSS_LETTER_RECEIVED = 5;

    /**
     * Debt on phone line
     */
    const STATUS_CROSS_DEBT = 6;

    /**
     * Invalid owner of phone line
     */
    const STATUS_CROSS_INVALID_OWNER = 7;

    /**
     * Invalid ATS of phone line
     */
    const STATUS_CROSS_INVALID_ATS = 8;

    /**
     * Letter has been lost
     */
    const STATUS_CROSS_LETTER_LOST = 9;

    /**
     * Phone pairs is occuped by smb else
     */
    const STATUS_CROSS_PAIRS_OCCUPED = 10;

    /**
     * Attire to client
     */
    const STATUS_CROSS_ATTIRE = 11;

    /**
     * BLocker on phone line
     */
    const STATUS_CROSS_BLOCKER = 12;

    /**
     * Remote on phone line
     */
    const STATUS_CROSS_REMOTE = 13;

    /**
     * Invalid law address of client
     */
    const STATUS_CROSS_INVALID_LAW_ADDRESS = 14;

    /**
     * Phone line is crossed by other internet service provider
     */
    const STATUS_CROSS_OTHER_ISP = 15;

    /**
     * Debt on phone line has been repayed
     */
    const STATUS_CROSS_DEBT_REPAY = 16;

    /**
     * Invalid owner has been fixed
     */
    const STATUS_CROSS_OWNER_FIXED = 17;

    /**
     * Blocker on phone line has been fixed
     */
    const STATUS_CROSS_BLOCKER_FIXED = 18;

    /**
     * Remote ob phone line has been fixed
     */
    const STATUS_CROSS_REMOTE_FIXED = 19;

    /**
     * Invalid law address has been fixed
     */
    const STATUS_CROSS_LAW_ADDRESS_FIXED = 20;

    /**
     * Other internet service provider has been fixed
     */
    const STATUS_CROSS_OTHER_ISP_FIXED = 21;

    /**
     * Leter has been resent
     */
    const STATUS_CROSS_RESENT = 22;

    /**
     * Client has gone
     */
    const STATUS_CROSS_CLIENT_LOST = 23;

    /**
     * optical concentrator ... (what is it?)
     */
    const STATUS_CROSS_OPTICAL_CONCENTRATOR = 24;

    /**
     * Point has been crossed succesfully
     */
    const STATUS_CROSS_CROSS_DONE = 25;

    /**
     * Point is waiting to be uncrossed - button uncross has been pushed
     * during editing client
     */
    const STATUS_CROSS_WAIT_UNCROSS = -1;

    /**
     * Point has been accepted to be uncrossed by line department
     */
    const STATUS_CROSS_ACCEPTED_UNCROSS = -2;

    /**
     * Point has gone to uncross - letter for uncross has been printed
     */
    const STATUS_CROSS_TO_UNCROSS = -3;

    /**
     * Point has been uncrossed succesfully
     */
    const STATUS_CROSS_UNCROSS_DONE = -25;

    const PHONE_TYPE_ORDINAL = 1;
    const PHONE_TYPE_DIRECT = 2;

    const TYPE_ACTION_UNCROSS = -25;
    const TYPE_ACTION_CROSS   = 25;

    /**
     * Gets list of points
     *
     * @param array $statuses - filter
     * @return array - list of points
     */
    public static function getPoints($statuses = array(), $order = '', $flag = null)
    {
    	if($flag == 1)
    	{
			if (count($statuses) != 0)
	        {
	            $where = '
	            	dslam_id is not null
	            	and dslam_id != 0
	            	and statuscross IN (' . implode(',', $statuses) . ') ';
	        }
    	}
    	elseif($flag == 0)
    	{
    		if (count($statuses) != 0)
	        {
	            $where = ' statuscross IN (' . implode(',', $statuses) . ') ';
	        }
    	}

        $pointModel = new AddPoint();
        return $pointModel->getList($where, $order);
    }

    public static function getCrossList($statuses = array(), $order = '')
    {
        if (count($statuses) != 0)
        {
            $where = 'statuscross IN (' . implode(',', $statuses) . ')';
        }

        $pointModel = new AddPoint();
        return $pointModel->getCrossList($where, $order);
    }

    /**
     * Saves list of points
     *
     * @param array $points - List of points
     */
    public static function savePoints($points)
    {
        $pointModel = new AddPoint();

        foreach ($points as $pointID => $pointData)
        {
            $pointModel->saveChanges($pointData, $pointID);
        }
    }

    /**
     *  Logging a uncross and cross ports
     */
    public static function loggingHubsPorts($atsID, $actionCode)
    {
    	$model = new PointStatuses();
    	if($atsID)
    	{
    		$result = $model->writeHubStatistic($atsID, $actionCode);
    	}
		return $result;
    }
}
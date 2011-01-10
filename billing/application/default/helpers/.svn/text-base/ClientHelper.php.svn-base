<?php
/**
 * Helper for work with clients in technical department
 *
 * @author marat
 */

require_once('AtsBadNumbers.php');
require_once('AtsNumbers.php');
require_once('AtsList.php');
require_once('DslamList.php');
require_once('Ports.php');
require_once('AtsList.php');
require_once('EditPointModel.php');
require_once('ClientModel.php');

class ClientHelper
{
    /**
     * Checks if current number is bad
     *
     * @param string $number - Current number
     * @return string - If number is bad - returns reason, else false
     */
    public static function checkForBadNumber($countryID, $number, $pointID = NULL)
    {
        $number = preg_replace("/[^0-9]/","",$number);

        if (strlen($number) > 7)
        {
            return 'Длина номера превышает 7 символов';
        }

        $pointModel = new EditPointModel();
        if ( ! $pointModel->checkNumber($pointID, $countryID, $number) )
        {
            return 'Данный номер уже занят';
        }

        $atsBadNumbersModel = new AtsBadNumbers();
        $atsBadNumberRow = $atsBadNumbersModel->fetchRow("'{$number}' BETWEEN start_number AND end_number");

        if ($atsBadNumberRow)
        {
            return $atsBadNumberRow->reason;
        }
        else
        {
            return false;
        }
    }

    public static function checkRshetNumber($number)
    {

        $number = preg_replace("/[^0-9]/","",$number);

        if (strlen($number) != 20)
        {
            return '*Длина Р/c превышает или меньше 20 цифр';
        }
        else
        {
            return 'Ошибок нет '. $number. '<br/>';
        }
    }

    public static function getCurrency($flag = false)
    {
		if($flag)
		{
			return "UZS";
		}
		else
		{
			return "USD";
		}
    }

	/**
	 *  Если ничего не вернется, то это означает что клиент-баланс пустой
	 */
    public static function getCurrencyByClientID($clientID)
    {
		$clientModel = new ClientModel();
		return $clientModel->getCurrencyWord($clientID);
    }

    /**
     * Gets ats list
     */
    public static function getAtsList()
    {
        $atsListModel = new AtsList();
        return $atsListModel->getOptions();
    }

    /**
     * Gets ats info by current number. Checks by ats phone range
     *
     * @param string $number - Current number
     * @return Ats info or false if number
     */
    public static function getRecommendedAts($number)
    {
        $number = preg_replace("/[^0-9]/","",$number);
        $atsNumbersModel = new AtsNumbers();
        $atsID = $atsNumbersModel->getAtsIDByNumber($number);

        if ($atsID)
        {
            $atsListModel = new AtsList();
            return $atsListModel->fetchRow('id = ' . $atsID);
        }
        else
        {
            return false;
        }
    }

    /**
     * Gets dslam list by given atsID
     *
     * @param integer $atsID
     * @return array - dslam list
     */
    public static function getDslamList($atsID)
    {
        if ( ! $atsID )
        {
            $atsID = "0";
        }

        $dslamListModel = new DslamList();
        return $dslamListModel->getOptions("is_deleted = false AND ats_id = {$atsID}");
    }

    /**
     * Gets first availbale dislam - which has empty port
     *
     * @param integer $atsID - Ats where to find dslam
     * @param integet $clientType - Type of client
     * @return Dslam info
     */
    public static function getFirstAvailableDslam($atsID, $clientType)
    {
        $dslamListModel = new DslamList();
        return $dslamListModel->getFirstAvailable($atsID, $clientType);
    }

    /**
     * Gets unbroken ports list by given dslam
     *
     * @param integer $dslamID - Dslam ID
     * @return array - list of ports
     */
    public static function getPortList($dslamID)
    {
        if ( ! $dslamID )
        {
            $dslamID = "0";
        }

        $portsModel = new Ports();
        // ADDON BROKEN PORT CHANGE 2010-10-06 19:20
        return $portsModel->getOptions("dslam_id = {$dslamID} AND status not in (" . Ports::BROKEN_STATUS. ",". Ports::PORT_BROKEN_STATUS_UNKNOWN. ")");
    }

    public static function getFirstAvailablePort($dslamID)
    {
        $portsModel = new Ports();
        return $portsModel->getFirstAvailablePort($dslamID);
    }

    public static function selectRschet($client_id)
    {
        $dataModel = new ClientModel(); // Модель откуда мона узнать все о клиенте
        $scheta    = $dataModel->rschetClient($client_id);
        return $scheta;
    }
}
<?
/**
 * Controller for support information pages
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('ClientHelper.php');
require_once('AsrHelp.php');
require_once('AtsList.php');
require_once('PhoneHubList.php');
require_once('AtsNumbers.php');
require_once('AtsBadNumbers.php');
require_once('PointStatuses.php');

class SupportInformationController extends BaseController
{
    public function atsAction()
    {
        $atsListModel = new AtsList();
        $atsNumbersModel = new AtsNumbers();
        $atsBadNumbersModel = new AtsBadNumbers();
        $phoneHubListModel = new PhoneHubList();

        $atsListDb = $atsListModel->fetchAllNotDeleted();
        $phoneHubs = $phoneHubListModel->getNotDeletedOptions();
        $atsNumbers = $atsNumbersModel->fetchAll();
        $atsBadNumbers = $atsBadNumbersModel->fetchAll();

        $sortBy = $this->_request->getParam('sortBy');
        $keys = array();

        foreach ($atsListDb as $key => $ats)
        {
            $atsList[$key]['id'] = $ats->id;
            $atsList[$key]['name'] = $ats->name;
            $atsList[$key]['address'] = $ats->address;
            $atsList[$key]['status'] = $ats->status == 1 ? 'ON' : 'OFF';;
            $atsList[$key]['notes'] = $ats->notes;
            $atsList[$key]['hub_name'] = $phoneHubs[$ats->phone_hub_id];

            $numbersString = '';
            $connector = '';

            foreach ($atsNumbers as $atsNumber)
            {
                if ($atsNumber->ats_id == $ats->id)
                {
                    $numbersString .= "{$connector}{$atsNumber->start_number} - {$atsNumber->end_number}";
                    $connector = '<br/>';
                }
            }

            $atsList[$key]['numbers'] = $numbersString;

            $badNumbersString = '';
            $connector = '';

            foreach ($atsBadNumbers as $atsBadNumber)
            {
                if ($atsBadNumber->ats_id == $ats->id)
                {
                    $badNumbersString .= "{$connector}{$atsBadNumber->start_number} - {$atsBadNumber->end_number} ({$atsBadNumber->reason})";
                    $connector = '<br/>';
                }
            }

            $atsList[$key]['bad_numbers'] = $badNumbersString;

            if ($sortBy)
            {
                $keys[$key] = $atsList[$key][$sortBy];
            }
        }

        if ($sortBy)
        {
            $result = array_multisort($keys, SORT_ASC, $atsList);
        }

        $this->view->atsList = $atsList;
    }

    public function phoneCheckAction()
    {
        if ( $this->_request->isPost() )
        {
            $number = $this->_request->getParam('number');
            $city = $this->_request->getParam('city');
            $clientType = $this->_request->getParam('client_type');

            $this->view->number = $number;
            $this->view->city = $number;
            $this->view->clientType = $clientType;

            $reason = ClientHelper::checkForBadNumber($city, $number);

            if ($reason)
            {
                $this->view->checkResult = $reason;
            }
            else
            {
                $ats = ClientHelper::getRecommendedAts($number);

                if ( ! $ats )
                {
                    $this->view->checkResult = 'Не найдено ни одной АТС для данного номера телефона';
                }
                else
                {
                    $recommendedDslamID = ClientHelper::getFirstAvailableDslam($ats->id, $clientType);

                    if ( ! $recommendedDslamID )
                    {
                        $this->view->checkResult = 'Нет свободных портов на АТС';
                    }
                    else
                    {
                        $this->view->checkResult = 'Подключение возможно';
                    }
                }
            }
        }

        $asrHelpModel = new AsrHelp();
        $asrTypes = $asrHelpModel->getAsrTypeOptions(array(AsrHelp::CITY_TYPE, AsrHelp::CLIENT_TYPE));
        $this->view->cities = $asrTypes[AsrHelp::CITY_TYPE];
        $this->view->clientTypes = $asrTypes[AsrHelp::CLIENT_TYPE];
    }
    
    public function crossProblemAction()
    {
        $problemListModel = new PointStatuses();
        $clientList = $problemListModel->getProblemClients();
        $this->view->problemlist = $clientList;
    }
}
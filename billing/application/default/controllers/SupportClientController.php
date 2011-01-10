<?
/**
 * Controller for support client pages
 *
 * @author marat
 */

require_once('BaseController.php');

class SupportClientController extends BaseController
{
    public function searchAction()
    {

        if ( $this->_request->isPost() )
        {
            $param = $this->_request->getParam('param');
            $value = $this->_request->getParam('value');
			$value = trim($value);
			$value = strtolower($value);

            if ($value)
            {
	            $model = new ClientModel();
	            $clients = $model->search($param, $value, 10);
	            $this->view->param = $param;
	            $this->view->value = $value;
	            $this->view->clients = $clients;
            }
        }
    }

    public function viewAction()
    {
        $pointID = $this->_request->getParam('id');

        $model 			= new ClientModel();
        $clienInfoModel = new ClientModel();

        $info 			  = $model->getInfo($pointID);

        $this->view->info = $info;

        $serviceTables = array (
            'adsl',
            'collacation',
            'ngn',
            'hosting',
            'tasix',
            'wifi',
            'vpn',
            'isdn',
            'tradtel',
            'dialup'
        );

        $resultServices = array();
        foreach($serviceTables as $service)
        {
        	$services = $clienInfoModel->showServices($info['client_id'], $service, 2);

        	if($services)
        	{
        		$resultServices = array_merge($resultServices, $services);
        	}
        }


		$this->view->sessionTime = $this->showDialupSessionTimeSum($info['u_login'], $resultServices[0]['startdate'], $resultServices[0]['enddate'], $info['client_type_id'] );
        $this->view->services 	 = $resultServices;
        $this->view->logs 		 = $clienInfoModel->getConnectLogs($info['u_login']);
    }

    private function showDialupSessionTimeSum($username, $startdate, $enddate, $clientTypeID)
    {
    	if($clientTypeID == 2 or $clientTypeID == 3)
    	{
    		$dialupModel = new DialupModel();
			$time 		 = $dialupModel->getSessionSumForDialup($username, $startdate, $enddate);
			return number_format($time, 2, '.', ' ');
    	}

    }

    public function financeAction()
    {
        $financeModel = new FinanceModel();
        $clienInfoModel = new ClientModel();

        $clientID = $this->_request->getParam('id');
        $transactions = $financeModel->getTransactionList($clientID);
        $this->view->transactions = $transactions;
        $this->view->client = $clienInfoModel->getClientName($clientID);
    }

    public function logsAction()
    {
        $logModel = new AbonHistoryModel();
        $clienInfoModel = new ClientModel();

        $clientID = $this->_request->getParam('id');
        $this->view->logs = $logModel->getClientLogs($clientID);
        $this->view->client = $clienInfoModel->getClientName($clientID);
    }

    public function sessionsAction()
    {
        $login = $this->_request->getParam('user');
        $radacctModel = new Radacct();

        $sessions = $radacctModel->getTopSessions($login);

        foreach ($sessions as $key => $session)
        {
            $sessionHour = floor($session['acctsessiontime'] / 3600);
            $rest = $session['acctsessiontime'] - $sessionHour * 3600;
            $sessionMinute = floor($rest / 60);
            $rest = $rest - $sessionMinute * 60;
            $sessionSec = $rest;

            if ($sessionMinute < 10)
            {
                $sessionMinute = "0{$sessionMinute}";
            }

            if ($sessionSec < 10)
            {
                $sessionSec = "0{$sessionSec}";
            }

            $sessions[$key]['acctsessiontime'] = $sessionHour . ':' . $sessionMinute . ':' . $sessionSec;
        }

        $clienInfoModel = new ClientModel();
        $this->view->client = $login;
        $this->view->sessions = $sessions;
    }

    public function closeSessionAction()
    {
        $radactID = $this->_request->getParam('id');
        $login = $this->_request->getParam('l');
        $radacctModel = new Radacct();
        $radacctModel->closeSession($radactID);
        $this->_redirect("/support-client/sessions/user/{$login}");
    }

    public function deleteSessionAction()
    {
        $radactID = $this->_request->getParam('id');
        $login = $this->_request->getParam('l');
        $radacctModel = new Radacct();
        $radacctModel->deleteSession($radactID);
        $this->_redirect("/support-client/sessions/user/{$login}");
    }

    public function serviceAction()
    {
    	$login   = $this->_request->getParam('login');
    	$ctype   = $this->_request->getParam('ctype');
    	$pid     = $this->_request->getParam('pid');

    	$radacct 		  = new Radacct();
    	$freeTrafficModel = new FreeTraffic();

    	$serviceType = new ServiceType();
    	$this->view->login = $login;
    	$this->view->ctype = $ctype;

    	$this->view->serviceTypes = $serviceType->fetchOptions();

    	$this->view->month = date('m');
    	$this->view->year = date('Y');


		$corpModel = new CorpTraffic();

		if($this->_request->isPost())
		{
			$month = $this->_request->getPost('month');
			$year = $this->_request->getPost('year');

			$startdate = $year. "-".$month."-01 00:00:00";
			$end       = $year. "-".$month."-".date('t', strtotime($startdate))." 23:59:59";

			if($ctype == "0")
			{
				//$this->view->getList = $corpModel->showStat($pid, $startdate, $end);
                //$this->view->getTotal = $corpModel->getTotalBytes($pid, $startdate, $end);

				$this->view->traffic     = $corpModel->showStat($pid, $startdate, $end);
	    		$this->view->freeTraffic = 0;
			}
			else
			{
				$this->view->traffic     = $radacct->getTraffic($login, $startdate, $end);
	    		$this->view->freeTraffic = $freeTrafficModel->getTraffic($login, $startdate);
			}
		}
		else
		{
	    	$this->view->traffic     = $radacct->getTraffic($login, date('Y-m-01'), date('Y-m-t'));
	    	$this->view->freeTraffic = $freeTrafficModel->getTraffic($login, date('Y-m-01'));
		}
    }

    public function checkRadiusAction()
    {
    	if ($this->_request->isPost())
    	{
    		$login = trim($this->_request->getParam('login'));
    		$password = $this->_request->getParam('password');
    		$serviceType = $this->_request->getParam('servicetype');
    		$this->view->login = $login;
    		$this->view->password = $password;
    		$this->view->serviceType = $serviceType;

    		$radreplyModel = new Radreply();
    		$radcheckModel = new Radcheck();
    		$userGroupModel = new UserGroup();
    		$radreplyModel->deleteAttributeValue($login, $serviceType, Radreply::SESSION_TIMEOUT_ATTRIBUTE);
    		$radreplyModel->deleteAttributeValue($login, $serviceType, Radreply::SESSION_OCTETS_LIMIT);

    		$this->view->setLimitResult = $radreplyModel->setLimit($login, $serviceType);

    		$this->view->passwordCheckResult = $radcheckModel->checkPassword($login, $password);

    		$this->view->radreply = $radreplyModel->getAttributes($login, $serviceType);

    		$this->view->userGroup = $userGroupModel->getGroup($login);
    	}
    }
}

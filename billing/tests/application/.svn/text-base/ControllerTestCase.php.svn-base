<?php
require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

abstract class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * @var Zend_Application
     */
    protected $_application;
 
    public function setUp()
    {
       // указываем функцию, которая будет выполнена до запуска тестов
       $this->bootstrap = array($this, 'appBootstrap');
       parent::setUp();
    }
 
    public function appBootstrap()
    {
    	$iniFile = realpath(APPLICATION_PATH . '/../config/config.ini');
    	
        // инициализируем наше приложение
        $this->_application = new Zend_Application(
            APPLICATION_ENV,
            $iniFile
        );
        
        $this->frontController->setControllerDirectory(APPLICATION_PATH . '/default/controllers', 'default');
        
        $config = new Zend_Config_Ini($iniFile,'general');
        $dbAdapter = Zend_Db::factory($config->db->adapter,$config->db->config->toArray());
        Zend_Db_Table::setDefaultAdapter($dbAdapter);
		Zend_Registry::set('dbAdapter', $dbAdapter);
        
		Zend_Session::start();
		Zend_Session::setOptions( array('strict'=>true) );
		
        $this->_application->bootstrap();
    }
    
	protected function _doLogin($login = 'progers', $password = 'z574KX')
	{
		$dbAdapter = Zend_Registry::get('dbAdapter');
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter, 'users', 'login', 'password', 'MD5(?)');
        $authAdapter->setIdentity($login);
        $authAdapter->setCredential($password);
         
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);

        if ($result->isValid())
        {
        	// success: store database row to auth's storage
            // system. (Not the password though!)
            $data = $authAdapter->getResultRowObject(array('id', 'first_name', 'last_name', 'home_page', 'is_deleted', 'country'));

			if ( ! $data->is_deleted)
            {
            	$_SESSION['privs'] = $this->getAllPrivs($dbAdapter);
                $homePage = $data->home_page;
                $auth->getStorage()->write($data);
			}
			else {
				throw new Exception('user has been deleted');
			}
        }
        else {
        	throw new Exception('can not login');
        }
	}
	
	protected function loginAndTestGet($url) 
	{
		// login
    	$this->_doLogin();
    	$this->request->setMethod('GET');
    	// GET 
    	$urlParts = explode('/', $url);
    	$this->dispatch($url);
    	$this->assertModule('default');
    	$this->assertRoute('default');
    	$this->assertNotRedirect();
		$this->assertController($urlParts[0]);
		$this->assertAction($urlParts[1]);
	}
	
	protected function generateRandomStr() 
	{
		return md5('test101' + (string)rand() + 'test102' + (string)rand());
	}
	
	protected function generateRandomValidCrossPhone() 
	{
		require_once('ClientHelper.php');
		// выглядит все это некрасиво но работает
		$shortPrefixList = array('222', '212', '249', '272', '279');
		foreach ($shortPrefixList as $prefix) 
			for ($i = 0; $i <= 9; $i++)
				for($j=0; $j <= 9; $j++)
					for($k=0; $k <= 9; $k++)
						for($l=0; $l <= 9; $l++)
						{
							$number = "{$prefix}{$i}{$j}{$k}{$l}";
							if (!ClientHelper::checkForBadNumber(0, $number))
							{
								$ats = ClientHelper::getRecommendedAts($number);
								if ($ats && ClientHelper::getFirstAvailableDslam($ats->id, 0))
								{
									return $number;
								}
							}
						}
	}

	private function process($depth, $length, $number, $validatorObj, $validatorFun, $negate=false)
	{
		for ($i = 0; $i <= 9; $i++)
		{
			$number{$depth} = $i;
			$test = $validatorObj->$validatorFun($number);
			if ($negate)
			{
				$test = !$test;
			}
			if ($test)
			{
				return $number;
			}

			if ($depth < $length)
			{
				$result = $this->process($depth + 1, $length, $number, $validatorObj, $validatorFun);
				if ($result)
				{
					return $result;
				}
			}
		}
	}

	private function generateValidNumber($length, $validator, $negate=false)
	{	
		$validatorObj =  $validator[0];
		$validatorFun = $validator[1];
		$number = '';
		for ($i = 0; $i < $length; $i++)
		{
			$number .= 0;
		}
		return $this->process(0, $length, $number, $validatorObj, $validatorFun, $negate);
	}
	
	protected function generateValidAccountNumber()
	{
		$clientObj = new ClientModel();
		return $this->generateValidNumber(20, array($clientObj, 'varifyRschet'));
	}
	
	protected function generateValidInn()
	{
		$clientObj = new ClientModel();
		return $this->generateValidNumber(9, array($clientObj, 'verifyInn'), true);
	}
	
	private function getAllPrivs($adapter)
	{
		$sql = "
			SELECT DISTINCT
                modules.name AS module_name,
                actions.name AS action_name
            FROM
                modules
            LEFT JOIN actions ON actions.module_id = modules.id
		";
		
		return $adapter->fetchAll($sql);
	}
}
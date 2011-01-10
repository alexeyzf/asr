<?
require_once ('Zend/Paginator.php');
require_once ('Zend/Paginator.php');
require_once ('Zend/Paginator/Adapter/Array.php');
require_once ('Zend/View/Helper/PaginationControl.php');
require_once ('Asr/FormHelper.php');
require_once ('SearchAbonDepartment.php');
require_once ('AsrHelp.php');
require_once ('GenCrossNumberModel.php');
require_once ('AbonDepartmentHistoryHelper.php');
require_once ('forms/SearchClient.php');

class SupportController extends Zend_Controller_Action
{
    public function indexAction()
    {

		 $client_name = $this->_request->getParam('restoreclient');
		 $stypes['type_search'] = array(
         	'PTS.u_login'      => 'по логину',
			'PTS.pcross'       => 'по телефону кроссировки',
			'PTS.contact_name' => 'по конт. телефону',
			'CLA.client_name'  => 'по наименованию'
         );
		 if($client_name == "")
		 {

		 }
		 else
		 {
		 	$stypes[0]['searchword'] = $client_name;
		 }
    	 $form = new Form_SearchClient();
		 $form->populate($stypes);
		 $this->view->form = $form;

		 if ($this->_request->isPost())
		 {
		 	 $type = $this->_request->getPost();
			 $form = new Form_SearchClient();
			 $form->populate($stypes);
			 $this->view->form = $form;
         }


         //
		 // Результат работы данной модели будет передаваться в объект пагинатора
		 $searchAbonDepModel = new SearchAbonDepartment();
	     $result_rows = $searchAbonDepModel->searchWithParam($type['type_search'], $type['searchword']);

	     $num = 3; //кол-во строк которые следует вывести
		 $page = $this->_getParam('page'); // Принимаем параметры
		 if($page < 1 or empty($page))
		 {
	 		$page = 1;
		 }
		 $start = $page * $num - $num; // нумерация страниц
		 // Создаем пагинатор объект и передаем ему данные из модели SearchAbonDepartment
		 $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($result_rows));
		 $paginator->setCurrentPageNumber($page);
		 $paginator->setItemCountPerPage($num);
		 $paginator->setView($this->view);
		 Zend_View_Helper_PaginationControl::setDefaultViewPartial('/my_pagination.phtml');
		 $this->view->paginator = $paginator;
    }

    public function repairInternetAction()
    {
        $data['u_login']     = $this->_request->getParam('u_login');
        $ctype               = $this->_request->getParam('ctype');
        $data['tablename']   = $this->_request->getParam('tablename');
        $this->_helper->layout->setLayout('repair-internet');

        $result = ServiceProblemHelper::getInfoAboutServiceStream($data, $ctype);

        $this->view->result = $result;
    }

    public function showLinksAction()
    {
        $data['u_login']     = $this->_request->getParam('u_login');
        $ctype               = $this->_request->getParam('ctype');
        $data['tablename']   = $this->_request->getParam('tablename');

        $modelTrap = new TrapModel();
        $result    = $modelTrap->getTraps($data['u_login']);

        $this->_helper->layout->setLayout('client-links');
        $this->view->result = $result;
    }

    public function showAdditionalServicesAction()
    {
		$pointID  = $this->_request->getParam('point_id');
		$username = $this->_request->getParam('username');
		if($pointID)
		{
			$supportModel 	   = new SupportModel();
			$trustPaymentModel = new TrustPaymentModel();

			$this->view->megabit_button =  $supportModel->getMegabitButtonService($pointID);
			$this->view->trustPayments  = $trustPaymentModel->getTrustPayment($username, 1);

			$this->_helper->layout->setLayout('support-additional');
		}
    }


    public function cabinetLogViewAction()
    {
		$username = $this->_request->getParam('u_login');

		if($username)
		{
			$logModel 	   = new LogModel();
 
            $this->view->data = $logModel->getLogsByUsername($username);
			$this->_helper->layout->setLayout('cabinet-log');
		}
    }
}
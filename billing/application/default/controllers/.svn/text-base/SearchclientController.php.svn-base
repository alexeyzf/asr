<?php
require_once ('BaseController.php');
require_once ('Zend/Paginator.php');
require_once ('Zend/Paginator.php');
require_once ('Zend/Paginator/Adapter/Array.php');
require_once ('Zend/View/Helper/PaginationControl.php');
require_once ('SearchAbonDepartment.php');
require_once ('AsrHelp.php');
require_once ('GenCrossNumberModel.php');
require_once ('AbonDepartmentHistoryHelper.php');
require_once ('forms/SearchClient.php');

class SearchclientController extends BaseController
{
	static $flagtype;

	public function indexAction()
	{
		$client_name = $this->_request->getParam('restoreclient');

        $param = $this->_request->getParam('param');
        $value = $this->_request->getParam('value');

        if($param)
        {
			$page = $this->_request->getParam('page');

	        $model = new ClientModel();

	        $paginatorAdapter = $model->searchToPaginatorDbAdapter($param, $value);
	        $paginator = new Zend_Paginator($paginatorAdapter);
	        $paginator->setItemCountPerPage(25);
	        $paginator->setCurrentPageNumber($page);
	        $paginator->setView($this->view);
	        Zend_View_Helper_PaginationControl::setDefaultViewPartial('/my_pagination.phtml');

	        $this->view->param = $param;
	        $this->view->value = $value;
	        $this->view->clients = $paginator;
	        
	        if ($page == 0) 
	        {
	        	$page = 1;
	        }
	        
	        $this->view->start = ($page - 1) * 25 + 1;
        }
	}

	public function iframeAction()
	{
		$table = $this->_request->getParam('table');
		$this->_helper->layout->setLayout('iframe');

		$stypes['type_search_options'] = array(
			'PTS.u_login'		=> 'по логину',
			'CLA.client_id'		=> 'по ID клиента',
			'PTS.pcross'		=> 'по телефону кроссировки',
			'CLA.client_name'	=> 'по наименованию'
		);

		if ($this->_request->isPost())
		{
			$type = $this->_request->getPost();

			$stypes['type_search'] = $type['type_search'];
			$stypes['searchword']  = $type['searchword'];
		}

		$form = new Form_SearchClient();
		$form->populate($stypes);
		$this->view->form = $form;

		// Результат работы данной модели будет передаваться в объект пагинатора
		$searchAbonDepModel = new SearchAbonDepartment();
		$result_rows = $searchAbonDepModel->serchViaIframe($type['type_search'], $type['searchword'], $table, 2);

		$num = 5; //кол-во строк которые следует вывести
		$page = $this->_getParam('page'); // Принимаем параметры;

		if($page < 1 or empty($page))
		{
			$page = 1;
		}

		$start = $page * $num - $num; // нумерация страниц
		// Создаем пагинатор объект и передаем ему данные из модели SearchAbonDepartment
		$paginator = new Zend_Paginator($result_rows);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($num);
		$paginator->setView($this->view);
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('/my_pagination.phtml');
		$this->view->paginator = $paginator;
		$this->view->table = $table;
	}
}
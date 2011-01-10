<?php
require_once ('BaseController.php');
require_once ('AbonHistoryModel.php');
require_once ('SearchAbonDepartment.php');
require_once ('Zend/Paginator.php');
require_once ('Zend/Paginator.php');
require_once ('Zend/Paginator/Adapter/Array.php');
require_once ('Zend/View/Helper/PaginationControl.php');
require_once ('Asr/FormHelper.php');

class LogController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$searchBy = $this->_request->getParam('by');
		$searchVal = $this->_request->getParam('value');
		$abonHistoryModel = new AbonHistoryModel();
		$result = $abonHistoryModel->searchQuery($searchBy, $searchVal);
		$this->view->search_by = $searchBy;
		$this->view->value = $searchVal;
		 
		//Paginator
		$num = 100; 						  //кол-во строк которые следует вывести
		$page = $this->_getParam('page'); // Принимаем параметр
		
		//Если параметр page не существует то задаем
		//его по умолчанию $page=1
		if($page < 1 or empty($page))
		{
			$page = 1;
		}
		
		$start = $page * $num - $num; // нумерация страниц
	 	// Результат работы данной модели будет передаваться в объект пагинатора
	 	$searchAbonDepModel = new SearchAbonDepartment();
	  	// Создаем пагинатор объект и передаем ему данные из модели SearchAbonDepartment
		$paginator = new Zend_Paginator($result);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($num);
		$paginator->setView($this->view);
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('/my_pagination.phtml');
		$this->view->paginator = $paginator;
	}
}
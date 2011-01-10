<?php
/**
 * Controller for tech cross pages
 * 
 * @author marat
 */

require_once ('BaseController.php');

class TechCallController extends BaseController 
{
	public function indexAction()
    {
		$callsListModel = new EngineerCalls();
		$status			= $callsListModel->getStatusCall();
		$list 			= $callsListModel->getCallsList();

		$page = $this->_request->getParam('page');
		$this->view->status    = $status;
		$this->view->paginator = PaginatorHelper::getPaginator($list, $page);
		$_SESSION['back_url'] = $_SERVER['REQUEST_URI'];

    }
    
	public function saveAction()
	{
		$data = $this->_request->getPost();
		$updateModel = new EngineerCalls();

		foreach($data['calls'] as $key => $value)
		{
			if($value['flag'] != "")
			{
				$row_up = $updateModel->updateStatusCall($key, $value['status'], $value['whatmodem']);
			}
		}
		$this->_redirect($_SESSION['back_url']);
	}
}
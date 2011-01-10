<?php
require_once ('BaseController.php');
require_once ('NoneactivModel.php');
require_once ('DistrictDistributionHelper.php');

class NoneactivController extends BaseController
{
	public function indexAction()
	{
		$page = $this->_request->getParam('page');
		$paramDate = $this->_request->getParam('date');
		
		if ( ! $paramDate )
		{
			$paramDate = 'now';
		}
		
    	$endDate = date('Y-m-d', strtotime($paramDate));
    	
    	$model = new NoneactivModel();
    	$data  = $model->getNoneActivClients($endDate);
    	
    	$this->view->data = DistrictDistributionHelper::distribute($data);

    	//$this->view->paginator = PaginatorHelper::getPaginator($data, $page, 50);
  	}
}
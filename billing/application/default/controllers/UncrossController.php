<?php
require_once ('BaseController.php');
require_once ('EditPointModel.php');

class UncrossController extends BaseController
{
    public function indexAction()
    {
    	$model = new EditPointModel();
    	$data = $model->getUncrossClients();
    	$this->view->dataClients = DistrictDistributionHelper::distribute($data);
    }
}
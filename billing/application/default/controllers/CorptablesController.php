<?php
require_once ('BaseController.php');
require_once ('CorptableModel.php');


class CorptablesController extends BaseController
{
  public function indexAction()
  {
  	$model = new CorptableModel();
  	$data = $model->getNewCorps();
	$this->view->data = $data;

  }

  public function createtableAction()
  {
  	$login = $this->_request->getParam('login');
  	//var_dump($login);
  	//exit();
  	$mysqlModel = new MysqlTraffic('mysqlAdapter');
  	$mysqlModel->createTableCorp(trim($login));
  }

}
?>

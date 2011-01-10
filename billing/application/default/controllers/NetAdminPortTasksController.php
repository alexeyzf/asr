<?php
/**
 * Controller for net admin port tasks pages
 *
 * @author elle
 */

require_once('BaseController.php');

class NetAdminPortTasksController extends BaseController
{
    public function indexAction()
    {
//    	$this->_helper->viewRenderer->setNoRender();
    	$portTasks = new Porttasks();
    	$this->view->all_tasks = $portTasks->getAll();
    }
}
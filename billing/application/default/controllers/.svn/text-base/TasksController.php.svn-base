<?php
/**
 * Default controller for accounting department
 *
 */

require_once ('BaseController.php');
require_once ('Porttasks.php');

class TasksController extends BaseController
{

    public function indexAction()
    {

    }

    public function selecttaskAction()
    {
    	$data = $this->_request->getPost();
        $model_task = new Porttasks();

        $startdate = $data['start_year']. "-". $data['start_month']. "-". $data['start_day'];
        $enddate   = $data['end_year']. "-". $data['end_month']. "-". $data['end_day'];
		//getNotCarriedTasksData
        $data = $model_task->taskList($startdate, $enddate);

        $this->view->task_list 	 = $data;
        $this->view->carriedList = $model_task->getNotCarriedTasksData();
    }

    public function deletetaskAction()
    {
        $data = $this->_request->getPost();

        $clearArr = array();

        if($this->_request->isPost())
        {

            foreach($data as $value)
            {
                foreach($value as $key => $need)
                {

                   if($need['needdelete'] == "-3")
                   {
                        array_push($clearArr, $key);
                   }
                }
            }


            $model = new Porttasks();
            $model->deleteTasks($clearArr);
        }

    	$this->_redirect('/Tasks/');
    }
}



<?php
require_once ('BaseController.php');
require_once ('EngineerCalls.php');
require_once ('PaginatorHelper.php');
require_once ('CallStatusHelper.php');

class CallsController extends BaseController
{
    public function indexAction()
    {
        $callsListModel = new EngineerCalls();
        $status            = $callsListModel->getStatusCall();
        $list             = $callsListModel->getCallsList();

        $page = $this->_request->getParam('page');
        $this->view->status    = $status;
        $this->view->paginator = PaginatorHelper::getPaginator($list, $page);
        $_SESSION['back_url'] = $_SERVER['REQUEST_URI'];

    }

    public function saveAction()
    {
        $data = $this->_request->getPost();
        $updateModel = new EngineerCalls();

        try
        {
            $updateModel->startTransaction();

            foreach($data['calls'] as $key => $value)
            {
                if($value['flag'] != "")
                {
                    $row_up = $updateModel->updateStatusCall($key, $value['status'], $value['whatmodem']);
                }
            }

            $updateModel->commitTransaction();
        }
        catch(Exception $ex)
        {
            $updateModel->rollbackTransaction();
            print $ex;
            exit;
        }

        $this->_redirect($_SESSION['back_url']);
    }
}

?>
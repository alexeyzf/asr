<?php
require_once ('BaseController.php');
require_once ('NgnModel.php');
require_once('ReportModel.php');


class NgnController extends BaseController
{
    public function indexAction()
    {
		if($this->_request->isPost())
		{
			$ngn_number = trim($this->_request->getPost('ngn_number'));

			$ngnModel = new NgnModel();
			$result   = $ngnModel->searchNgnNumber($ngn_number);

			$this->view->result = $result;

		}
    }

    public function registrationAction()
    {
        $ngnModel = new NgnModel();
        $this->view->ngnData = $ngnModel->showNotRegisteredNumbers();
    }


    public function doneAction()
    {
        if($this->_request->isPost())
        {

            $model = new NgnModel();

            $number_done = $this->_request->getParam('number');
            $point_id    = $this->_request->getParam('pid');

            $flag = $model->setIsDone($point_id, $number_done);
            //MessageHelper::addInfo('done', $tranlsate->_('All has passed successfully'));
            $this->_redirect('/Ngn/registration');
        }
    }
/*
    public function showstatAction()
    {
    	require_once('forms/DatePeriod.php');

        $number = $this->_request->getParam('ngn_number');


    	$form = new Form_DatePeriod();
    	$this->view->form = $form;

        $ngnModel    = new NgnModel();
        $reportModel = new ReportModel();

		if($this->_request->isPost())
		{
			$postData = $this->_request->getPost();
			if ($form->isValid($postData))
    		{
    			$start = $form->getValue('startdate');
    			$end   = $form->getValue('enddate');

				$tarifs          = $ngnModel->getTarifList();
	        	$allClientsCalls = $ngnModel->getNGNCalls($number, $start, $end);
    		}
		}

        $this->view->listCalls = $reportModel->getCalls($allClientsCalls, $tarifs);

    }
*/

    public function showstatAction()
    {
    	require_once('forms/DatePeriod.php');

        $number = $this->_request->getParam('ngn_number');


    	$form = new Form_DatePeriod();
    	$this->view->form = $form;

        $ngnModel    = new NgnModel();
        $reportModel = new ReportModel();

		if($this->_request->isPost())
		{
			$postData = $this->_request->getPost();
			if ($form->isValid($postData))
    		{
    			$start = $form->getValue('startdate');
    			$end   = $form->getValue('enddate');

				$phoneServicesCalls = new PhoneServicesCalls();
    			$phoneServicesTarifs = new PhoneServicesTarifs();

				$arr['numeber'] = $number;

    			$calls = $phoneServicesCalls->getCalls($start, $end, $arr);
    			$tarifs = $phoneServicesTarifs->getList($start, $end);

    			$reportModel = new ReportModel();

    			$this->view->data = $reportModel->getCalls($calls, $tarifs);
    		}
		}
    }
}
?>

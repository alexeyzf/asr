<?php
require_once ('BaseController.php');
//require_once ('MessagesHelper.php');

class SigmaKapitalController extends BaseController
{

    public function indexAction()
    {
        require_once('forms/DatePeriod.php');
		$form = new Form_DatePeriod();
		$this->view->form = $form;


        $sigmaKapitalModel = new SigmaKapitalModel();

        $this->view->data = $sigmaKapitalModel->getSigmaSerial();

        if($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            if($form->isValid($data))
            {
                $startDate = $form->getValue('startdate');
                $endDate   = $form->getValue('enddate');

                $auth = Zend_Auth::getInstance();
                $user = $auth->getStorage()->read();

                $sigmaKapitalModel->setSigmaSerial($data['new_serial'], $data['new_percent'], $startDate, $endDate, $user->id);

                $this->_redirect('/Sigma-kapital/index');
            }
        }


    }

    public function closeAction()
    {
        if($this->_request->isPost())
        {
            $serial    = $this->_request->getPost('is_serial');
            $serial_id = $this->_request->getPost('serial_id');

            $sigmaKapitalModel = new SigmaKapitalModel();

            $nonActivatedCards = $sigmaKapitalModel->verifyNonActivCardsSerial($serial);

            if(count($nonActivatedCards) > 0)
            {
                $this->view->msg    = ' На данный момент существуют не активированные карты данной серии';
                $this->view->data   = $nonActivatedCards;
                $this->view->serial = $serial;
            }
            else
            {
                $this->view->msg  = 'Не активированные карты из данной серии отсутствуют';
                $this->view->serial = $serial;
            }
        }
    }

    public function postCloseSerialAction()
    {
        if($this->_request->isPost())
        {
            $serial = $this->_request->getPost('del_serial');



            $sigmaSerialModel = new SigmaKapitalModel();
            $sigmaSerialModel->closeSigmaSerial($serial);

            $this->_redirect('/Sigma-kapital/index');
        }
    }


    public function getStatisticAction()
    {
    	require_once('forms/DatePeriod.php');
		$form = new Form_DatePeriod();
		$this->view->form = $form;

		$sigmaKapitalModel = new SigmaKapitalModel();

    	if($this->_request->isPost())
    	{
            $data = $this->_request->getPost();

            if($form->isValid($data))
            {
                $startDate = $form->getValue('startdate');
                $endDate   = $form->getValue('enddate');

				$startDate = $startDate. " 00:00:00";
				$endDate   = $endDate. " 23:59:59";

                $this->view->getstat = $sigmaKapitalModel->getStatisticSigma($startDate, $endDate);
                //$result = $sigmaKapitalModel->getStatisticSigma($startDate, $endDate);



				$this->view->group   = $sigmaKapitalModel->groupingCash($startDate, $endDate);
            }
    	}
    }

}
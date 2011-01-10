<?php

/**
 * Controller for tech courier pages
 *
 * @author marat
 */

require_once('BaseController.php');

class TechCourierController extends BaseController
{
    public function indexAction()
    {
        if ($this->_request->isPost())
        {
        	$day   = $this->_request->getParam('day');
			$month = $this->_request->getParam('month');
			$year  = $this->_request->getParam('year');

			$this->view->day   = $day;
			$this->view->month = $month;
			$this->view->year  = $year;

			$need_date = $year."-".$month."-".$day;

            $date = date('Y-m-d', strtotime( '+1 day '. $need_date));

            $hub = $this->_request->getPost('hub');

            $phoneHubModel = new PhoneHubList();
            $hubRow = $phoneHubModel->fetchRecordByID($hub);
            $hubName = $hubRow->name;

            $lettersModel = new LettersToAts();
            $letters = $lettersModel->getLettersByHub($hub, $date);

            $rekvizits = Zend_Registry::get('rekvizits');
            require_once('CourierTableRtf.php');
            $rtfHelper = new CourierTableRtfHelper($rekvizits->company_name);
            $rtfHelper->generate($hubName, $date, $letters);
        }
        else
        {
			$this->view->day   = date('d');
			$this->view->month = date('m');
			$this->view->year  = date('Y');

            $phoneHubModel = new PhoneHubList();
            $this->view->hubs = $phoneHubModel->getNotDeletedOptions();
        }
    }
}
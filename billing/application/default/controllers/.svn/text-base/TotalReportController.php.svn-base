<?php
/*
 * Created on 18.08.2009
 * Редактирование, изменение тарифных планов для услуг
 */

require_once ('BaseController.php');

class TotalReportController extends BaseController
{
    public function indexAction()
    {
    	$this->view->month = date('m');
    	$this->view->year = date('Y');

        if($this->_request->isPost())
        {
            $month   = $this->_request->getParam('month');
            $year    = $this->_request->getParam('year');
            $by_type = trim($this->_request->getParam('by_type'));
            $is_face = trim($this->_request->getParam('is_face'));

            $this->view->month = $month;
            $this->view->year = $year;
            $this->view->by_type = $by_type;
            $this->view->is_face = $is_face;

            $streamModel 	  = new StreamModel();
            $totalReportModel = new TotalReportModel();

            if($is_face == "by_dialup")
            {
                $date = $year."-".$month;
                $this->view->type = $by_type;
                $this->view->face = $is_face;

                $this->view->data = $streamModel->getDialup($date);

				$this->render('dialup-clients-layout');
            }

            if($is_face == "by_corp_phone")
            {
                $date = $year."-".$month;
                $this->view->type = $by_type;
                $this->view->face = $is_face;

                $this->view->data = $streamModel->getCorpPhoneService($date, 0);

				$this->render('corp_phones_layout');
            }

            if($is_face == "by_stream_phone")
            {
                $date = $year."-".$month;
                $this->view->type = $by_type;
                $this->view->face = $is_face;

                $this->view->data = $streamModel->getCorpPhoneService($date, 1);

				$this->render('corp_phones_layout');
            }

            if($by_type == "by_pay" and ($is_face == "1" or $is_face == "0"))
            {
                $date = $year."-".$month;
                $this->view->type = $by_type;

                $this->view->data = $totalReportModel->getReportTotal($date, $month, $year, $is_face);
                if($is_face == 1)
                {
					$this->render('index_stream');
                }
                else
                {
					$this->render('index_corp');
                }
            }

            if($by_type == "by_ports"  and ($is_face == 1 or $is_face == 0))
            {
                $date = $year."-".$month;
                $this->view->type = $by_type;
                $this->view->data = $streamModel->getByPorts($date, $month, $year);
            }

            if($by_type == "by_clients"  and ($is_face == 1 or $is_face == 0))
            {
                $date = $year."-".$month;
                $this->view->type = $by_type;
                $this->view->data = $streamModel->getByClient($date, $month, $year, $is_face);
                $this->view->startDate = $date . '-01';
            }
        }
    }

}
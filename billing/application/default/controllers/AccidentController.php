<?php

require_once ('BaseController.php');

class AccidentController extends BaseController
{

    public function indexAction()
    {
        $accidentModel = new AccidentModel();

        $this->view->dataList = $accidentModel->getAllAccidents(1);
    }

    public function showFrameAction()
    {
        $atsModel = new AtsList();
        $this->view->data = $atsModel->getOptions();
    }

    public function markDslamAction()
    {
        if($this->_request->isPost())
        {
            $data = $this->_request->getPost();


            $arr = array();

            foreach($data as $key => $value)
            {
                if($value['check'] and $value['dslam'] != "")
                {
                    array_push($arr, $value);
                }
            }

            $accidentModel = new AccidentModel();
            $result        = $accidentModel->addAccidentsDslam($arr);
            $this->_redirect('/Accident/index');
        }
    }

    public function closeAccidentAction()
    {
        $id = $this->_request->getParam('id');
        
        if( $id )
        {
            $accidentModel = new AccidentModel();
            $accidentModel->setClosed($id);
            $this->_redirect('/Accident/index');
        }
    }

    public function jurnalAction()
    {
        $accidentModel = new AccidentModel();

        $this->view->data = $accidentModel->getAllAccidents(2);
    }
}
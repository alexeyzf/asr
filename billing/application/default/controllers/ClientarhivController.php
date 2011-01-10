<?php
require_once ('BaseController.php');
require_once ('forms/Arhivclient.php');


class ClientarhivController extends BaseController
{

    public function indexAction()
    {

        $postvar = $this->_request->getPost();
        $arrTypes = array (
            'CLA_a.client_name'     => 'По наименованию',
            'CLA_a.client_id'       => 'По ID клиента',
            'CLA_a.phone'           => 'По конт. тел.',
            'PTS_a.point_id'        => 'По ID точки',
            'PTS_a.u_login'         => 'По логину',
            'PTS_a.connect_address' => 'По адресу подключения'
        );

        $values['types'] = $arrTypes;


        $form = new Form_Arhivclient();
        $form->populate($values);
        $this->view->form = $form;

        if($this->_request->isPost())
        {
            $type = $this->_request->getPost();


            $form = new Form_Arhivclient();
            $form->populate($values);
            $this->view->form = $form;
        }

        // Результат работы данной модели будет передаваться в объект пагинатора
        $searchArhivModel = new Arhivservices();
        $result_rows = $searchArhivModel->searchInArhiv($type['type_search'], $type['searchword']);

        $num = 3; //кол-во строк которые следует вывести
        $page = $this->_getParam('page'); // Принимаем параметры
        if($page < 1 or empty($page))
        {
            $page = 1;
        }
        $start = $page * $num - $num; // нумерация страниц
        // Создаем пагинатор объект и передаем ему данные из модели SearchAbonDepartment
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($result_rows));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($num);
        $paginator->setView($this->view);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('/my_pagination.phtml');
        $this->view->paginator = $paginator;
    }

    public function viewAction()
    {
    	$clientID = $this->_request->getParam('id');
    	$clientArhivModel = new ClientsArhiv();
    	$financeModel     = new FinanceModel();

    	$client   = $clientArhivModel->getInfo($clientID);
		$listtran = $financeModel->getTransactionList($clientID);


		$this->view->services = $clientArhivModel->getArhivServiceByClientID($clientID);
    	$this->view->data = $client;
    	$this->view->finance = $listtran;
    }

    public function viewTrafficAction()
    {
   		$ip 	  = $this->_request->getParam('ipaddress');
   		$clientID = $this->_request->getParam('client_id');



		$this->_helper->layout->setLayout('traffic-layout');

		require_once('forms/DatePeriod.php');
		$form = new Form_DatePeriod();
		$this->view->form = $form;

		$this->view->ipaddress = $ip;

		$corpModel = new CorpTraffic();

        if($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            if($form->isValid($data))
            {
                $startDate = $form->getValue('startdate');
                $endDate   = $form->getValue('enddate');

                $this->view->getList = $corpModel->showStat($ip, $startDate, $endDate, 1);

                $this->view->getTotal = $corpModel->getTotalBytes($ip, $startDate, $endDate, null, 1);
            }
        }

    }
}
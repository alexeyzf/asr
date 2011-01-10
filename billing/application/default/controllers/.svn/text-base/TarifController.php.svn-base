<?php
/*
 * Created on 18.08.2009
 * Редактирование, изменение тарифных планов для услуг
 */

require_once('forms/Startdate.php');
require_once('forms/Tarif.php');
require_once('forms/BulkChange.php');
require_once('forms/TarifSpeedReport.php');
require_once('forms/TarifReport.php');
require_once('forms/TarifSummaryReport.php');
require_once('forms/TarifComponent.php');
require_once('ListServiceModel.php');
require_once('EmployeeFormHelper.php');
require_once('TarifListModel.php');
require_once('BaseController.php');
require_once('TarifHelper.php');
require_once('UtilsHelper.php');

class TarifController extends BaseController
{
    public function indexAction()
    {
		$listTarifs = new TarifListModel();
		$list 		= $listTarifs->getListTarif();

		$this->view->datalist = $list;
    }

    public function bulkChangeAction()
    {
        $tarifList = new TarifListModel();
        $adslList = $tarifList->getServiceTarifs(TarifListModel::STREAM);

        $form = new Form_BulkChange();
        $form->populate($adslList);
        $this->view->form = $form;

        if ($this->_request->isPost())
        {
                $postData = $this->_request->getPost();
                $form->populate($data);

                if ($form->isValid($postData))
                {
                    $formData = $form->getValues();
                    $result = $this->bulkChange(array(
                        'oldTarifId' => $formData['current_tarif_id'],
                        'newTarifId' => $formData['new_tarif_id']
                    ));
                    $this->view->data = $result;
                }
        }
    }

    public function bulkChange($data)
    {
        $tarifModel = new TarifListModel();
        $result = $tarifModel->doBulkTarifUpdate($data['oldTarifId'], $data['newTarifId']);
        if (!$result)
        {
            $result = 'Тарифы успешно изменены';
        }
        return $result;
    }

    public function editAction()
    {
    	$tarifID = $this->_request->getParam('id');

        $tarifModel   = new TarifListModel();
        $serviceModel = new ListServiceModel();

        $services               = $serviceModel->getAllService();
        $clientTypes            = EmployeeFormHelper::getASRType();
        $data['servicetype_id'] = $services;
        $data['client_type']    = $clientTypes[7];

        $form = new Form_Tarif();
        $this->view->form = $form;

        if ($this->_request->isPost())
        {
                $postData = $this->_request->getPost();
                $form->populate($data);

                if ($form->isValid($postData))
                {
                    $formData = $form->getValues();
                    $tarifModel = new TarifListModel();
                    $tarifModel->saveTarif($formData, $postData['tarif_id']);
                    $this->_redirect('/tarif');
                }
        }
        else
        {
                $data = $tarifModel->getTarifData($tarifID);
                $data['servicetype_id'] = $services;
                $data['client_type'] = $clientTypes[7];
                $form->populate($data);
        }
    }

	public function addcomponentAction()
	{
		$tarif_id  = $this->_request->getParam('tarif_id');
		$show_type = $this->_request->getParam('showtype');

		$serviceTypeModel = new TarifListModel();
		$dataTarif 		  = $serviceTypeModel->getListTarif($tarif_id);

		// Знач. по умл.
		$values['weekday'] 			   = 1;
		$values['component_name'] 	   = "новое";
		$values['hours']			   = "00";
		$values['minut']			   = "00";
		$values['sec']  			   = "00";
		$values['hoursend']			   = "23";
		$values['minutend']			   = "59";
		$values['secend']  			   = "59";



		$form = new Form_TarifComponent();
		$form->populate($values);
		$this->view->form     = $form;
		$this->view->tarif    = $dataTarif;
		$this->view->nowtarif = $tarif_id;

		if($this->_request->isPost())
		{
			$dataForm = $this->_request->getPost();
			$dataForm['starttime'] = $dataForm['hours'].$dataForm['minut'].$dataForm['sec'];
			$dataForm['endtime'] = $dataForm['hoursend'].$dataForm['minutend'].$dataForm['secend'];

			$result = $serviceTypeModel->saveComponent($dataForm, $dataForm['comp_id']);
			$this->_redirect('/Tarif/index');
		}

	}

	public function editcomponentAction()
	{
		$tarif_id = $this->_request->getParam('tarif_id');
		$component_id = $this->_request->getParam('comp_id'); // ID

		$datacompModel = new TarifListModel();
		$datacomp 	   = $datacompModel->getTarifComponent($tarif_id, $component_id);
		list($hours, $minut, $sec) = explode(":",$datacomp['starttime']);
		list($hoursend, $minutend, $secend) = explode(":",$datacomp['endtime']);

		//
		$datacomp['hours'] = $hours;
		$datacomp['minut'] = $minut;
		$datacomp['sec'] = $sec;

		$datacomp['hoursend'] = $hoursend;
		$datacomp['minutend'] = $minutend;
		$datacomp['secend'] = $secend;

		$form = new Form_TarifComponent();
		$form->populate($datacomp);
		$this->view->form = $form;
		$this->view->data = $datacomp;
	}

	public function deletecomponentAction()
	{
		$component_id = $this->_request->getParam('comp_id');
		$deleteCompModel = new TarifListModel();
		$delete = $deleteCompModel->deleteComponent($component_id);

		if($delete)
		{
			$this->_redirect($_SESSION['back_url']);
		}
	}

	public function phoneTarifListAction()
	{
		$phoneServiceTarifModel = new PhoneServicesTarifs();
		$startDate = date('Y-m-01');
		$endDate = date('Y-m-t');
		$orderBy = $this->_request->getParam('order-by');

		if ( ! $orderBy )
		{
			$orderBy = 'directions';
		}

		$phoneServicesTarifs = $phoneServiceTarifModel->getList($startDate, $endDate, $orderBy);

		$this->view->phoneTarifs = $phoneServicesTarifs;
	}

	public function editPhoneTarifAction()
	{
		require_once('forms/PhoneTarif.php');

		$ID = $this->_request->getParam('id');
		$phoneServiceTarifModel = new PhoneServicesTarifs();
		$form = new Form_PhoneTarif();
		$this->view->form = $form;

		if ($this->_request->isPost())
		{
			$postData = $this->_request->getPost();

			if ($form->isValid($postData))
			{
				$formData = $form->getValues();
				AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::EDIT_PHONE_TARIF, $_SERVER['REQUEST_URI'], 0);
				$phoneServiceTarifModel->saveChanges($formData, $ID);
				$this->_redirect('/tarif/phone-tarif-list');
			}
		}
		else
		{
			$tarif = $phoneServiceTarifModel->fetchRecordByID($ID)->toArray();
			$form->populate($tarif);
		}
	}

	public function deletePhoneTarifAction()
	{
		$ID = $this->_request->getParam('id');
		$phoneServiceTarifModel = new PhoneServicesTarifs();
		$data['end_date'] = '2010-02-01';
		$phoneServiceTarifModel->update($data, "id = $ID");
		$this->_redirect('/tarif/phone-tarif-list');
	}

	public function showDublicateTarifsAction()
	{
		$tarifListModel = new TarifListModel();
		$tarifs = $tarifListModel->getDublicateTarifs();
		$this->view->tarifs = $tarifs;
	}

    public function speedReportAction()
    {
        $form = new Form_TarifSpeedReport();
        $this->view->form = $form;

        if( $this->_request->isPost() )
        {
        	$postData = $this->_request->getPost();

        	if ( $form->isValid($postData) )
        	{
        		$data = $form->getValues();
        		$date = $data['date'];
        		$service = $data['service'];
        		$tarifModel = new TarifListModel();
        		$data = $tarifModel->getDataForSpeedReport($date, $service);

        		$dataBySpeed = array();
        		foreach($data as $row)
        		{
        			$dataBySpeed[ $row['speed'] ][ $row['country_id'] ][ $row['client_type_id'] ] = $row['points_count'];
        		}

				$this->view->data = $dataBySpeed;
        	}
        }
    }

    public function tarifReportAction()
    {
    	$form = new Form_TarifReport();
    	$this->view->form = $form;

    	if ( $this->_request->isPost() )
    	{
    		$postData = $this->_request->getPost();

    		if ( $form->isValid($postData) )
    		{
    			$formData = $form->getValues();
    			$date = $formData['date'];
    			$clientType = $formData['client_type'];

    			$tarifModel = new TarifListModel();
    			$data = $tarifModel->getDataForTarifReport($date, $clientType);

    			$this->view->data = $data;
    		}
    	}
    }
    
    private function cmp($a, $b) 
    {
    	$a = (int)$a[0]['clients_sum']; 
		$b = (int)$b[0]['clients_sum'];
		if ($a == $b) 
		{
			return 0;
		}
		return $a < $b ? 1 : -1;
    }
    
    public function tarifSummaryReportAction()
    {
    	$form = new Form_TarifSummaryReport();
    	$this->view->form = $form;
    	
    	if ( $this->_request->isPost() )
    	{
    		$postData = $this->_request->getPost();

    		if ( $form->isValid($postData) )
    		{
    			$formData = $form->getValues();
    			$months = UtilsHelper::getMonthsArr(
                        $formData['start_month'],
                        $formData['finish_month'],
                        31);

                $model = new TarifListModel();
                $clientType = $formData['client_type'];
                foreach ($months as $month)
                {
                    list($startDate, $endDate) = $month;
                    if (in_array($clientType, array(0, 1)))
                    {
                        $fullInfo = $model->getFullInfoForPeriod($startDate, $endDate, $clientType);
                        $monthInfo = $model->getDataForTarifSummaryReport($startDate, $endDate, $clientType);
                    }
                    else
                    {
                        $fullInfo = $model->getFullInfoForPeriod($startDate, $endDate, 1, 7);
                        $monthInfo = $model->getDataForNovaTarifSummaryReport($startDate);
                    }
                    // var_dump(array('fullInfo' => $fullInfo, 'month' => $monthInfo)); exit;
                    foreach ($monthInfo as $row)
                    {
                        $data[$row['tarif_name']]["{$startDate}-{$endDate}"] = array(
                            'tarif_name' => $row['tarif_name'],
                            'price' => $row['price'],
                            'amount' => $row['amount'],
                            'price_percents' => $row['price'] / $fullInfo['full_sum'] * 100,
                            'amount_percents' => $row['amount'] / $fullInfo['full_amount'] * 100
                        );
                        $tarifs[] = $row['tarif_name'];
                    }
                }
                //var_dump($data); exit;
                foreach (array_unique($tarifs) as $tarif)
                {
                    foreach ($months as $month)
                    {
                        list($startDate, $endDate) = $month;
                        $result[$tarif]["{$startDate}-{$endDate}"] = array(
                            'tarif_name' => $tarif ,
                            'price' => $data[$tarif]["{$startDate}-{$endDate}"]['price'] ? $data[$tarif]["{$startDate}-{$endDate}"]['price'] : 0.0,
                            'amount' => $data[$tarif]["{$startDate}-{$endDate}"]['amount'] ? $data[$tarif]["{$startDate}-{$endDate}"]['amount'] : 0,
                            'price_percents' => $data[$tarif]["{$startDate}-{$endDate}"]['price_percents'] ? $data[$tarif]["{$startDate}-{$endDate}"]['price_percents'] : 0.0,
                            'amount_percents' => $data[$tarif]["{$startDate}-{$endDate}"]['amount_percents'] ? $data[$tarif]["{$startDate}-{$endDate}"]['amount_percents'] : 0.0
                        );
                    }
                }
                // var_dump($result); exit;
                $this->view->data = array('months' => $months, 'data' => $result);
    		}
    	}
    }

    public function tarifChangeReportAction()
    {
    	if ( $this->_request->isPost() )
    	{
    		$month 		= $this->_request->getParam('month');
    		$year 		= $this->_request->getParam('year');
    		$clientType = $this->_request->getParam('client_type');
    		$orderBy    = $this->_request->getParam('order');

    		$this->view->month 		 = $month;
    		$this->view->year 		 = $year;
    		$this->view->client_type = $clientType;
    		$this->view->order 		 = $orderBy;

    		$tarifModel = new TarifListModel();
            if (in_array($clientType, array(0, 1)))
            {
                $data = $tarifModel->getDataForTarifChangeReport($month, $year, $clientType);
            }
            else
            {
                $data = $tarifModel->getDataForNovaTarifChangeReport($month, $year);
            }
            
			$total = array();
			foreach($data as $key => $value)
			{
				if($orderBy == 0)
				{
					$str = 'from_tarif_id';
					$clear = 'from_tarif_name';
					//unset($value[$clear]);
				}
				elseif($orderBy == 1)
				{
					$str = 'to_tarif_id';
					$clear = 'to_tarif_name';
					//unset($value[$clear]);
				}

				$total[$value[$str]][] = $value;
			}

    		$this->view->data = $total;
    	}
    }

    public function tarifReportViewAction()
    {
            $tarif_id     = $this->_request->getParam('tarif_id');
            $tablename    = $this->_request->getParam('tablename');
            $isface       = $this->_request->getParam('isface');

            $tarifModel = new TarifListModel();
            $data = $tarifModel->getServiceTarifsReportClientDetails($tablename, $tarif_id, $isface);

            $this->view->data      = $data;
            $this->view->tablename = $tablename;
    }
}
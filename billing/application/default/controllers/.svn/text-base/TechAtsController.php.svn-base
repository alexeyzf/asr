<?php
/**
 * Controller for tech ats
 *
 * @author marat
 */

require_once('BaseController.php');
require_once('AtsList.php');
require_once('PhoneHubList.php');
require_once('AtsNumbers.php');
require_once('AtsBadNumbers.php');
require_once('Cities.php');
require_once('forms/Techats.php');

class TechAtsController extends BaseController
{
    /**
     * Action for list of ats
     */
    public function indexAction()
    {
        $atsListModel       = new AtsList();
        $atsNumbersModel    = new AtsNumbers();
        $atsBadNumbersModel = new AtsBadNumbers();
        $phoneHubListModel  = new PhoneHubList();

        $atsListDb     = $atsListModel->fetchAllNotDeleted();
        $phoneHubs     = $phoneHubListModel->getNotDeletedOptions();
        $atsNumbers    = $atsNumbersModel->fetchAll();
        $atsBadNumbers = $atsBadNumbersModel->fetchAll();

        $sortBy = $this->_request->getParam('sortBy');

        if($sortBy == "")
        {
        	$sortBy = "name";
        }

        $keys   = array();

        foreach ($atsListDb as $key => $ats)
        {
            $atsList[$key]['id']       = $ats->id;
            $atsList[$key]['name']     = $ats->name;
            $atsList[$key]['address']  = $ats->address;
            $atsList[$key]['status']   = $ats->status == 1 ? 'ON' : 'OFF';;
            $atsList[$key]['notes']    = $ats->notes;
            $atsList[$key]['hub_name'] = $phoneHubs[$ats->phone_hub_id];
			$atsList[$key]['expanded'] = $ats->expanded;

            $numbersString = '';
            $connector = '';

            foreach ($atsNumbers as $atsNumber)
            {
                if ($atsNumber->ats_id == $ats->id)
                {
                    $numbersString .= "{$connector}{$atsNumber->start_number} - {$atsNumber->end_number}";
                    $connector = '<br/>';
                }
            }

            $atsList[$key]['numbers'] = $numbersString;

            $badNumbersString = '';
            $connector = '';

            foreach ($atsBadNumbers as $atsBadNumber)
            {
                if ($atsBadNumber->ats_id == $ats->id)
                {
                    $badNumbersString .= "{$connector}{$atsBadNumber->start_number} - {$atsBadNumber->end_number} ({$atsBadNumber->reason})";
                    $connector = '<br/>';
                }
            }

            $atsList[$key]['bad_numbers'] = $badNumbersString;

            if ($sortBy)
            {
                $keys[$key] = $atsList[$key][$sortBy];
            }
        }

        if ($sortBy)
        {
            $result = array_multisort($keys, SORT_ASC, $atsList);
        }

        $this->view->atsList = $atsList;
    }

    /**
     * Action for modify or add ats
     */
    public function modifyAction()
    {
        $atsListModel = new AtsList();
        $atsNumbersModel = new AtsNumbers();
        $atsBadNumbersModel = new AtsBadNumbers();

        $id = $this->_request->getParam('id');

		$this->view->expanded = $atsListModel->verifyAts($id);

        if ( $this->_request->isPost() )
        {
            $data = $this->_request->getPost();

            $numbers = $this->_request->getParam('numbers');
            $badNumbers = $this->_request->getParam('bad_numbers');
            unset($numbers['newCOUNTER']);
            unset($badNumbers['newCOUNTER']);

            $form = $this->createForm();
            $formValidateResult = $form->isValid($data);

            $atsNumbers = $atsNumbersModel->fetchAll()->toArray();
            $numbersValidateErrors = $this->validateNumbers($numbers, $atsNumbers, $id);

            $atsBadNumbers = $atsBadNumbersModel->fetchAll()->toArray();
            $badNumbersValidateErrors = $this->validateNumbers($badNumbers, $atsBadNumbers, $id);

            if ( $formValidateResult && count($numbersValidateErrors) == 0 && count($badNumbersValidateErrors) == 0 )
            {
                $id = $atsListModel->saveChanges($data, $id);
                $atsNumbersModel->deleteByAtsID($id);
                $atsNumbersModel->insertNumbers($numbers, $id);

                $atsBadNumbersModel->deleteByAtsID($id);
                $atsBadNumbersModel->insertNumbers($badNumbers, $id);

                $this->_redirect('/tech-ats');
            }
            else
            {
                $this->view->form = $form;
                $this->view->atsID = $id;
                $this->view->numbers = $numbers;
                $this->view->badNumbers = $badNumbers;
                $this->view->numbersErrors = $numbersValidateErrors;
                $this->view->badNumbersErrors = $badNumbersValidateErrors;
            }
        }
        else
        {
            if ($id)
            {
                $ats = $atsListModel->fetchRecordByID($id);
                $atsNumbers = $atsNumbersModel->fetchAllByAtsID($id);
                $atsBadNumbers = $atsBadNumbersModel->fetchAllByAtsID($id);
                $this->view->numbers = $atsNumbers;
                $this->view->badNumbers = $atsBadNumbers;
            }
            else
            {
                $ats = $atsListModel->createRow();
                $this->view->isNew = true;
            }

            $this->view->form = $this->createForm($ats);
            $this->view->atsID = $id;
        }
    }

    /**
     * Creates Zend_Form for modify action
     *
     * @param array $ats - Ats data to populate form with
     * @return Zend_Form
     */
    private function createForm($ats = null)
    {
        if ( ! $ats )
        {
            $atsListModel = new AtsList();
            $ats = $atsListModel->createRow();
        }

        $citiesModel = new Cities();
        $phoneHubListModel = new PhoneHubList();

        $values = $ats->toArray();
        $values['city_options'] = $citiesModel->getEnabledCitiesOptions();
        $values['phone_hub_options'] = $phoneHubListModel->getNotDeletedOptions();

        $form = new Form_Techats();
        $form->populate($values);

        return $form;
    }

    private function validateNumbers(&$numbers, $allNumbers, $atsID)
    {
        $errors = array();

        foreach ($numbers as $key => $number)
        {
            if ( ! $number['start_number'] && ! $number['end_number'] )
            {
                unset($numbers[$key]);
                continue;
            }

            if ($number['start_number'] > $number['end_number'])
            {
                $errors[] = "Начальный телефон должен быть меньше <br/>конечного в диапазоне {$number['start_number']} - {$number['end_number']}";
            }
        }

        $atsListModel = new AtsList();

        foreach ($allNumbers as $atsNumber)
        {
            if ($atsNumber['ats_id'] == $atsID)
            {
                continue;
            }

            foreach ($numbers as $number)
            {
                if (! ($number['end_number'] < $atsNumber['start_number'] || $atsNumber['end_number'] < $number['start_number']) )
                {
                    $ats = $atsListModel->fetchRow('id = ' . $atsNumber['ats_id']);
                    $errors[] = "Диапазон номеров {$number['start_number']} - {$number['end_number']} пересекается c <br/> {$atsNumber['start_number']} - {$atsNumber['end_number']} у {$ats['name']}";
                }
            }
        }

        return $errors;
    }

    /**
     * Action for delete ats
     */
    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
        $atsListModel = new AtsList();
        $atsListModel->markToDelete($id);

        $this->_redirect('/tech-ats');
    }

    public function getspeedupAction()
    {
		$ats_id = $this->_request->getPost('ats_id');
		$atsListModel  = new AtsList();
		$portTaskModel = new Porttasks();

		$dataClients = $atsListModel->atsClientsStream(trim($ats_id));

		for($i = 0; $i < count($dataClients); $i++)
		{
			if($dataClients[$i]['group_name'] == "reserved")
			{
				continue;
			}
			$portTaskModel->insertTask($dataClients[$i]);
		}

		$atsListModel->markExpended($ats_id);

    }



}
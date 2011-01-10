<?php
/**
 * Controller for card contracts pages
 *
 * @author marat
 */

require_once ('BaseController.php');
require_once ('ClientModel.php');
require_once ('ClientContract.php');
require_once ('CardOrdersDiscounts.php');
require_once ('AsrHelp.php');
require_once ('forms/CardContract.php');

class CardContractController extends BaseController
{
    public function indexAction()
    {
        if ($this->_request->isPost())
        {
            $param = $this->_request->getParam('param');
            $value = $this->_request->getParam('value');

            $model = new ClientModel();
            $this->view->contracts = $model->searchCardClient($param, $value);
        }
    }

    public function createAction()
    {
        $asrModel = new AsrHelp();
        $options = $asrModel->getAsrTypeOptions(array(AsrHelp::BANK_TYPE, AsrHelp::DIRECTOR_TYPE));
        $data['bank_options'] = $options[AsrHelp::BANK_TYPE];
        $data['boss_options'] = $options[AsrHelp::DIRECTOR_TYPE];

        $form = new Form_CardContract();
        $form->populate($data);
        $this->view->form = $form;

        if ($this->_request->isPost())
        {
            $data = $this->_request->getPost();

            $accounts = $this->_request->getPost('accounts');
            unset($accounts['COUNTER']);

            if ($form->isValid($data))
            {
                $model = new ClientModel();
                $rowData = $form->getValues();
                $rowData['client_type_id'] = 4;
				$rowData['currency']       = 'UZS';
                $clientID = $model->saveChanges($rowData);

                foreach ($accounts as $account)
                {
                    $model->addRschet($clientID, $account);
                }

                $auth = Zend_Auth::getInstance();
                $manager = $auth->getStorage()->read();

                $clientContractModel = new ClientContract();
                $contractRow = $clientContractModel->createRow();
                $contractRow->client_id  = $clientID;
                $contractRow->manager_id = $manager->id;
                $contractRow->save();

                $this->_redirect('/card-contract/index');
            }
        }
    }

    public function viewAction()
    {
        $clientModel = new ClientModel();
        $contractModel = new ClientContract();
        $asrModel = new AsrHelp();

        $clientID = $this->_request->getParam('id');
        $client = $clientModel->getClientByID($clientID);
        $options = $asrModel->getAsrTypeOptions(array(AsrHelp::BANK_TYPE, AsrHelp::DIRECTOR_TYPE));
        $client['bank_id'] = $options[AsrHelp::BANK_TYPE][$client['bank_id']];
        $client['boss_id'] = $options[AsrHelp::DIRECTOR_TYPE][$client['boss_id']];
        $this->view->client = $client;
        $this->view->accounts = $clientModel->rschetClient($clientID);
        $this->view->contracts = $contractModel->getContractsByClientID($clientID);
    }

    public function modifyAction()
    {
        $clientID = $this->_request->getParam('id');

        $clientModel = new ClientModel();
        $asrModel = new AsrHelp();

        $form = new Form_CardContract();
        $this->view->form = $form;

        $options = $asrModel->getAsrTypeOptions(array(AsrHelp::BANK_TYPE, AsrHelp::DIRECTOR_TYPE));
        $data['bank_options'] = $options[AsrHelp::BANK_TYPE];
        $data['boss_options'] = $options[AsrHelp::DIRECTOR_TYPE];

        if ($this->_request->isPost())
        {
            $form->populate($data);
            $postData = $this->_request->getPost();

            if ($form->isValid($postData))
            {
                $rowData = $form->getValues();
                $rowData['client_type_id'] = 4;
                $clientModel->saveChanges($rowData, $clientID);
                $this->_redirect('/card-contract/view/id/' . $clientID);
            }
        }
        else
        {
            $data += $clientModel->getClientByID($clientID);
            $form->populate($data);
        }
    }

    public function printAction()
    {
        $clientModel = new ClientModel();
        $contractModel = new ClientContract();
        $clientAccountModel = new Rschet();
        $cardOrdersDiscountsModel = new CardOrdersDiscounts();

        $clientID = $this->_request->getParam('id');

        $client = $clientModel->getClientByID($clientID);
        $clientAccounts =$clientAccountModel->getListByClientID($clientID);

        $comma = '';
        foreach ($clientAccounts as $account)
        {
        	$client['rschets'] = $comma . $account['schet'];
        	$comma = ', ';
        }

        $contracts = $contractModel->getContractsByClientID($clientID);
        $contract = $contracts[0];

        $rekvizits = Zend_Registry::get('rekvizits');

        $discounts = $cardOrdersDiscountsModel->getAllDiscounts();

        require_once ('CardContractPdf.php');
        $pdfHelper = new CardContractPdfHelper($rekvizits);
        $pdfHelper->setClientInfo($client);
        $pdfHelper->startPrint($contract['contract_number'], $contract['dateagree'], $discounts);
    }
}
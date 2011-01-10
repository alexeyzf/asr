<?
/**
 * Controller for client accounts pages
 *
 * @author marat
 */

require_once 'BaseController.php';

class ClientAccountController extends BaseController
{
	/**
	 * The default action - showes the search client page
	 */
    public function indexAction()
    {
            $type = $this->_request->getParam('type');
            $svalue = $this->_request->getParam('svalue');

            if($this->_request->isPost())
            {

                if($type == "by_inn")
                {
                        if ($svalue)
                        {
                                $this->view->inn = $svalue;
                                $clientModel = new ClientModel();
                                $clientAccountModel = new Rschet();
                                $client = $clientModel->getClientByInn($svalue);

                                if ($client)
                                {
                                        $this->view->client = $client;
                                        $this->view->clientAccounts = $clientAccountModel->getListByClientID($client->client_id);
                                }
                                else
                                {
                                        $this->view->message = 'Клиент с данным ИНН не найден';
                                }
                        }
                }
                elseif($type == "by_schet")
                {
                                $this->view->inn = $svalue;
                                $clientModel = new ClientModel();
                                $clientAccountModel = new Rschet();
                                $client = $clientModel->getClientByRschetTest($svalue);

                                if ($client)
                                {
                                        $this->view->clientID = $client;
                                }
                                else
                                {
                                        $this->view->message = 'Клиент с данным Р/С не найден';
                                }
                }
                elseif($type == "by_contract")
                {
                                $this->view->inn = $svalue;
                                $clientModel 		= new ClientModel();
                                $clientAccountModel = new Rschet();

                                $client = $clientModel->getClientByContract($svalue);

                                if ($client)
                                {
                                        $this->view->clientID = $client;
                                        $this->view->client = $client['client_id'];
                                        $this->view->clientAccounts = $clientAccountModel->getListByClientID($client['client_id']);
                                }
                                else
                                {
                                        $this->view->message = 'Клиент с данным Контрактом не найден';
                                }
                }
            }
    }

    public function saveAction()
    {
            $clientID = $this->_request->getParam('client_id');
            $accounts = $this->_request->getParam('accounts');

            $clientAccountModel = new Rschet();

            try
            {
                    $clientAccountModel->startTransaction();

                    foreach ($accounts as $id => $account)
                    {
                            $account = trim($account);

                            if ( ! $account )
                            {
                                    continue;
                            }

                            $clientAccountModel->saveAccount(intval($id), $clientID, $account);
                    }

                    $clientAccountModel->commitTransaction();
            }
            catch(Exception $ex)
            {
                    $clientAccountModel->rollbackTransaction();
                    print $ex;
                    exit;
            }

            $this->_redirect('/client-account/index');
    }

}
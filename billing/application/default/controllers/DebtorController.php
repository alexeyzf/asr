<?php
require_once ('BaseController.php');
require_once ('DebtorModel.php');
require_once ('ClientModel.php');

class DebtorController extends BaseController
{
    public function indexAction()
    {
        $modelDebtor = new DebtorModel();

        if($this->_request->isPost())
        {
            $postData = $this->_request->getPost();
            if ($postData['postback'])
            {
                $client = new ClientModel();
                $client->startTransaction();
                try
                {
                    foreach ($postData as $key => $value)
                    {
                        preg_match('/^note_(?P<client_id>\d+)$/', $key, $matches);
                        if ($matches && $value)
                        {
                            $id = $matches['client_id'];
                            $client->update(array('notes'=>$value), "client_id={$id}");
                        }
                    }
                }
                catch (Exception $ex)
                {
                    $client->rollbackTransaction();
                    var_dump($ex); exit;
                }
                $client->commitTransaction();
            }
            
            $clientType  = $postData['ctype'];
            $this->view->ctype = $clientType;
            $data = $modelDebtor->getDebtorList($clientType);
            $this->view->dataClients = DistrictDistributionHelper::distribute($data);
        }
    }

    public function showDisabledServiceAction()
    {
		if($this->_request->isPost())
		{
			$service = $this->_request->getPost('service');

			if($service)
			{
				$debtorModel = new DebtorModel();

				$this->view->data = $debtorModel->getServiceDebtors($service);
			}
		}
    }
}
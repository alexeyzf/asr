<?php
/**
 * Controller for card orders pages
 * @author marat
 */

require_once ('BaseController.php');
require_once ('CardOrders.php');
require_once ('CardOrdersDiscounts.php');
require_once ('ClientModel.php');
require_once ('forms/CardOrder.php');

class CardOrderController extends BaseController
{
    public function indexAction()
    {
        $clientID = $this->_request->getParam('client_id');
        $cardOrdersModel = new CardOrders();
        $orders = $cardOrdersModel->getClientOrders($clientID);
        $this->view->orders = $orders;
        $this->view->clientID = $clientID;

        $clientModel = new ClientModel();
        $this->view->client = $clientModel->getClientName($clientID);
    }

    public function createAction()
    {
        $clientID = $this->_request->getParam('client_id');


        $form = new Form_CardOrder();
        $this->view->form = $form;

        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();

            if ($form->isValid($postData))
            {
                $data = $form->getValues();
                $cardOrdersModel = new CardOrders();
                $cardOrdersDetailsModel = new CardOrdersDetails();



                $orderID = $cardOrdersModel->saveChanges($data);

                $cardUnits = array(3, 5, 10, 15, 20);
                $totalAmount = 0;

                foreach ($cardUnits as $faceValue)
                {
                    $count = $data["details{$faceValue}"];

                    if ( ! $count )
                    {
                        continue;
                    }

                    $cardOrdersDetailsModel->addDetail($orderID, $data['card_type'], $faceValue, $count);
                    $totalAmount += $faceValue * 1000 * $count;
                }

                $cardOrdersDiscountsModel = new CardOrdersDiscounts();
                $discount = $cardOrdersDiscountsModel->getDiscountByAmount($totalAmount);
                $cardOrdersModel->setDiscount($orderID, $discount);

                $this->_redirect("/card-order/index/client_id/{$clientID}");
            }
        }
        else
        {
            $clientModel = new ClientModel();
            $data['client_id'] = $clientID;
            $data['client'] = stripslashes($clientModel->getClientName($clientID));

            $cardOrdersModel = new CardOrders();

            $data['max_invoice']       = $cardOrdersModel->getMaxInvoiceNumber();
            $data['next_order_number'] = $cardOrdersModel->getNextOrderNumber($clientID);

            $form->populate($data);
        }
    }



    public function viewAction()
    {
        $orderID = $this->_request->getParam('id');
        $cardOrdersModel = new CardOrders();
        $cardOrdersDetailsModel = new CardOrdersDetails();
        $order = $cardOrdersModel->fetchRecordByID($orderID);

        $clientModel = new ClientModel();
        $this->view->client = $clientModel->getClientName($order['client_id']);

        $this->view->order = $order;
        $this->view->details = $cardOrdersDetailsModel->getOrderDetails($orderID);
    }

    public function postAction()
    {
        $cardOrdersModel = new CardOrders();
        $cardOrdersDetailsModel = new CardOrdersDetails();
        $clientModel = new ClientModel();

        $orderID = $this->_request->getParam('id');
        $order = $cardOrdersModel->fetchRecordByID($orderID);
        $amount = $cardOrdersDetailsModel->getOrderAmount($orderID);
		$totalAmount = $amount * (100 - $order['discount']) / 100;

        $client = $clientModel->getClientByID($order['client_id']);

        if ( $client['ballance'] < $totalAmount )
        {
            $clientName = stripslashes($client['client_name']);
            $this->view->error = "Не хватает денег на счету клиента \"{$clientName}\"";
        }
        elseif ($order['is_posted'])
        {
            $this->view->error = "Заказ уже был проведен";
        }
        else
        {
            $clientModel->decreaseBallance($order['client_id'], $totalAmount);
            $cardOrdersModel->setPosted($orderID);
            $this->_redirect("/card-order/index/client_id/{$order['client_id']}");
        }
    }

    public function deleteAction()
    {
        $orderID = $this->_request->getParam('id');
        $cardOrdersDetailsModel = new CardOrdersDetails();
        $cardOrdersModel = new CardOrders();
        $clientModel = new ClientModel();
        $order = $cardOrdersModel->fetchRecordByID($orderID);

        if ($order['is_posted'])
        {
            $amount = $cardOrdersDetailsModel->getOrderAmount($orderID);
            $totalAmount = $amount * (100 - $order['discount']) / 100;
            $clientModel->increaseBallance($order['client_id'], $totalAmount);
        }

        $cardOrdersModel->markToDelete($orderID);

        $this->_redirect("/card-order/index/client_id/{$order['client_id']}");
    }

    public function printOrderAction()
    {
        $cardOrdersDetailsModel = new CardOrdersDetails();
        $cardOrdersModel = new CardOrders();
        $clientModel = new ClientModel();
        $contractModel = new ClientContract();

        $orderID = $this->_request->getParam('id');
        $order = $cardOrdersModel->fetchRecordByID($orderID);
        $details = $cardOrdersDetailsModel->getOrderDetails($orderID);
        $client = $clientModel->getClientByID($order['client_id']);
        $rekvizits = Zend_Registry::get('rekvizits');
        $contracts = $contractModel->getContractsByClientID($order['client_id']);
        $contract = $contracts[0];

        require_once ('CardOrderPdf.php');
        $helper = new CardOrderPdfHelper($rekvizits);
        $helper->setClientInfo($client);

        $helper->startPrint(
            $contract['contract_number'], $contract['dateagree'],
            $order['number'], $order['discount'],
            $details);
    }

    public function printInvoiceAction()
    {
        $cardOrdersDetailsModel = new CardOrdersDetails();
        $cardOrdersModel = new CardOrders();
        $clientModel = new ClientModel();
        $contractModel = new ClientContract();
		$clientAccountModel = new Rschet();

        $orderID   = $this->_request->getParam('id');
        $order     = $cardOrdersModel->fetchRecordByID($orderID);

        $details   = $cardOrdersDetailsModel->getOrderDetails($orderID);
        $client    = $clientModel->getClientByID($order['client_id']);

    	$clientAccounts =$clientAccountModel->getListByClientID($order['client_id']);

        $comma = '';
        foreach ($clientAccounts as $account)
        {
        	$client['rschets'] = $comma . $account['schet'];
        	$comma = ', ';
        }

        $rekvizits = Zend_Registry::get('rekvizits');
        $contracts = $contractModel->getContractsByClientID($order['client_id']);

        $contract  = $contracts[0];

        require_once ('CardInvoicePdf.php');
        $helper = new CardInvoicePdfHelper($rekvizits);
        $helper->setClientInfo($client);

        $helper->startPrint(
            $contract['contract_number'],
            $contract['dateagree'],
            $order['invoice_number'],
            $order['discount'],
            $details,
            $order['order_date']);
    }
}
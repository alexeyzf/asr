<?php
/**
 * Default controller for InvoiceController for Бух department
 *
 */

require_once ('BaseController.php');
require_once ('forms/invoice.php');
require_once ('Zend/tcpdf/tcpdf.php');
require_once ('models/InvoiceModel.php');
require_once ('models/ClientModel.php');
require_once ('models/InvoiceAvoider.php');
require_once ('SchetFactura.php');
require_once ('SchetPdf.php');
require_once ('CurrencyToWord.php');

class InvoiceController extends BaseController
{

    public function indexAction()
    {
		$form = new Form_Invoice();
		$form->populate();
		$this->view->form = $form;
    }

    private function sendContent($dir, $fName)
    {
    	header('Content-Description: File Transfer');
    	header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
		header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream', false);
        header('Content-Type: application/download', false);
        header('Content-Type: application/pdf', false);
        header('Content-Disposition: attachment; filename="'.basename($fName).'";');
        header('Content-Transfer-Encoding: binary');
        header("Content-length: " . filesize($dir . $fName));
        readfile($dir . $fName);
		exit;
    }

    public function createAction()
    {
    	$model 			  = new InvoiceModel();
    	$ballanceLogModel = new BallanceLogs();
    	$clientModel 	  = new ClientModel();

    	$month = $this->_request->getPost('month_need');
    	$year  = $this->_request->getPost('year_need');
    	$cid   = $this->_request->getParam('client_id');

    	$dir = realpath(BILLING_PATH . '/invoices/') . '/';
    	$fName = "invoice_{$month}_{$year}.pdf";

    	if ( ! $cid && is_file($dir . $fName))
    	{
    		$this->sendContent($dir, $fName);
    	}

		$dataArr = $model->getInvoice($month, $year, $cid, true);

		$beforeDate = date('Y-m-d', strtotime("{$year}-{$month}-01"));
		$afterDate = date('Y-m-d', strtotime("+1 month {$year}-{$month}-01"));
		$ballanceBeforeData = $ballanceLogModel->getBallanceLogs($beforeDate);
		$ballanceAfterData = $ballanceLogModel->getBallanceLogs($afterDate);

		$ballanceData = array();

		foreach ($ballanceBeforeData as $clientID => $amount)
		{
			$ballanceData[$clientID]['before']['date'] = $beforeDate;
			$ballanceData[$clientID]['before']['amount'] = $amount;
		}

		foreach ($ballanceAfterData as $clientID => $amount)
		{
			$ballanceData[$clientID]['after']['date'] = $afterDate;
			$ballanceData[$clientID]['after']['amount'] = $amount;
		}

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetFont('arial', '', 7);
		$pdf->AddPage();
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		$schetDate = date('Y-m-01', strtotime("+2 month {$year}-{$month}-01"));
		$cid = 0;

		$rekvizits = Zend_Registry::get('rekvizits');

		for($i = 0; $i < count($dataArr); $i++)
		{
			$rowsCount = $model->getInvoiceDetailsCount($dataArr[$i]['invoice_id']);

			if($rowsCount == 0)
			{
				continue;
			}

			$dataArr[$i]['bank']     = $clientModel->getBankName($dataArr[$i]['bank_id']);
			$dataArr[$i]['city']     = $clientModel->getCityName($dataArr[$i]['country_id']);
			$rschets = $clientModel->rschetClient($dataArr[$i]['client_id']);

			$isTreasury = false;
			foreach($rschets as $rschet)
			{
				if ($rschet == $rekvizits->treasury_account)
				{
					$isTreasury = true;
					break;
				}
			}

			$dataArr[$i]['rschets']  = $rschets[0];
			$dataArr[$i]['clientSattlementAccount']  = $rschets[0];

			$cid = $dataArr['client_id']; // TEMPORARY
			if($cid == $dataArr[$i]['client_id'])
			{
				continue;
			}


			for($copy = 0; $copy < 2; $copy++)
			{
				// Print accounting blank_
				$content .= SchetFactura::getPdfSchetFakturaHeader($dataArr[$i]);

				$data_for_client = $model->getServiceForSchet($dataArr[$i]['invoice_id']);

				if ( count($data_for_client) == 0 )
				{
					continue;
				}

				$content .= SchetFactura::getBodyPdf( $data_for_client, $ballanceData[$dataArr[$i]['client_id']], $isTreasury, $dataArr[$i]['lastdate']);
				$content .= SchetFactura::getFooterPdf($data_for_client, $dataArr[$i]['lastdate'], false, $dataArr[$i]['client_id']);
				//print $htmlcontent;
				$pdf->writeHTML($content, true, 0, true, 0);

				$content = "";

				// Тут генерим счет BEGIN
				if(count($data_for_client) <= 3)
				{
					$schetDetails = $model->getSchetData($dataArr[$i]['client_id'], $schetDate);
					$schetPdfHelper = new SchetPdfHelper(Zend_Registry::get('rekvizits'));

					$schetPdfHelper->setClientInfo($dataArr[$i], 1);
					$schetContent = $schetPdfHelper->getHtml($dataArr[$i]['contract_number'],
															 $dataArr[$i]['contract_date'],
															 $dataArr[$i]['schetnum'],
															 $schetDetails[0]['lastdate'],
															 $schetDetails,
															 $dataArr[$i]['ballance'],
                                                             $dataArr[$i]['client_id']
															 );
					$pdf->writeHtml($schetContent, true, 0, true, 0);
					$pdf->AddPage();

					$cid = $dataArr[$i]['client_id'];
				}
				else
				{
					$pdf->AddPage();
				}
			}

			// Тут генерим счет
			if(count($data_for_client) > 3)
			{
				$schetDetails = $model->getSchetData($dataArr[$i]['client_id'], $schetDate);
				$schetPdfHelper = new SchetPdfHelper($rekvizits);

				$schetPdfHelper->setClientInfo($dataArr[$i], 1);
				$schetContent = $schetPdfHelper->getHtml($dataArr[$i]['contract_number'],
														 $dataArr[$i]['contract_date'],
														 $dataArr[$i]['schetnum'],
														 $schetDetails[0]['lastdate'],
														 $schetDetails,
														 $dataArr[$i]['ballance'],
                                                         $dataArr[$i]['client_id']
														 );
				$pdf->writeHtml($schetContent, true, 0, true, 0);
				$pdf->AddPage();

				$cid = $dataArr[$i]['client_id'];
			}

		}

		//
        $pdf->Output($dir . $fName, 'F');
        $this->sendContent($dir, $fName);
    }

    function targetsSelectionAction()
    {
        $avoiders = new InvoiceAvoiderModel();

        if ($this->_request->isGet())
        {
            $this->view->data = $avoiders->getInvoicesAvoiders();
        }
        else
        {
            $checked = $this->_request->getPost('avoiders');
            $avoiders->updateState($checked);
            $this->_redirect('/invoice/targets-selection');
        }
    }
}
<?php
/**
 * Controller for revision act pages
 */

require_once ('BaseController.php');

class RevisionActController extends BaseController 
{
	public function indexAction()
	{
		if ( $this->_request->isPost() )
		{
			$startYear = $this->_request->getParam('year');
			$startMonth = 12;
			
			$ballanceLogModel = new BallanceLogs();
			$transactionModel = new FinanceModel();
			$invoiceModel = new InvoiceModel();
			$clientModel = new ClientModel();
			$revisionActBallanceModel = new RevisionActBallance();
			
			$corpClientIDs = $clientModel->getCorpClientsIDs();
			
			$pdf = $this->preparePdf();
			
			$isAddPage = false;
			
			foreach ($corpClientIDs as $clientID)
			{
				$clientInfo = $clientModel->getClientInfo($clientID);
	    		if (is_array($clientInfo))
	    		{
	    			$clientInfo = $clientInfo[0];
	    		}
	
	        	$rschets = $clientModel->rschetClient($clientID);
	
		        $connector = '';
	        	foreach ($rschets as $rschet)
	        	{
	           		$rschetText .= $connector . $rschet['schet'];
		      		$connector = ',';
	        	}
	
	        	$clientInfo['rschets'] = $rschetText;
				
				$ballance = $ballanceLogModel->getBallanceOnDate("01.01.{$startYear}", $clientID);
				$transactions = $transactionModel->getSortedTransactions($clientID, $startYear, $startMonth);
				$invoices = $invoiceModel->getClientAmounts($clientID, $startYear, $startMonth);
	
				$revisionActPdfHelper = new RevisionActPdfHelper(Zend_Registry::get('rekvizits'));
				$revisionActPdfHelper->setClientInfo($clientInfo);
				$html = $revisionActPdfHelper->getHtml($startYear, $ballance[0], $ballance[1], $transactions, $invoices);
				
				$revisionActBallanceModel->logBallance($clientID, $startYear, 
						$revisionActPdfHelper->getClientEndBallanceUsd(), 
						$revisionActPdfHelper->getClientEndBallanceUzs());
				
				if ($isAddPage)
				{
					$pdf->AddPage();
				}
				else
				{
					$isAddPage = true;
				}
				
				$pdf->writeHtml($html);
				$pdf->AddPage();
				$pdf->writeHtml($html);
			}
			
			$pdf->Output("revision-act.pdf", 'I');
		}
	}
	
	private function preparePdf()
	{
		require_once ('Zend/tcpdf/tcpdf.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->AddFont('arial');
        $pdf->AddFont('arialbd', 'b');

        $pdf->AddPage();
        //$pdf->SetCreator(PDF_CREATOR);
        //$pdf->SetAuthor('SharqStream');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // делаем отступы
        $pdf->SetMargins(15, 10, 15, 0);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Ставим авто пейдж бреак
        $pdf->SetAutoPageBreak(TRUE, 15);

        $pdf->SetFont('arial', '', 7);
        
        return $pdf;
	}
}

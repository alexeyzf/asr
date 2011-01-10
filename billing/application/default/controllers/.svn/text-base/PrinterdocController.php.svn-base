<?php
require_once ('ClientModel.php');
require_once ('SearchServicePdf.php');
require_once ('EngineerCalls.php');
require_once ('AnketaHelper.php');
require_once ('AdditionalHelper.php');

class PrinterdocController extends Zend_Controller_Action {

    public function indexAction()
    {

    }

    public function printcontractAction()
    {
        // Вытаскиваем наши реквизиты (файл config.ini)
        $rekvizits = Zend_Registry :: get('rekvizits');

        $client_id      = $this->_request->getParam('client_id');
        $contract_id    = $this->_request->getParam('contract_id');
        $client_type_id = $this->_request->getParam('client_type_id');

        $clientModel = new ClientModel();
        $serviceInfoModel = new SearchServicePdf();
        $dataClient = $clientModel->getClientInfo($client_id);

        $rschets = $clientModel->rschetClient($client_id);
        $connector = '';

        foreach ($rschets as $rschet)
        {
            $rschetText .= $connector . $rschet['schet'];
            $connector = ',';
        }

        $allServiceArray = array (
            0 => 'adsl',
            1 => 'collacation',
            2 => 'hosting',
            3 => 'ngn',
            4 => 'tasix',
            5 => 'wifi',
            6 => 'vpn',
            7 => 'isdn',
            8 => 'tradtel'
        );

        $arrService = array ();

		for ($i = 0; $i < count($allServiceArray); $i++)
		{
			array_push($arrService, $serviceInfoModel->startSearchService($client_id, $allServiceArray[$i], $client_type_id));
			if (!$arrService[$i])
			{
				unset ($arrService[$i]);
			}
		}

		foreach ($arrService as $key => $value)
		{
			$arr[] = $value;
		}

		// Получили чистый массив

        require_once ('ContractPdf.php');
        $contractPdfHelper = new ContractPdfHelper($rekvizits);
        $contractPdfHelper->setClientInfo($dataClient[0]);
        $contractPdfHelper->startPrint($client_type_id, $rschetText, $arr);
    }

    public function printServiceContractAction()
    {
    	$serviceTable = $this->_request->getParam('table');
    	$pointID = $this->_request->getParam('point_id');

    	$serviceInfoModel = new SearchServicePdf();
    	$info = $serviceInfoModel->getServiceInfo($serviceTable, $pointID);

    	$clientContractModel = new ClientContract();
    	$contract = $clientContractModel->getLastContract($info['client_id']);

    	$pdfHelper = new ContractPinPdfHelper(Zend_Registry::get('rekvizits'));
    	$pdfHelper->start($contract['contract_number'], $contract['dateagree'], $info);
    }

    public function selectRschet($client_id)
    {
        /*
        * Метод вытаскивает все записи по client_id из таблицы rschet
        */

        $dataModel = new ClientModel(); // Модель откуда мона узнать все о клиенте
        $scheta    = $dataModel->rschetClient($client_id);
        return $scheta;
    }

    public function questionnaireAction()
    {

        $callsModel = new EngineerCalls();
        $dataModel = new ClientModel();

        $client_id = $this->_request->getParam('client_id');
        $point_id = $this->_request->getParam('point_id');

        $data = $callsModel->getCallsList($point_id);
        $rschet = $dataModel->rschetClient($client_id);

        // GET PRINT DATA IN PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('arialbd', '', 10);
        $pdf->AddPage();

        // MAIN HEAD
        $pdf->writeHTML('АНКЕТА', true, 0, true, 0, 'C');
        $pdf->writeHTML('для подключения клиента к сети ООО "Sharq Telekom"<br/><br/>', true, 0, true, 0, 'C');

        // TITLE 1
        $pdf->writeHTML('I. Общая часть (Служба маркетинга)', true, 0, true, 0);
        $pdf->SetFont('arial', '', 10);
        $pdf->writeHTML(AnketaHelper :: firstBlock($data, $rschet), true, 0, true, 0);

        // TITLE 2
        $pdf->SetFont('arialbd', '', 10);
        $pdf->writeHTML('II. Техническая часть (Служба технического обеспечения)', true, 0, true, 0);
        $pdf->SetFont('arial', '', 10);
        $pdf->writeHTML(AnketaHelper :: secondBlock($data), true, 0, true, 0);

        // TITLE 3
        $pdf->SetFont('arialbd', '', 10);
        $pdf->writeHTML('III. Административная часть (Служба технического обеспечения)', true, 0, true, 0);
        $pdf->SetFont('arial', '', 10);
        $pdf->writeHTML(AnketaHelper :: thirdBlock($data), true, 0, true, 0);

        // EXIT WITH PRINT pDF
        $pdf->Output('anketa.pdf', 'I');
        exit ();
    }

    public function massPressLetterAction()
    {
    	if($this->_request->isPost())
    	{
    		$data = $this->_request->getPost();

			$clearArr = array();

			foreach($data['points'] as $key => $value)
			{
				if($value['is_changed'] == "1")
				{
					array_push($clearArr, $key);
				}
			}

			$documentModel = new DocumentModel();
			$printDocument = new PdfHelperDocumentUncross();

	        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	        $pdf->AddFont('arial');
	        $pdf->AddFont('arialbd', 'b');

	        $pdf->AddPage();
	        $pdf->SetCreator(PDF_CREATOR);
	        $pdf->SetAuthor('SharqStream');

			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);

	        // делаем отступы
	        $pdf->SetMargins(15, 10, 15, 0);
	        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	        // Ставим авто пейдж бреак
	        $pdf->SetAutoPageBreak(TRUE, 15);

	        $pdf->SetFont('arial', '', 11);

			foreach($clearArr as $item)
			{
				$result = $documentModel->getData($item);

				$pointID        = $item;
	            $atsID          = $result[0]['ats_id'];
	            $letterKind     = 2;
	            $letterType     = 1;
	            $letterSentWay  = 1;
	            $letterSentDate = 1;

	            $letterModel = new LettersToAts();
	            $letterID    = $letterModel->createLetter($pointID, $atsID, $letterType, $letterKind, $letterSentWay, $letterSentDate);

	            $clientModel = new ClientModel();
	            $pointInfo   = $clientModel->getInfo($pointID);

	            $pointInfo['letter_to_ats_id'] = $letterID;
	            TechHistoryHelper::addLogFromPoint($pointInfo, TechHistoryHelper::LETTER_FORMED);

	            $rekvizits = Zend_Registry::get('rekvizits');
	            $letter = $letterModel->getLetterByID($letterID);

	            $phoneHubModel = new PhoneHubList();
	            $phoneHubData = $phoneHubModel->fetchRecordByID($pointInfo['phone_hub_id'])->toArray();

				if ($letterKind == LettersToAts::LETTER_KIND_UNCROSS)
	            {
	            	$printDocument->replaceData($result, $pdf, $letter['number']);
	            }
			}
				$pdf->Output("popup.pdf", 'I');
				exit();
    	}
    }

}
?>

<?php
/**
 * Helper for generate pdf for additional agree
 *
 * @author marat
 */

require_once('PdfHelper.php');
require_once('ServicePdf.php');
require_once('ClientContract.php');

class AdditionalAgreePdfHelper extends PdfHelper
{
    public function __construct($companyRekvizits)
    {
        parent::__construct($companyRekvizits);
    }

    public function startPrint($addAgree, $serviceData, $type_agree = null)
    {
        $this->_pdf->SetTitle('Дополнительное соглашение');

        if($type_agree == "2")
        {
                $addAgree['contract_number'] = $this->getDopAgreesNumber($serviceData['client_id']);
        }

        $addAgreeDate = new Zend_Date($addAgree['dateagree']);
        $addAgreeDate = $addAgreeDate->toString('d MMMM Y г.');

        $template = $this->getTemplate('additionalAgree');
        $addAgreeNumber = $addAgree['contract_number'] ? $addAgree['contract_number'] : 1;

        $template = str_replace('{$number}', $addAgreeNumber, $template);
        $template = str_replace('{$clientBank}', $serviceData['bank_name'], $template);

        $template = str_replace('{$sign_name}', $serviceData['sign_name'], $template);
        $template = str_replace('{$clientPhone}', $serviceData['phone'], $template);

        $template = str_replace('{$currentDate}', $addAgreeDate, $template);
        $template = $this->replaceCompanyRekvizits($template);
        $template = $this->replaceClientRekvizits($template, $serviceData['startdate']);
        $template = str_replace('{$pointID}', $serviceData['point_id'], $template);
        $template = $this->clear($template);
        $this->_pdf->writeHTML($template);

        $this->_pdf->AddPage();

        $appendixTemplate = $this->getTemplate('additionalAgreeAppendix');
        $appendixTemplate = str_replace('{$number}', $addAgree['contract_number'], $appendixTemplate);
        $appendixTemplate = str_replace('{$start_date_pr}', date('Y/m/d', strtotime($serviceData['startdate'])), $appendixTemplate);
        $appendixTemplate = str_replace('{$currentDate}', $addAgreeDate, $appendixTemplate);
        $appendixTemplate = str_replace('{$address}', $serviceData['connect_address'], $appendixTemplate);

        $appendixTemplate = str_replace('{$post_sign_name}', $serviceData['post_sign_name'], $appendixTemplate);
        $appendixTemplate = str_replace('{$sign_name}', $serviceData['sign_name'], $appendixTemplate);
        $appendixTemplate = str_replace('{$manager}',  $this->_clientInfo['manager'], $appendixTemplate);

        $appendixTemplate = $this->replaceCompanyRekvizits($appendixTemplate);
        $appendixTemplate = $this->replaceClientRekvizits($appendixTemplate);
        $appendixTemplate = $this->replaceServiceInfo($appendixTemplate, $serviceData);
        $appendixTemplate = $this->clear($appendixTemplate);
        $this->_pdf->writeHTML($appendixTemplate);

        $this->_pdf->SetFont('arial');

        $serviceMethod = $serviceData['tablelink'] . 'Additional';
        $this->_pdf->writeHTML(AdditionalHelper :: $serviceMethod ($serviceData), true, 0, true, 0);

        $servicePdfHelper = new ServicePdfHelper($this->_companyRekvizits);
        $servicePdfHelper->setClientInfo($this->_clientInfo);
        $footer = $servicePdfHelper->getServiceFooter();
        $this->_pdf->writeHTML($footer);

        $this->_pdf->AddPage();
        $protocolText = $this->getTemplate('protocolAgree');
        $protocolText = str_replace('{$client_name}', $this->_clientInfo['client_name'], $protocolText);

        $protocolText = str_replace('{$servicetype_name}', $serviceData['servicetype_name'], $protocolText);
        $protocolText = str_replace('{$contract_number}', $this->_clientInfo['contract_number'], $protocolText);
        $protocolText = str_replace('{$start_date_pr}', date('Y/m/d', strtotime($serviceData['startdate'])), $protocolText);

        $dateAgree = new Zend_Date($this->_clientInfo['dateagree']);
        $dateAgree = $dateAgree->toString('d MMMM Y г.');
        $protocolText = str_replace('{$dateagree}',$dateAgree, $protocolText);
        $protocolText = str_replace('{$time}', $addAgreeDate, $protocolText);
        $this->_pdf->writeHTML($protocolText);

        $this->_pdf->writeHTML($servicePdfHelper->getServiceProtocol($serviceData['tablelink'], $serviceData));

        $protocolTextFooter = $this->getTemplate('protocolAgreeFooter');
        $endDate = new Zend_Date($this->_clientInfo['enddate']);
        $endDate = $endDate->toString('d MMMM Y г.');
        $protocolTextFooter = str_replace('{$enddate}', $endDate, $protocolTextFooter);
        $protocolTextFooter = str_replace('{$enddate_year}', date('Y/m/d', strtotime($serviceData['startdate'])), $protocolTextFooter);
        $protocolTextFooter = str_replace('{$client_name}', $this->_clientInfo['client_name'], $protocolTextFooter);
        $protocolTextFooter = str_replace('{$start_date_pr}', date('Y/m/d', strtotime($serviceData['startdate'])), $protocolTextFooter);

        $protocolTextFooter = str_replace('{$sign_name}', $serviceData['sign_name'], $protocolTextFooter);
        $protocolTextFooter = str_replace('{$post_sign_name}', $serviceData['post_sign_name'], $protocolTextFooter);
        $protocolTextFooter = str_replace('{$manager}',  $this->_clientInfo['manager'], $protocolTextFooter);
        $this->_pdf->writeHTML($protocolTextFooter);

        $this->sentResponce('addagree');
    }

    private function replaceServiceInfo($template, $serviceInfo)
    {
        $template = str_replace('{$serviceName}', $serviceInfo['servicetype_name'], $template);
        $template = str_replace('{$comment}', $serviceInfo['comment'], $template);

        foreach ($serviceInfo as $itemName => $value)
        {
            if ($itemName == 'foreign_access')
            {
                if ($value)
                {
                    $value = 'Есть';
                }
                else
                {
                    $value = 'Нет';
                }
            }
            elseif ($itemName == 'limit')
            {
                if ($serviceInfo['unlimit'])
                {
                    $value = 'нелимитированный';
                }
                else
                {
                    $value .= ' МБ';
                }
            }

            $template = str_replace('{$' . $itemName . '}', $value, $template);
        }

        return $template;
    }

    public function getDateWrite($date, $plus = null)
    {
		 if($plus == "1")
		 {
        	$t = new Zend_Date($date);
        	$t = $t->toArray();

			$t['year'] = intval(date($t['year'], strtotime('+1 year'))) + 1;

			$t = $t['year']."-". $t['month']."-". $t['day'];
			$addAgreeDate = new Zend_Date($t);
			return  $addAgreeDate->toString('d MMMM Y г.');
		 }
		 else
		 {
		 	$addAgreeDate = new Zend_Date($date);
         	return  $addAgreeDate->toString('d MMMM Y г.');
		 }
    }

    public function getDopAgreesNumber($client_id)
    {
		$model = new ClientContract();
		$data = $model->getLastDopAgrees($client_id);
		return $data;
    }

}
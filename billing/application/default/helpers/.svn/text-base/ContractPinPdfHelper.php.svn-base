<?php
require_once('PdfHelper.php');

class ContractPinPdfHelper extends  PdfHelper
{
	function __construct($companyRekvizits)
    {
        parent::__construct($companyRekvizits);
    }
    
    public function start($contractNumber, $contractDate, $info)
    {
    	$this->_pdf->SetFontSize(8);
    	$this->_pdf->SetTitle("Договор № {$contractNumber} клиента {$this->_clientInfo['client_name']}");
    	$content = $this->getTemplate('contractPin');
    	
    	if ($info['client_type_id'] == 0)
    	{
    		$content = str_replace('{$juridical}', $this->getJuridicalRekvizits(), $content);
    	}
    	else
    	{
    		$content = str_replace('{$juridical}', $this->getIndividualRekvizits(), $content);
    	}
    	
    	$content = str_replace('{$town}', $info['town'], $content);
    	
    	$dateAgree = new Zend_Date($contractDate);
        $dateAgree = $dateAgree->toString('"d" MMMM Y года');
        $content = str_replace('{$contractDate}', $dateAgree, $content);
        $content = str_replace('{$contractNumber}', $contractNumber, $content);
    	
        $content = $this->replaceCompanyRekvizits($content);
        $this->setClientInfo($info);
        $content = $this->replaceClientRekvizits($content);
        
        $content = str_replace('{$pointLogin}', $info['u_login'], $content);
        $content = str_replace('{$pointPassword}', $info['u_passwd'], $content);
        $content = str_replace('{$pin}', $info['login'], $content);
        
        $this->_pdf->writeHTML($content);
        $this->sentResponce("contract_pin_{$contractNumber}");
    }
    
    private function getJuridicalRekvizits()
    {
    	return '
    		<tr>
		        <td>
		            Адрес юридический: {$clientAddress}
		        </td>
		        <td>
		            Адрес юридический: {$companyAddress}
		        </td>
		    </tr>
		    <tr>
		        <td>
		            Адрес физический: {$clientPhysicalAddress}
		        </td>
		        <td>
		            Адрес физический: {$companyPhysicalAddress}
		        </td>
		    </tr>
		    <tr>
		        <td>
		            Адрес почтовый: {$clientZipAddress}
		        </td>
		        <td>
		            Адрес почтовый: {$companyZipAddress}
		        </td>
		    </tr>
		    <tr>
		        <td>
		            Тел: {$clientPhone}, факс: {$clientFax}
		        </td>
		        <td>
		            Тел: {$companyPhone}, факс: {$companyFax}
		        </td>
		    </tr>
    		<tr>
		        <td>
		            Р/c {$clientSattlementAccount}<br />
		            в {$clientBank}<br />
		            МФО {$clientMfo}<br />
		            ИНН {$clientINN}<br />
		            ОКОНХ {$clientOkonx}
		        </td>
		        <td>
		            Р/с {$companySattlementAccount}<br />
		            в {$companyBank}<br />
		            МФО {$companyMfo}<br />
		            ИНН {$companyINN}<br />
		            ОКОНХ {$companyOkonx}
		        </td>
		    </tr>
    	';
    }
    
    private function getIndividualRekvizits()
    {
    	return '
    		<tr>
    			<td>
    				Серия, номер пасспорта: {$clientPassport}
    			<td>
    			<td>
		            Адрес юридический: {$companyAddress}
		        </td>
    		</tr>
    		<tr>
		        <td>
		            Адрес: {$clientPhysicalAddress}
		        </td>
		        <td>
		            Адрес физический: {$companyPhysicalAddress}
		        </td>
		    </tr>
		    <tr>
		        <td>
		            Тел: {$clientPhone}, факс: {$clientFax}
		        </td>
		        <td>
		            Тел: {$companyPhone}, факс: {$companyFax}
		        </td>
		    </tr>
    	';
    }
}
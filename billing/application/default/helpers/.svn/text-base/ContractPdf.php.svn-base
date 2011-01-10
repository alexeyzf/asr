<?php
/**
 * Helper for generate pdf for client contract
 *
 * @author alex
 */
require_once ('PdfHelper.php');
require_once ('ServicePdf.php');
require_once ('ModemModel.php');
require_once ('NgnModel.php');

class ContractPdfHelper extends PdfHelper
{
    function __construct($companyRekvizits)
    {
        parent::__construct($companyRekvizits);
    }

    private function writeFirtTemplate($template, $rschetText)
    {
    	$currentDate = new Zend_Date($this->_clientInfo['dateagree']);
        $this->_time = $currentDate->toString('d MMMM Y г.');

        $template = str_replace('{$contract_number}', $this->_clientInfo['contract_number'], $template);
        $template = str_replace('{$town}', $this->_clientInfo['town'], $template);
        $template = str_replace('{$dateagree}', $this->_time, $template);
        $template = str_replace('{$client_name}', $this->_clientInfo['client_name'], $template);
        $template = str_replace('{$gen_name}', $this->_companyRekvizits->gen_name_s, $template);
        $template = str_replace('{$contact_name}', $this->_clientInfo['contact_name'], $template);
        $template = str_replace('{$legaladdress}', $this->_clientInfo['legaladdress'], $template);
        $template = str_replace('{$address}', $this->_clientInfo['address'], $template);
        $template = str_replace('{$phone}', $this->_clientInfo['phone'], $template);
        $template = str_replace('{$fax}', $this->_clientInfo['fax'], $template);
        $template = str_replace('{$rschet}', $rschetText, $template);
        $template = str_replace('{$bankname}', $this->_clientInfo['bank_name'], $template);
        $template = str_replace('{$mfo}', $this->_clientInfo['mfo'], $template);
        $template = str_replace('{$inn}', $this->_clientInfo['inn'], $template);
        $template = str_replace('{$okonx}', $this->_clientInfo['okonx'], $template);
        $template = str_replace('{$sign_name}', $this->_clientInfo['sign_name'], $template);

        $template = str_replace('{$post_sign_name}', $this->_clientInfo['post_sign_name'], $template);
        $template = str_replace('{$manager_name}', $this->_companyRekvizits->gen_name, $template);

        $this->_pdf->writeHTML($template, true, 0, true, 0);
    }

    public function startPrint($clientType, $rschetText, $services)
    {
		if ($clientType == 1)
		{
			$this->streamPrint($rschetText, $services);
		}
		else
		{
			$this->corpPrint($rschetText, $services);
		}
    }

    public function corpPrint($rschetText, $services)
    {
    	$this->_pdf->SetTitle('Договор № ' . $this->_clientInfo['contract_number']);
        $modemModel = new ModemModel();

        $ngnModel = new NgnModel();

		// TODO

		// Addon for EC clients
		if($services[0][0]['currency'] == "UZS")
		{
			$template = $this->getTemplate('contractEC');
		}
		else
		{
			$template = $this->getTemplate('contract');
		}
		// End Addon

		$this->writeFirtTemplate($template, $rschetText);

        $this->_pdf->AddPage();


        // Приложение 2. Таблицы
        $iterator = 1;

        for ($i = 0; $i < count($services); $i++)
        {
            for ($z = 0; $z < count($services[$i]); $z++)
            {
                $template2 = $this->getTemplate('contractAppendix');

                if ($services[$i][$z]['start_speed'])
                {
                    $speed = substr($services[$i][$z]['start_speed'], 0, strpos($services[$i][$z]['start_speed'], '/'));
                    $speed = "до {$speed}";
                    $services[$i][$z]['not_garant'] = $speed;
                }
                else
                {
                    $speed = $services[$i][$z]['speed'];
                }

                $template2 = str_replace('{$dateagree}', $this->_time, $template2);
                $template2 = str_replace('{$data_contract_agree}', $this->_time, $template2);
                $template2 = str_replace('{$iterator}', $iterator, $template2);
                $template2 = str_replace('{$contract_number}', $services[$i][$z]['contract_number'], $template2);
                $template2 = str_replace('{$time}', $this->_time, $template2);
                $template2 = str_replace('{$client_name}', $services[$i][$z]['client_name'], $template2);
                $template2 = str_replace('{$pcross}', $services[$i][$z]['pcross'], $template2);
                $template2 = str_replace('{$u_login}', $services[$i][$z]['u_login'], $template2);
                $template2 = str_replace('{$u_passwd}', $services[$i][$z]['u_passwd'], $template2);
                $template2 = str_replace('{$servicetype_name}', $services[$i][$z]['servicetype_name'], $template2);
                $template2 = str_replace('{$tarif_name}', $services[$i][$z]['tarif_name'], $template2);
                $template2 = str_replace('{$tarif_price}', $services[$i][$z]['tarif_price'], $template2);
                $template2 = str_replace('{$traffic}', $services[$i][$z]['limit'], $template2);
                $template2 = str_replace('{$id}', $services[$i][$z]['id'], $template2);
                $template2 = str_replace('{$isface}', $services[$i][$z]['face'], $template2);
                $template2 = str_replace('{$user_name}', $services[$i][$z]['user_name'], $template2);
                $template2 = str_replace('{$user_phone}', $services[$i][$z]['user_phone'], $template2);
                $template2 = str_replace('{$user_email}', $services[$i][$z]['user_email'], $template2);
                $template2 = str_replace('{$client_id}', $services[$i][$z]['client_id'], $template2);
                $template2 = str_replace('{$inn}', $services[$i][$z]['inn'], $template2);
                $template2 = str_replace('{$rschets}', $rschetText, $template2);
                $template2 = str_replace('{$bank_name}', $services[$i][$z]['bank_name'], $template2);
                $template2 = str_replace('{$mfo}', $services[$i][$z]['mfo'], $template2);
                $template2 = str_replace('{$okonx}', $services[$i][$z]['okonx'], $template2);
                $template2 = str_replace('{$client_phone}', $services[$i][$z]['client_phone'], $template2);
                $template2 = str_replace('{$fax}', $services[$i][$z]['fax'], $template2);
                $template2 = str_replace('{$email}', $services[$i][$z]['email'], $template2);
                $template2 = str_replace('{$contact_name}', $services[$i][$z]['point_contact_name'], $template2);
                $template2 = str_replace('{$contact_phone}', $services[$i][$z]['pts_phone'], $template2);
                $template2 = str_replace('{$connect_address}', $services[$i][$z]['connect_address'], $template2);
                $template2 = str_replace('{$email}', $services[$i][$z]['email'], $template2);
                $template2 = str_replace('{$speed}', $speed, $template2);
                $template2 = str_replace('{$phone}', $services[$i][$z]['phone'], $template2);

				$template2 = str_replace('{$manager_name}', $services[$i][$z]['manager'], $template2);
                $template2 = str_replace('{$start_date_pr}', date('Y/m/d', strtotime($services[$i][$z]['startdate'])), $template2);

        		$template2 = str_replace('{$sign_name}', $this->_clientInfo['sign_name'], $template2);
        		$template2 = str_replace('{$post_sign_name}', $this->_clientInfo['post_sign_name'], $template2);
                $servicePdfHelper = new ServicePdfHelper($this->_companyRekvizits);
                $servicePdfHelper->setClientInfo($this->_clientInfo);

                $method = $services[$i][$z]['tablename'] . 'Additional';
                $services[$i][$z]['dateagree'] = $this->_time;
                $this->_pdf->writeHTML($template2 . AdditionalHelper :: $method ($services[$i][$z]), true, 0, true, 0);
                $this->_pdf->writeHTML($servicePdfHelper->getServiceFooter($services[$i][$z]), true, 0, true, 0);

                $iterator++;
                $this->_pdf->AddPage();

                $protocolText = $this->getTemplate('protocolAgree');

                $protocolText = str_replace('{$client_name}', $services[$i][$z]['client_name'], $protocolText);
                $protocolText = str_replace('{$servicetype_name}', $services[$i][$z]['servicetype_name'], $protocolText);
                $protocolText = str_replace('{$contract_number}', $services[$i][$z]['contract_number'], $protocolText);
                $protocolText = str_replace('{$dateagree}', $services[$i][$z]['dateagree'], $protocolText);
                $protocolText = str_replace('{$time}', $this->_time, $protocolText);

                $protocolText = str_replace('{$manager_name}', $this->_clientInfo['manager'], $protocolText);
				$protocolText = str_replace('{$start_date_pr}', date('Y/m/d', strtotime($services[$i][$z]['startdate'])), $protocolText);
                $this->_pdf->writeHTML($protocolText, true, 0, true, 0);

                $modemDataNew = $modemModel->getModemForClient($services[$i][$z]['client_id'], $services[$i][$z]['point_id']);

                for($m = 0; $m < count($modemDataNew); $m++)
                {
					$modems .= $modemDataNew[$m]['modem_serial'];
					$modemPrice = $modemPrice + $modemDataNew[$m]['modem_price'];
				}

                //$services[$i][$z]['tarif_price'] = ServiceHelper::getAbonPrice($services[$i][$z]);

				$services[$i][$z]['modem_serial'] = $modems;
				$services[$i][$z]['modem_price']  = $modemPrice;

				$modemPrice = 0;
				$modems = "";

                $this->_pdf->writeHTML($servicePdfHelper->getServiceProtocol($services[$i][$z]['tablename'], $services[$i][$z]), true, 0, true, 0);

				// TODO
				// Addon for EC clients

				if($services[0][0]['currency'] == "UZS")
				{
					$nextYearObj = new Zend_Date(date("Y-12-31"));
        			$nextYear    = $nextYearObj->toString('d MMMM Y г.');

					$protocolTextFooter = $this->getTemplate('protocolAgreeFooterEC');
					$protocolTextFooter = str_replace('{$nextYearEC}', $nextYear, $protocolTextFooter);
				}
				else
				{
					$protocolTextFooter = $this->getTemplate('protocolAgreeFooter');
				}
				// End Addon

                $protocolTextFooter = str_replace('{$enddate}', $services[$i][$z]['enddate'], $protocolTextFooter);
                $protocolTextFooter = str_replace('{$startdate}', $services[$i][$z]['dateagree'], $protocolTextFooter);
                $protocolTextFooter = str_replace('{$client_name}', $services[$i][$z]['client_name'], $protocolTextFooter);

        		$protocolTextFooter = str_replace('{$sign_name}', $this->_clientInfo['sign_name'], $protocolTextFooter);
        		$protocolTextFooter = str_replace('{$post_sign_name}', $this->_clientInfo['post_sign_name'], $protocolTextFooter);
				$protocolTextFooter = str_replace('{$manager}', $this->_clientInfo['manager'], $protocolTextFooter);
				$protocolTextFooter = str_replace('{$start_date_pr}', date('Y/m/d', strtotime($services[$i][$z]['startdate'])), $protocolTextFooter);
                $this->_pdf->writeHTML($protocolTextFooter, true, 0, true, 0);
                $this->_pdf->AddPage();

                if($services[$i][$z]['group_name'] == 'cafe')
                {
                	$date = date('"d" m-Y', strtotime($services[$i][$z]['dateagree']));

                    $reklamaInetCafe = $this->getTemplate('inet_cafe');
                    $reklamaInetCafe = str_replace('{$date_dogovor_string}', $date, $reklamaInetCafe);
                    $this->_pdf->writeHTML($reklamaInetCafe, true, 0, true, 0);
                    $this->_pdf->AddPage();
                }


                $Zona = $this->getTemplate('Zona');

                $Zona = str_replace('{$contract_number}', $services[$i][$z]['contract_number'], $Zona);
                $Zona = str_replace('{$dateagree}', $services[$i][$z]['dateagree'], $Zona);
                $Zona = str_replace('{$start_date_pr}', date('Y/m/d', strtotime($services[$i][$z]['startdate'])), $Zona);
				$this->_pdf->writeHTML($Zona, true, 0, true, 0);

				if($services[$i][$z]['need_cross'] == 1 or $services[$i][$z]['tablelink'] == "ngn")
				{
					// Rect
					$this->_pdf->Text(10, 57, 'Шкаф исполнителя');
					$this->_pdf->Rect(10, 60, 30, 10, 'D');
					$this->_pdf->Arrow($x0=40, $y0=65, $x1=50, $y1=65, $arm_size=2, $arm_angle=5);

					$this->_pdf->Text(50, 57, 'Рамка исполнителя');
					$this->_pdf->Rect(50, 60, 30, 10, 'D');
					$this->_pdf->Arrow($x0=80, $y0=65, $x1=90, $y1=65, $arm_size=2, $arm_angle=5);

					$this->_pdf->Text(35, 75, '[Исполнитель]');

					$this->_pdf->Rect(90, 50, 1, 35, 'DF');

					$crossStr = "Кросс (".$services[$i][$z]['pcross'].")";
					$this->_pdf->Text(94, 57, $crossStr);
					$this->_pdf->Rect(90, 60, 30, 10, 'D');
					$this->_pdf->Arrow($x0=120, $y0=65, $x1=130, $y1=65, $arm_size=2, $arm_angle=5);
					$this->_pdf->Text(105, 75, '[Заказчик]');

					$this->_pdf->Rect(130, 50, 1, 35, 'DF');

					$this->_pdf->Text(132, 57, 'Сплитер ADSL-модем');
					$this->_pdf->Rect(130, 60, 30, 10, 'D');
					$this->_pdf->Arrow($x0=160, $y0=65, $x1=170, $y1=65, $arm_size=2, $arm_angle=5);
					$this->_pdf->Text(141, 75, '[Исполнитель]');

					$this->_pdf->Rect(170, 50, 1, 35, 'DF');

					$this->_pdf->Text(171, 57, 'Оборудование заказчика');
					$this->_pdf->Rect(170, 60, 30, 10, 'D');

					$this->_pdf->Text(181, 75, '[Заказчик]');

					$ZonaFooter = $this->getTemplate('ZonaFooter');
					$this->_pdf->writeHTML($ZonaFooter, true, 0, true, 0);

					$this->_pdf->AddPage();
				}
				else
				{
					$modelResponsibility = new ResponsibilityContractHelper();
					$this->_pdf->writeHTML($modelResponsibility->getServiceResp($services[$i][$z]['tablelink'], 'test'), true, 0, true, 0);
					$this->_pdf->AddPage();
				}

                // ТУТ Приложение 4
                $modemDataNew2 = $modemModel->getModemForClient($services[$i][$z]['client_id'], $services[$i][$z]['point_id']);

                for($m=0; $m < count($modemDataNew2); $m++)
                {
                	$htmlModemCorp .= "
                        	<tr>
                            	<td  align='center'>1</td>
                                <td  align='center'>{$modemDataNew2[$m]['modem_name']}</td>
                                <td align='center'>шт.</td>
                                <td align='center'>1</td>
                                <td align='center'>{$modemDataNew2[$m]['modem_serial']}</td>
                                <td align='center'>{$modemDataNew2[$m]['modem_price']}</td>
                            </tr>";
                }

                $corpModemTemplate = $this->getTemplate('CorpModem');
                $corpModemTemplate = str_replace('{$reserch_modem}', $htmlModemCorp, $corpModemTemplate);
                $corpModemTemplate = str_replace('{$dateagree}', $this->_time, $corpModemTemplate);
                $corpModemTemplate = str_replace('{$client_name}', $services[$i][$z]['client_name'], $corpModemTemplate);
                $corpModemTemplate = str_replace('{$contract_number}', $services[$i][$z]['contract_number'], $corpModemTemplate);
                $corpModemTemplate = str_replace('{$time}', $this->_time, $corpModemTemplate);
                $this->_pdf->writeHTML($corpModemTemplate, true, 0, true, 0);

                $this->_pdf->AddPage();

                $backFromClient = $this->getTemplate('BackActCorp');
                $backFromClient = str_replace('{$reserch_modem}', $htmlModemCorp, $backFromClient);
                $backFromClient = str_replace('{$client_name}', $services[$i][$z]['client_name'], $backFromClient);
                $backFromClient = str_replace('{$dateagree}', $this->_time, $backFromClient);
                $backFromClient = str_replace('{$contract_number}', $services[$i][$z]['contract_number'], $backFromClient);
                $this->_pdf->writeHTML($backFromClient, true, 0, true, 0);
                $this->_pdf->AddPage();

                $htmlModemCorp = "";
            }
        }

        $this->sentResponce('contract');
    }

    public function streamPrint($rschetText, $services)
    {
    	$this->_pdf->SetTitle('Договор № ' . $this->_clientInfo['contract_number']);
        $modemModel = new ModemModel();

        $ngnModel     = new NgnModel();
		$dynamicModel = new DynamicTarifModel();


        $template = $this->getTemplate('contractStream');
        $this->writeFirtTemplate($template, $rschetText);

        $template2 = $this->getTemplate('contractAppendixStream');

        $crossI = 0; $crossZ = 0;

        for ($i = 0; $i < count($services); $i++)
        {
            for ($z = 0; $z < count($services[$i]); $z++)
            {

				$dynamicData = $dynamicModel->getDynamicData($services[$i][$z]['u_login'], $services[$i][$z]['id']);

            	if($services[$i][$z]['group_name'] == "special")
            	{
            		$services[$i][$z]['speed'] 		 = $dynamicData['speed_up'] . "/". $dynamicData['speed_down'];
            		$services[$i][$z]['tarif_price'] = $dynamicData['tarif_price'];
            	}

                $template2 = str_replace('{$dns_support}', 
                        $services[$i][$z]['tablelink'] == 'hosting' ? '<td>Поддержка DNS в других зонах, USD</td>' : '', $template2);

            	$htmlTarif .= "
            		<tr>
						<td>{$services[$i][$z]['tarif_name']}</td>
						<td>{$services[$i][$z]['speed']}</td>
						<td>{$services[$i][$z]['tarif_price']}</td>
						<td>{$services[$i][$z]['limit']}</td>
						<td>{$services[$i][$z]['tarif_components'][0]['traffic_excess']}</td>
            	";
                $htmlTarif .= $services[$i][$z]['tablelink'] == 'hosting' ? '<td>Да(2.5$)</td>' : '';
                $htmlTarif .= "</tr>";


            	if ($services[$i][$z]['tablelink'] == 'ngn')
            	{
            		$crossNumber = '';
            		$ngnInfo = $ngnModel->getAdditionalInfo($services[$i][$z]['id']);

            		if (is_array($ngnInfo['numbers']['numbers']))
            		{
            			$concatSymbol = '';

            			foreach($ngnInfo['numbers']['numbers'] as $value)
        				{
            				$crossNumber .=  $concatSymbol. $value['number'];
            				$concatSymbol = ', ';
        				}
            		}
            	}
            	else
            	{
            		$crossNumber = $services[$i][$z]['pcross'];
            	}

            	$htmlIdentifications .= "
            		<tr>
            			<td>
							{$services[$i][$z]['servicetype_name']}
						</td>
						<td>
							{$crossNumber}
						</td>
						<td>
							{$services[$i][$z]['u_login']}
						</td>
						<td>
							{$services[$i][$z]['u_passwd']}
						</td>
					</tr>";

				if ($services[$i][$z]['need_cross'])
				{
					$crossI = $i;
					$crossZ = $z;
				}
            }
        }

		$template2 = str_replace('{$tarifs}', $htmlTarif, $template2);
		$template2 = str_replace('{$identifications}', $htmlIdentifications, $template2);

        $modemData = $modemModel->getModemForClient($services[0][0]['client_id']);

        for($m = 0; $m < count($modemData); $m++)
        {
         	$htmlModem .= "
            	<tr>
                	<td>1</td>
                    <td>{$modemData[$m]['modem_name']}</td>
                    <td>шт.</td>
                    <td>1</td>
                    <td>{$modemData[$m]['modem_serial']}</td>
                    <td>{$modemData[$m]['modem_price']}</td>
               </tr>";
		}

        $template2 = str_replace('{$reserch_modem}', $htmlModem, $template2);

        $template2 = str_replace('{$dateagree}', $this->_time, $template2);
        $template2 = str_replace('{$data_contract_agree}', $this->_time, $template2);

        $template2 = str_replace('{$contract_number}', $services[$crossI][$crossZ]['contract_number'], $template2);
        $template2 = str_replace('{$time}', $this->_time, $template2);
        $template2 = str_replace('{$client_name}', $services[$crossI][$crossZ]['client_name'], $template2);
        $template2 = str_replace('{$pcross}', $services[$crossI][$crossZ]['pcross'], $template2);
        $template2 = str_replace('{$u_login}', $services[$crossI][$crossZ]['u_login'], $template2);
        $template2 = str_replace('{$u_passwd}', $services[$crossI][$crossZ]['u_passwd'], $template2);
        $template2 = str_replace('{$servicetype_name}', $services[$crossI][$crossZ]['servicetype_name'], $template2);
        $template2 = str_replace('{$tarif_name}', $services[$crossI][$crossZ]['tarif_name'], $template2);
        $template2 = str_replace('{$tarif_price}', $services[$crossI][$crossZ]['tarif_price'], $template2);
        $template2 = str_replace('{$traffic}', $services[$crossI][$crossZ]['limit'], $template2);
        $template2 = str_replace('{$id}', $services[$crossI][$crossZ]['id'], $template2);
        $template2 = str_replace('{$isface}', $services[$crossI][$crossZ]['face'], $template2);
        $template2 = str_replace('{$user_name}', $services[$crossI][$crossZ]['user_name'], $template2);
        $template2 = str_replace('{$user_phone}', $services[$crossI][$crossZ]['user_phone'], $template2);
        $template2 = str_replace('{$user_email}', $services[$crossI][$crossZ]['user_email'], $template2);
        $template2 = str_replace('{$client_id}', $services[$crossI][$crossZ]['client_id'], $template2);
        $template2 = str_replace('{$inn}', $services[$crossI][$crossZ]['inn'], $template2);
        $template2 = str_replace('{$rschets}', $rschetText, $template2);
        $template2 = str_replace('{$bank_name}', $services[$crossI][$crossZ]['bank_name'], $template2);
        $template2 = str_replace('{$mfo}', $services[$crossI][$crossZ]['mfo'], $template2);
        $template2 = str_replace('{$okonx}', $services[$crossI][$crossZ]['okonx'], $template2);
        $template2 = str_replace('{$client_phone}', $services[$crossI][$crossZ]['client_phone'], $template2);
        $template2 = str_replace('{$fax}', $services[$crossI][$crossZ]['fax'], $template2);
        $template2 = str_replace('{$email}', $services[$crossI][$crossZ]['email'], $template2);
        $template2 = str_replace('{$contact_name}', $services[$crossI][$crossZ]['point_contact_name'], $template2);
        $template2 = str_replace('{$contact_phone}', $services[$crossI][$crossZ]['pts_phone'], $template2);
        $template2 = str_replace('{$connect_address}', $services[$crossI][$crossZ]['connect_address'], $template2);
        $template2 = str_replace('{$email}', $services[$crossI][$crossZ]['email'], $template2);
        $template2 = str_replace('{$speed}', $services[$crossI][$crossZ]['speed'], $template2);
        $template2 = str_replace('{$phone}', $services[$crossI][$crossZ]['phone'], $template2);
		$template2 = str_replace('{$manager_name}', $services[$crossI][$crossZ]['manager'], $template2);
        $template2 = str_replace('{$start_date_pr}', date('Y/m/d', strtotime($services[$crossI][$crossZ]['startdate'])), $template2);

        $template2 = str_replace('{$sign_name}', $this->_clientInfo['sign_name'], $template2);
        $template2 = str_replace('{$post_sign_name}', $this->_clientInfo['post_sign_name'], $template2);

        $template2 = str_replace('{$passp_n}', $services[$crossI][$crossZ]['passp_n'], $template2);
        $template2 = str_replace('{$ruvd_name}', $services[$crossI][$crossZ]['ruvd_name'], $template2);
//var_dump($template2); exit;
        $tplPages = explode('<page_break>', $template2);
        $first = true;

        foreach ($tplPages as $page)
        {
        	if ($first)
        	{
        		$first = false;
        	}
        	else
        	{
        		$this->_pdf->AddPage();
        	}

        	$this->_pdf->writeHTML($page , true, 0, true, 0);
        }

        $this->sentResponce('contract');
    }
}
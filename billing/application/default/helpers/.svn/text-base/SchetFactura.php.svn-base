<?php
require_once ('ClientModel.php');
require_once ('InvoiceModel.php');
require_once ('KassaModel.php');

class SchetFactura
{
	public function getPdfSchetFakturaHeader($data)
	{
		$lastdate = date('d.m.Y',strtotime($data['lastdate']));
		$today    = date('d.m.Y');

		$rschets  = SchetFactura::getRschet($data['client_id']);

		$bank = SchetFactura::getBank($data['bank_id']);
		$city = SchetFactura::getCity($data['country_id']);

		$address = $data['address'] ? $data['address'] : $data['legaladdress'];

		$html = "
		<span width=\"100%\" style=\"font-family: arial\" align=\"center\">
    		<b style=\"font-family:arialbd; font-size: 11\">
				СЧЁТ-ФАКТУРА N {$data['schetfnum']} <br />
				от $lastdate года <br />
				по договору N {$data['contract_number']} от {$data['contract_date']}
			</b>
		</span><br />
		<span width=\"100%\" align=\"right\">
    		Регистрационный номер: {$data['client_id']}
		</span>
		<br />
		<table align=\"left\" border=\"1\" cellpadding=\"4\"  cellspacing=\"4\">
			<tr>
				<td>
					<b style=font-family:arialbd >Поставщик: </b> ООО \"Sharq Telekom\"
					<br />
					<b style=font-family:arialbd >Адрес: </b> г. Ташкент, ул. А.Темура, пр. 1, д.6
					<br />
					<b style=font-family:arialbd >Телефон: </b> 113-00-00, факс: 113-13-02
					<br />
					<b style=font-family:arialbd >Р/сч: </b> 20208000304118577004
					<br />
					<b style=font-family:arialbd >в: </b> ОПЕРУ АКБ \"КАПИТАЛ БАНК\"
					<br />
					<b style=font-family:arialbd >Город: </b> Ташкент
					<br />
					<b style=font-family:arialbd >МФО: </b> 00974
					<br />
					<b style=font-family:arialbd >ИНН: </b>203608943
					<br />
					<b style=font-family:arialbd >ОКОНХ: </b> 82100
				</td>
				<td>
					<b style=font-family:arialbd >Получатель: </b> {$data['client_name']}
					<br />
					<b style=font-family:arialbd >Адрес: </b> {$address}
					<br />
					<b style=font-family:arialbd >Телефон: </b> {$data['phone']}, факс: {$data['fax']}
					<br />
					<b style=font-family:arialbd >Р/сч: </b> {$rschets}
					<br />
					<b style=font-family:arialbd >в: </b> {$bank}
					<br />
					<b style=font-family:arialbd >Город: </b> {$city}
					<br />
					<b style=font-family:arialbd >МФО: </b> {$data['mfo']}
					<br />
					<b style=font-family:arialbd >ИНН: </b>{$data['inn']}
					<br />
					<b style=font-family:arialbd >ОКОНХ: </b> {$data['okonx']}
				</td>
			</tr>
		</table>
		</br>
		";
		return $html;
	}

	public function getBodyPdf($data, $ballanceData, $isTreasure = false, $lastDate = null)
	{
		$total_summa = 0;

		$html = "
		<table border=\"1\" cellpadding=\"0\">
			<tr>
				<td align=\"center\" width=\"143\"> <b style=\"font-family:arialbd\" >Наименование товара </b></td>
				<td align=\"center\" width=\"27\"><b style=\"font-family:arialbd\" > Ед. изм. </b></td>
				<td align=\"center\" width=\"43\"><b style=\"font-family:arialbd\" > к.-во </b></td>
				<td align=\"center\" width=\"50\"><b style=\"font-family:arialbd\" > Цена </b></td>
				<td align=\"center\"><b style=\"font-family:arialbd\" > Стоимость поставки без учёта НДС </b></td>
				<td align=\"center\">
					<b style=\"font-family:arialbd\" >Акцизный налог </b><br />
					<table width=\"100%\" border=\"1\" cellspacing=\"0\">
						<tr>
							<td>
								Ставка
							</td>
							<td>
								Сумма
							</td>
						</tr>
					</table>
				</td>

				<td align=\"center\">
					<b style=\"font-family:arialbd\" >НДС </b><br />
					   <br />
					<table border=1 width=\"100%\" cellspacing=\"0\">
						<tr>
							<td> Ставка </td>
							<td> Сумма </td>
						</tr>
					</table>
				</td>
				<td align=\"center\"><b style=\"font-family:arialbd\" > Стоимость поставки с учётом НДС </b></td>
			</tr>
		";

		/*
		 * writing ballance on start month
		 */
		$ballanceBeforeAmount = number_format($ballanceData['before']['amount'], 2, ',', ' ');
		$ballanceBeforeDate = date('d.m.Y', strtotime($ballanceData['before']['date']));


		if ($isTreasure)
		{
			$treasuryRateModel = new TreasuryRateModel();
			$currentRate = $treasuryRateModel->getRate($lastDate);

			for ($i = 0; $i < count($data); $i++)
			{
				$data[$i]['price'] = $data[$i]['price'] * $currentRate;
				$data[$i]['amount'] = $data[$i]['amount'] *  $currentRate;
				$data[$i]['traffic_overlimit_price'] = $data[$i]['traffic_overlimit_price'] * $currentRate;
				$data[$i]['overlimit'] = $data[$i]['overlimit'] * $currentRate;
				$data[$i]['total'] = $data[$i]['total'] * $currentRate;
			}
		}

		for($i=0; $i<count($data); $i++)
		{
			$amount				 = number_format($data[$i]['amount'], 2, ',', ' ');

			$traffic_overlimit 		 = money_format('%i', $data[$i]['traffic_overlimit']);
			$traffic_overlimit_price = $data[$i]['traffic_overlimit_price'];
			$overlimit 				 = number_format($data[$i]['overlimit'], 2, ',', ' ');
			$is_total 	             = number_format($data[$i]['total'] - $data[$i]['overlimit'], 2, ',', ' ');

			$html .= "
			<tr>
				<td align=\"center\" width=\"143\">{$data[$i]['servicename']}</td>
				<td align=\"center\"  width=\"27\"> {$data[$i]['unit']} </td>
				<td align=\"center\"  width=\"43\"> {$data[$i]['quantity']}  </td>
				<td align=\"center\"  width=\"50\"> {$data[$i]['price']} </td>
				<td align=\"center\"> {$is_total} </td>
				<td align=\"center\">
					<center> Без акцизного налога </center>
			    </td>
				<td align=\"center\">
					<center> Без НДС </center>
				</td>
				<td align=\"center\"> ---- </td>
			</tr>
			";

			if($data[$i]['overlimit'] > 0)
			{
			 $html .= "
					<tr>
						<td align=\"center\" width=\"143\"><b style=\"font-family:arialbd\" > Превышение лимита установленного абонентской платой за {$data[$i]['name']} месяц </b></td>
						<td align=\"center\"  width=\"27\"> Мб. </td>
						<td align=\"center\"  width=\"43\"> {$traffic_overlimit} </td>
						<td align=\"center\"  width=\"50\"> {$traffic_overlimit_price} </td>
						<td align=\"center\"> {$overlimit} </td>
						<td align=\"center\">
							<center> Без акцизного налога </center>
					    </td>
						<td align=\"center\">
							<center> Без НДС </center>
						</td>
						<td align=\"center\"> ---- </td>
					</tr>
			  ";
			}
		}

		/*
		 * writing ballance on after month
		 */
		$ballanceAfterAmount = number_format($ballanceData['after']['amount'], 2, ',', ' ');
		$ballanceAfterDate = date('d.m.Y', strtotime("-1 day {$ballanceData['after']['date']}"));

		return $html;
	}

	public function getRschet($client_id)
	{
		$model   = new ClientModel();
		$rschets = $model->rschetClient($client_id);

        return $rschets[0]['schet'];
	}

	public function getBank($bankid)
	{
		$model = new ClientModel();
		return $model->getBankName($bankid);

	}

	public function getCity($city_id)
	{
		$city_model = new ClientModel();
		return $city_model->getCityName($city_id);
	}

	public function getFooterPdf($data_footer, $date, $isTreasure = false, $clientID = false)
	{
		$invoiceModel = new InvoiceModel();
		$kassaModel   = new KassaModel();
		$rateModel = new Rates();

		$nowRate     = $rateModel->getRate($date);

		$rekvizits 	  = Zend_Registry::get('rekvizits');
		$total = 0;

		for($i = 0; $i < count($data_footer); $i++)
		{
			$total = $total + $data_footer[$i]['total'];
		}

		$html = '';

		$currency = ClientHelper::getCurrencyByClientID($clientID);

		if ( ! $isTreasure)
		{
			$total = round($total, 2);

			if($currency == "USD")
			{
				$total_in_summas = $total * $nowRate;
			}
			else
			{
				$total_in_summas = $total;
			}


			$total_in_summas = round($total_in_summas, 2);
			$total = number_format($total, 2, ',', ' ');
			$total_print = number_format($total_in_summas, 2, ',', ' ');

			$html .= "
				<tr>
					<td align=\"right\" width=\"143\"><b style=\"font-family:arialbd\" > Итого начислено: </b></td>
					<td align=\"center\"  width=\"27\"></td>
					<td align=\"center\"  width=\"43\"></td>
					<td align=\"center\"  width=\"50\"></td>
					<td align=\"right\"><b style=\"font-family:arialbd\" > {$total} </b></td>
					<td align=\"center\"></td>
					<td align=\"center\"></td>
					<td align=\"center\"></td>
				</tr>";
			$rateText = "Курс на " . date('d.m.Y', strtotime($date)) . " составляет: 1USD = {$nowRate} сум <br />";
		}
		else
		{
			$treasuryRateModel = new TreasuryRateModel();
			$currentRate = $treasuryRateModel->getRate($date);


			if($currency == "USD")
			{
				$total_in_summas = round($total * $currentRate, 2);
			}
			else
			{
				$total_in_summas = round($total, 2);
			}
			$total_print = number_format($total_in_summas, 2, ',', ' ');
			$rateText = '';
		}


		$html .= "
			<tr>
				<td align=\"right\" width=\"143\"><b style=\"font-family:arialbd\" > Итого начислено в сум: </b></td>
				<td align=\"center\"  width=\"27\"></td>
				<td align=\"center\"  width=\"43\"></td>
				<td align=\"center\"  width=\"50\"></td>
				<td align=\"right\"><b style=\"font-family:arialbd\" > ". $total_print." </b></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
			</tr>
		";

		$html .= "</table>";
		$html .= "
		<table border=\"0\">
		    <tr>
				<td align=\"left\">
					<b style=\"font-family:arialbd\" >
							{$rateText}
							Итого начислено: " . CurrencyToWord::num2str($total_in_summas, 'UZS') . " без НДС
					</b>
				</td>
			</tr>
		</table>
		<br />
		<table border=\"0\">
		    <tr>
				<td align=\"left\">
					Руководитель:_________________________{$rekvizits->gen_name_main}
				</td>
				<td align=\"left\">
					Получил:___________________________
					<center><sup><p>(подпись ответственного лица от Получателя)</p></sup></center>
				</td>
			</tr>
			<tr>
				<td align=\"left\">
					Главный бухгалтер:_________________________{$rekvizits->head_accounting_department}
				</td>
				<td align=\"left\">
					По доверенности:<br />
					N___________от\"___\"___________________20__г.
				</td>
			</tr>
			<tr>

			</tr>
			<tr>
				<td align=\"left\">
					Отпустил:_______________________
					<sup><p>(подпись ответственного лица от Поставщика)</p></sup>
				</td>
				<td align=\"left\">
					__________________________
					<sup><p>(Ф.И.О. получателя)</p></sup>
				</td>
			</tr>
		</table>
		";

		return $html;
	}
}

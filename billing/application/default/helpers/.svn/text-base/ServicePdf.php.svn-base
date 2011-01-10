<?php
/**
 * Helper for generate pdf for service
 *
 * @author alex, marat
 */

require_once ('PdfHelper.php');
require_once ('AdslModel.php');
require_once ('NgnModel.php');

class ServicePdfHelper extends PdfHelper
{
    public function __construct($companyRekvizits)
    {
        parent::__construct($companyRekvizits);
    }

    public function getServiceFooter($data = null)
    {
        $htmlcontent .= '
        <table border="1" width="100%" style="font-family: arial" cellpadding="2">
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">ООО "Sharq Telekom"</b>
            </td>
            <td colspan="2">
                <b style="font-family:arialbd">' . $this->_clientInfo['client_name'] . '</b>
            </td>
        </tr>
        <tr>
            <td>
                Дата <br />
					' . $data['dateagree'] . '
            </td>
            <td>
                Подпись
                <br />
                <br />
            </td>
            <td>
                Дата <br />
				' . $data['dateagree'] . '
            </td>
            <td>
                Подпись
                <br />
                <br />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Имя:</b>
                ' . $this->_clientInfo['manager'] . '
            </td>
            <td colspan="2">
                <b style="font-family:arialbd">
                    Имя:
					'.$this->_clientInfo['sign_name'].'
                    <br />
                    <br />
                </b>
            </td>
        </tr>
        <tr>
        <td colspan="2">
                <b style="font-family:arialbd">Должность:</b>
                	Менеджер
            </td>
            <td colspan="2">
                <b style="font-family:arialbd">
                    Должность:
					'.$this->_clientInfo['post_sign_name'].'
                    <br />
                    <br />
                </b>
            </td>
        </tr>
        </table>
        <br />
        <br />
        <br />
        ';
        return $htmlcontent;
    }

    public function getServiceProtocol($serviceType, $data)
    {

        switch ($serviceType)
        {
            case 'adsl':
                return $this->getAdslServiceProtocol($data);

            case 'vpn':
                return $this->getVpnServiceProtocol($data);

            case 'hosting':
                return $this->getHostingServiceProtocol($data);

            case 'tasix':
                return $this->getTasixServiceProtocol($data);

            case 'ngn':
                return $this->getNgnServiceProtocol($data);

            case 'wifi':
                return $this->getWifiServiceProtocol($data);

            case 'collacation':
                return $this->getCollacationServiceProtocol($data);

            case 'tradtel':
                return $this->getTradtelServiceProtocol($data);

            case 'isdn':
                return $this->getIsdnServiceProtocol($data);

            default:
                return '';
        }
    }

    private function getAdslServiceProtocol($data)
    {
    	$currency = trim($data['currency']);

		$model = new AdslModel();
		$modem_data = $model->getModem($data['client_id'], $data['point_id']);

		if($data['discount'] != 0)
		{
			$discount_amount = $data['discount'] * $data['tarif_price'] / 100;
		}
		if($data['discount'] == "")
		{
			$data['discount'] = 0;
		}
		$total_amount = $data['tarif_price'] - $discount_amount;

    	$speed = $data['speed'];

    	$startSpeed = $data['start_speed'];

    	if ($startSpeed)
    	{
    		$speed = substr($speed, 0, strpos($speed, '/'));
    		$speed = "до {$speed}";
    	}

    	if ( is_array($data['tarif_components']) && count($data['tarif_components']) )
    	{
    		$excess = $data['tarif_components'][0]['traffic_excess'];
    	}

        $htmlcontent .= '
        <table border="1" width="100%">
        	<tr>
        		<td>Базовые тарифы</td>
        		<td>Соединение на скорости Кбит.с</td>
        		<td>Абонентская плата '. $currency .'</td>
        		<td>Лимит траффика Мб</td>
        		<td>Стоимость 1 Мб при превышении лимита траффика, '. $currency .'</td>
        	</tr>
        	<tr>
        		<td>' . $data['tarif_name'] . '</td>
        		<td>' . $speed . '</td>
        		<td>' . $data['tarif_price'] . '</td>
        		<td>' . $data['limit'] . '</td>
        		<td>' . $excess . '</td>
        	</tr>
        </table>
        <br />
        <br />';

		for($i=0; $i<count($modem_data); $i++)
		{
			$htmlcontent .= '
			<table border="1" width="100%" cellpadding=2>
		        <tr>
		            <td>
		                Услуги при подключении
		            </td>
		            <td>
		                Тип подключения
		            </td>
		            <td>
		                Наличие модема
		            </td>
		            <td>
		                Условия аренды модема
		            </td>
		            <td>
		               Тип модема
		            </td>
		            <td>
		                Стоимость услуги при подключении с учетом скидки, '. $currency .'
		            </td>
		        </tr>
		        <tr>
		            <td>---</td>
		            <td>
		                ADSL
		            </td>
		            <td>
		                ' . ($modem_data[$i]['modem_serial'] ? 'Без модема' : 'Свой модем') . '
		            </td>
		            <td>
		                ' . ($modem_data[$i]['modem_serial'] ? $modem_data[$i]['modem_serial'] : 'Свой модем') . '
		            </td>
		            <td>
						'.  $modem_data[$i]['modem_type'] .'
		            </td>
		            <td>
		                '.$modem_data[$i]['modem_price'].'
		            </td>
		        </tr>
		        </table>
		        <table border="0" width="100%">
		        <tr>
		            <td>
		                Итого за подключение: '.$modem_data[$i]['modem_price'].''. $currency .', Скидка: 0%

		            </td>
		        </tr>
				</table>
		        <br />
			';
		}

		$totalPrice = $data['tarif_price'] * (100 - $data['discount']) / 100;


		$htmlcontent .= '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Периодические услуги
            </td>
            <td>
                Скорость к клиенту/от клиента, Кбит/с
            </td>
            <td>
                Абонентская плата, '. $currency .'
            </td>
            <td>
                Скидка ' . $data['discount'] . '%
            </td>
            <td>
                Абонентская плата с учётом скидки, '. $currency .'
            </td>
        </tr>
        <tr>
            <td>
                ADSL
            </td>
            <td>
                ' . $speed . '
            </td>
            <td>
                ' . $data['tarif_price'] . '
            </td>
            <td>
                ' . $data['discount'] . '%
            </td>
            <td>
                ' . $totalPrice . '
            </td>
        </tr>';

		if ($data['ip_price'])
        {
        	$htmlcontent .= '
        	<tr>
        		<td>Дополнительные IP адреса</td>
        		<td></td>
        		<td>' . $data['ip_price'] . '</td>
        		<td></td>
        		<td>' . $data['ip_price'] . '</td>
        	</tr>
        	';
        }

        $totalPrice += $data['ip_price'];

		//TODO
		// ADDON START
		if($currency == "UZS")
		{
			$tableHeader_1 = "Периодические услуги";
			$tableHeader_2 = "Общая сумма договора до конца года (сум)";
			$tableHeader_4 = "Общая сумма договора до конца года с учётом скидки (сум)";
			$tableHeader_1_1 = $data['servicetype_name'];


		}
		else
		{
			$tableHeader_1 = "Доп. услуги";
			$tableHeader_2 = "Стоимость 1МБ, USD";
			$tableHeader_4 = "Стоимость 1МБ с учетом скидки, USD";
			$tableHeader_1_1 = "Стоимость 1МБ сверх лимита";
		}

        $htmlcontent .= '
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата:  ' . $totalPrice . ', Скидка: ' . $data['discount'] . '% <br />
                Регистрационная плата: ' . $data['reg_pay'] . ''. $currency .'
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
            <tr>
                <td>'.$tableHeader_1.'</td>
                <td>'.$tableHeader_2.'</td>
                <td>Скидка %</td>
                <td>'.$tableHeader_4.'</td>
            </tr>
        ';
		if($currency == "USD")
		{
				if (count($data['tarif_components']) == 0)
				{
					$htmlcontent .= '
					<tr>
		                <td> </td>
		                <td>0,00</td>
		                <td> </td>
		                <td>0,00</td>
		            </tr>
					';
				}

		        foreach ($data['tarif_components'] as $tarifComponent)
		        {
		            $htmlcontent .= "
		            <tr>
		                <td>{$tarifComponent['component_name']}</td>
		                <td>{$tarifComponent['traffic_excess']}</td>
		                <td>0%</td>
		                <td>{$tarifComponent['traffic_excess']}</td>
		            </tr>
		            ";
		        }
		}
		else
		{
			//300 тыс+ 100/30*29
			$days = (((date("t") - date('d')) + 1) * ($data['tarif_price'] / date("t")));
			$totalUZS = $days + (abs(date("m") - 12) * $data['tarif_price']);

					$htmlcontent .= '
					<tr>
		                <td>'.$tableHeader_1_1.'</td>
		                <td>'.number_format($totalUZS, 2, '.', ' ').'</td>
		                <td>' . $data['discount'] . '</td>
		                <td>'.number_format($totalUZS, 2, '.', ' ').'</td>
		            </tr>
					';
		}


        $htmlcontent .= '
        </table>
        ';

        return $htmlcontent;
    }

    private function getVpnServiceProtocol($data)
    {
		$model = new AdslModel();
		$modem_data = $model->getModem($data['client_id'], $data['point_id']);


    	$totalPrice = $data['tarif_price'] * (100 - $data['discount']) / 100;

        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Базовые тарифы
            </td>
            <td>
                Соединение на скорости, Кбит/с
            </td>
            <td>
                Абонентская плата, USD
            </td>
        </tr>
        <tr>
            <td>
                Безлимитный
            </td>
            <td>
                '.$data['speed'].'
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Услуги при подключении
            </td>
            <td>
                Тип подключения
            </td>
            <td>
                Наличие модема
            </td>
            <td>
                Условия аренды модема
            </td>
            <td>
                Скидка на услуги при подключении %
            </td>
            <td>
                Стоимость услуги при подключении с учётом скидки, USD
            </td>
        </tr>';

		for($i=0; $i<count($modem_data); $i++)
		{

		$htmlcontent .=
        '<tr>
            <td>
                ---
            </td>
            <td>
                VPN
            </td>
		            <td>
		                ' . ($modem_data[$i]['modem_serial'] ? 'Без модема' : 'Свой модем') . '
		            </td>
		            <td>
		                ' . ($modem_data[$i]['modem_serial'] ? $modem_data[$i]['modem_serial'] : 'Свой модем') . '
		            </td>
		            <td>
						'.  $modem_data[$i]['modem_type'] .'
		            </td>
		            <td>
		                '.$modem_data[$i]['modem_price'].'
		            </td>
        </tr>';
		}

		$htmlcontent .=
        '</table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого за подключение: '.$data['reg_pay_total'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Периодические услуги
            </td>
            <td>
                Количество точек, шт.
            </td>
            <td>
                Соединение на скорости, Кбит/с
            </td>
            <td>
                Абонентская плата за 1 точку, USD
            </td>
            <td>
                Скидка %
            </td>
            <td>
                Всего по абонентской плате с учетом скидки, USD
            </td>
        </tr>
        <tr>
            <td>
                '.$data['tablename'].'
            </td>
            <td>
                Количество точек, шт.
            </td>
            <td>
                '.$data['speed'].'
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
            <td>
                '.$data['discount'].'%
            </td>
            <td>
                '.$totalPrice.'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$totalPrice.'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>
        ';

        return $htmlcontent;
    }

    private function getHostingServiceProtocol($data)
    {
    	$currency = trim($data['currency']);



		if($currency == "UZS")
		{
			$curency_word = "UZS";
			$dns_header = "Да (4250 UZS)";
			$dns_amount = 4250.00;
			$tarifPrice = $data['tarif_price'] - 2.5;
			$total = $tarifPrice + $dns_amount;
		}
		else
		{
			$curency_word = "USD";
			$dns_header = "Да (2.5$)";
			$dns_amount = 2.5;
			$tarifPrice = $data['tarif_price'] - 2.5;
			$total = $tarifPrice + $dns_amount;
		}

        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Базовые тарифы
            </td>
            <td>
                Место на диске
            </td>
            <td>
                Домен третьего уровня
            </td>
            <td>
                Трафик
            </td>
            <td>
                Поддержка DNS в других зонах, '.$curency_word.'
            </td>
            <td>
                Оплата в месяц, '.$curency_word.'
            </td>
            <td>
                Итого оплата, '.$curency_word.'
            </td>
        </tr>
        <tr>
            <td>
                '.$data['tarif_name'].'
            </td>
            <td>
                '.$data['limit'].' Мб
            </td>
            <td>
                Нет
            </td>
            <td>
                '.$data['traffic'].'
            </td>
            <td>
                '. $dns_header .'
            </td>
            <td>
                ' . $tarifPrice . '
            </td>
            <td>
                ' . $total . '
            </td>
        </tr>
        </table>
        ';

        return $htmlcontent;
    }

    private function getTasixServiceProtocol($data)
    {

    	$total_regPay = $data['reg_pay'] + $data['modem_price'];

        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Базовые тарифы
            </td>
            <td>
                Соединение на скорости, Кбит/с
            </td>
            <td>
                Абонентская плата, USD
            </td>
        </tr>
        <tr>
            <td>
                Лимитный
            </td>
            <td>
                '.$data['speed'].'
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Услуги при подключении
            </td>
            <td>
                Тип подключения
            </td>
            <td>
                Наличие модема
            </td>
            <td>
                Условия аренды модема
            </td>
            <td>
                Скидка на услуги при подключении %
            </td>
            <td>
                Стоимость услуги при подключении с учётом скидки, USD
            </td>
        </tr>
        <tr>
            <td>
                ---
            </td>
            <td>
                Local NET
            </td>
            <td>
                '.$data['modem_serial'].'
            </td>
            <td>
                ---
            </td>
            <td>
                '.$data['discount'].'
            </td>
            <td>
                '.$total_regPay.'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого за подключение: '.$total_regPay.'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
	        <tr>
	            <td>
	                Переодические услуги
	            </td>
	            <td>
	                Скорость к клиенту/от клиента, Кбит/с
	            </td>
	            <td>
	                Абонентская плата, USD
	            </td>
	            <td>
	                Скидка %
	            </td>
	            <td>
	                Абонентская плата с учётом скидки, USD
	            </td>
	        </tr>
	        <tr>
	            <td>
	                ---
	            </td>
	            <td>
	                '.$data['speed'].'
	            </td>
	            <td>
	                '.$data['tarif_price'].'
	            </td>
	            <td>
	                '.$data['discount'].'
	            </td>
	            <td>
	                '.$data['reg_pay'].'
	            </td>
	        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$data['tarif_price'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>
        ';

        return $htmlcontent;
    }

    private function getNgnServiceProtocol($data)
    {
		$priceAbon 		  = $data['tarif_price'];


        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Услуги при подключении
            </td>
            <td>
                Стоимость услуги подключения, USD
            </td>
            <td>
                Скидка на услуги при подключении, USD
            </td>
            <td>
                Стоимсоть услуги при подключении с учетом скидки, USD
            </td>
        </tr>
        <tr>
            <td>
                Подключение
            </td>
            <td>
                '.$data['reg_pay'].'
            </td>
            <td>
                '.$data['discount'].'
            </td>
            <td>
                '.$data['reg_pay'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого за подключение: '.$data['reg_pay'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Периодические услуги
            </td>
            <td>
                Стоимость абонентской платы в месяц, USD
            </td>
        </tr>
        <tr>
            <td>
                Абонентская плата
            </td>
            <td>
                '.$priceAbon.'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$priceAbon.'$, Скидка: 0%
            </td>
        </tr>
        </table>

        <br />

        <br />
        ';

        return $htmlcontent;
    }

    private function getWifiServiceProtocol($data)
    {
        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Базовые тарифы
            </td>
            <td>
                Соединение на скорости, Кбит/с
            </td>
            <td>
                Абонентская плата, USD
            </td>
            <td>
                Лимит трафика, Мб
            </td>
            <td>
                Стоимость 1Мб при превышении лимита трафика, USD
            </td>
        </tr>
        <tr>
            <td>
                Лимитный
            </td>
            <td>
                '.$data['speed'].'
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
            <td>
                '.$data['traffic'].'
            </td>
            <td>
                '.$data['traffic_excess'].'
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Переодические услуги
            </td>
            <td>
                Скорость к клиенту/от клиента, Кбит/с
            </td>
            <td>
                Абонентская плата, USD
            </td>
            <td>
                Скидка %
            </td>
            <td>
                Абонентская плата с учётом скидки, USD
            </td>
        </tr>
        <tr>
            <td>
                WIFI
            </td>z
            <td>
                '.$data['speed'].'
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
            <td>
                '.$data['discount'].'
            </td>
            <td>
                '.$data['reg_pay'].'
            </td>
        </tr>
        ';

        if ($data['ip_price'])
        {
        	$htmlcontent .= '
        	<tr>
        		<td>Дополнительные IP адреса</td>
        		<td></td>
        		<td>' . $data['ip_price'] . '</td>
        		<td></td>
        		<td>' . $data['ip_price'] . '</td>
        	</tr>
        	';
        }

        $totalPrice += $data['ip_price'];

        $htmlcontent .= '
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: ' . $totalPrice . '$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>
        <br />
        ';

        return $htmlcontent;
    }

    private function getCollacationServiceProtocol($data)
    {
    	$services = explode('.', $data['discountcomment']);

        $string_content = "";
        foreach($services as $item)
        {
                $string_content .= $item. "<br />";
        }
        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Перечень
            </td>
            <td>
            	Скорость
            </td>
            <td>
                Сумма списания (единоразовая), USD
            </td>
            <td>
                Абонентская плата в месяц, USD
            </td>
        </tr>
        <tr>
            <td>
                '.$string_content.'
            </td>
            <td>
            	' . $data['speed'] . ' Кбит/с
            </td>
            <td>
                ' . $data['reg_pay'] . '
            </td>
            <td>
                '. $data['tarif_price'] .'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$data['tarif_price'].'$, Скидка: '. ($data['discount'] ? $data['discount'] : '0') .'%
            </td>
        </tr>
        </table>
        <br />
        ';
        return $htmlcontent;
    }

    private function getTradtelServiceProtocol($data)
    {
        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Услуги при подключении
            </td>
            <td>
                Стоимость услуги, USD
            </td>
            <td>
                Скидка на услуги при подключении, %
            </td>
            <td>
                Стоимость услуги при подключении с учетом скидки, USD
            </td>
        </tr>
        <tr>
            <td>
                Регистрационная плата за выделение телефонного номера
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
            <td>
                '.$data['discount'].'
            </td>
            <td>
                '.$data['reg_pay'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого за подключение: '.$data['reg_pay'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Переодические услуги
            </td>
            <td>
                Стоимость услуги, USD
            </td>
        </tr>
        <tr>
            <td>
                Абонентская плата за телефонный номер индивидуального пользования в месяц
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
        </tr>
        </table>
                <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$data['tarif_price'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>
        <br />
        ';
        return $htmlcontent;
    }

    private function getIsdnServiceProtocol($data)
    {

        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Услуги при подключении
            </td>
            <td>
                Стоимость услуги, USD
            </td>
            <td>
                Скидка на услуги при подключении, %
            </td>
            <td>
                Стоимость услуги при подключении с учетом скидки, USD
            </td>
        </tr>
        <tr>
            <td>
                Регистрационная плата за предоставление доступа к сети общего пользования с выделением цифрового
                тракта PRA ISDN (30B+D)
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
            <td>
                '.$data['discount'].'
            </td>
            <td>
                '.$data['reg_pay'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого за подключение: '.$data['reg_pay'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                Переодические услуги
            </td>
            <td>
                Стоимость услуги, USD
            </td>
        </tr>
        <tr>
            <td>
                Ежемесячная арендная плата за цифровой тракт PRA ISDN
            </td>
            <td>
                240
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: 240$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>
        <br />
        ';

        return $htmlcontent;
    }

}
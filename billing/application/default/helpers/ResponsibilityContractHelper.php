<?php
require_once ('PdfHelper.php');


class ResponsibilityContractHelper extends PdfHelper
{
    public function __construct()
    {

    }

    public function getServiceResp($serviceType, $data)
    {
        switch ($serviceType)
        {
            //case 'adsl':
            //    return $this->getAdslServiceProtocol($data);

            //case 'vpn':
            //    return $this->getVpnServiceProtocol($data);

            case 'hosting':
                return $this->getHostingResponsibilityService($data);

            //case 'tasix':
            //    return $this->getTasixServiceProtocol($data);

            case 'ngn':
                return $this->getNgnResponsibilityService($data);

            //case 'wifi':
            //    return $this->getWifiServiceProtocol($data);

            case 'collacation':
                return $this->getCollacationResponsibilityService($data);

            //case 'tradtel':
            //    return $this->getTradtelServiceProtocol($data);

            //case 'isdn':
            //    return $this->getIsdnServiceProtocol($data);

            default:
                return '';
        }
    }

    private function getHostingResponsibilityService($data)
    {
        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                <b style="font-family:arialbd">
					Исполнитель
				</b>
            </td>
            <td>
				<b style="font-family:arialbd">
            		Заказчик
				</b>
            </td>
        </tr>
        <tr>
            <td>
				1. Установка и настройка виртуального web-сервера ЗАКАЗЧИКА в сети Интернет <br />
				2. Обеспечение работы виртуального web-сервера ЗАКАЗЧИКА круглосуточно в течение
				   семи дней в неделю <br />
				3. Обеспечение Заказчика необходимой документацией для администрирования виртуального
				   web-сервера <br />
            </td>
            <td>
                1. Размещение и содержание информации на web-сервере <br />
				2. Соблюдение правил предоставления web-хостинга: <br />
					2.1. соблюдение законов <br />
					2.2. соблюдение прав третьих лиц <br />
                    2.3. соблюдение этики поведения в сети Интернет <br />
					2.4. правильное использование ресурсов серверов <br />
				3. Соблюдение норм пользования сетью: <br />
				       3.1. ограничение на информационный шум (СПАМ) <br />
				       3.2. запрет несанкционированного доступа и сетевых атак <br />
				       3.3. соблюдение правил установленных владельцами ресурсов <br />
				       3.4. недопустимость фальсификации <br />
					   3.5. настройка собственных ресурсов <br />
				4. Любые затраты или ущерб, прямо или косвенно возникшие в результате использования
					услуги web-хостинга <br />
            </td>
        </tr>
        </table>
		<br />
		<br />
		<br />
		<br />
        <table border="0" width="100%">
        <tr>
            <td>
                <b style="font-family:arialbd">
					Исполнитель
				</b>
            </td>
            <td>
                <b style="font-family:arialbd">
					Заказчик
				</b>
            </td>
        </tr>
        <tr>
            <td>
                <b style="font-family:arialbd">
					______________________
				</b>
            </td>
            <td>
                <b style="font-family:arialbd">
					______________________
				</b>
            </td>
        </tr>
        </table>
        <br />
        ';
        return $htmlcontent;
    }

    private function getNgnResponsibilityService($data)
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

    private function getCollacationResponsibilityService($data)
    {
        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td>
                <b style="font-family:arialbd">
					Исполнитель
				</b>
            </td>
            <td>
				<b style="font-family:arialbd">
            		Заказчик
				</b>
            </td>
        </tr>
        <tr>
            <td>
				1. Предоставление физического места в
				   шкафу Исполнителя для сервера Заказчика <br />
				2. Обеспечение круглосуточного электропитания и охраны сервера
				   Заказчика <br />
				3. Вынос и внос сервера Заказчика при
				   соответствующей заявке Заказчика <br />
				4. Физическая перезагрузка сервера Заказчика в течение, рабочего
				   дня Исполнителем при соответствующей заявке Заказчика <br />
				5. Предоставление Ethernet-порта на коммутаторе Исполнителя <br />
				6. Выделение 1 фиксированного IP-адреса <br />
				7. Поддержка обратной зоны DNS <br />
				8. Предоставление доступа в сеть TASIX <br />
            </td>
            <td>
				1. Работоспособность сервера Заказчика, в т.ч. комплектующих сервера <br />
				2. Работоспособность программного обеспечения и соблюдение политик
				   безопасности сервера Заказчика <br />
				3. Обслуживание и ремонт сервера Заказчика <br />
            </td>
        </tr>
        </table>
		<br />
		<br />
		<br />
		<br />
        <table border="0" width="100%">
        <tr>
            <td>
                <b style="font-family:arialbd">
					Исполнитель
				</b>
            </td>
            <td>
                <b style="font-family:arialbd">
					Заказчик
				</b>
            </td>
        </tr>
        <tr>
            <td>
                <b style="font-family:arialbd">
					______________________
				</b>
            </td>
            <td>
                <b style="font-family:arialbd">
					______________________
				</b>
            </td>
        </tr>
        </table>
        <br />
        ';
        return $htmlcontent;
    }
}
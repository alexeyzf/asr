<?php
require_once ('TradtelModel.php');
require_once ('AdslModel.php');
require_once ('NgnModel.php');

class AdditionalHelper
{
    private static function getNumbers($id, $tablelink)
    {
        /**
        *  Возвращаем весь диапазон номеров и диапазоны так называемых прямых проводов (Номера)
        */
        $constructor = ucfirst($tablelink). 'Model';
        $model = new $constructor();
        $result = $model->getAdditionalInfo($id);

        $concat_symbol = '';
        foreach($result['numbers']['numbers'] as $key => $value)
        {
            $str_range                 .= $concat_symbol. $value['number'];
            $str_range_directLine   .= $concat_symbol. $value['directline_number'];
            $concat_symbol = ', ';
        }
        $arr['numbers']     = $str_range;
        $arr['directlines'] = $str_range_directLine;
        return $arr;
    }

    public static function adslAdditional($data)
    {
        /**
        *  Метод рисует таблицу для ADSL
        */

		if($data['not_garant'] != "")
		{
			$data['speed'] = $data['not_garant'];
		}

        $str .= '
        <table border="1" width="100%"  cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Характеристики услуги</b>
            </td>
        </tr>
        <tr>
            <td>
                Требуемая скорость порта
            </td>
            <td>
                '.$data['speed'].' Кбит/с
            </td>
        </tr>
        <tr>
            <td>
                Требуемый лимит трафика
            </td>
            <td>
                '.$data['traffic'].'
            </td>
        </tr>
        <tr>
            <td>
                Место включения в сеть ООО "Sharq Telekom"
            </td>
            <td>
                empty
            </td>
        </tr>
        <tr>
            <td>
                Интерфейс на порту
            </td>
            <td>
                ADSL
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%"  cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Маршрутизация и IP-адресация</b>
            </td>
        </tr>
        <tr>
            <td>
                Тип маршрутизации
            </td>
            <td>
                Динамическая
            </td>
        </tr>
        <tr>
            <td>
                Имеющиеся IP-адреса
            </td>
            <td>
                empty
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%"  cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Начальные идентификационные данные</b>
            </td>
        </tr>
        <tr>
            <td>
                Логин
            </td>
            <td>
                '.$data['u_login'].'
            </td>
        </tr>
        <tr>
            <td>
                Пароль
            </td>
            <td>
                '.$data['u_passwd'].'
            </td>
        </tr>
        <tr>
            <td>
                Телефон кроссировки
            </td>
            <td>
                '.$data['pcross'].'
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Пароль небезопасный! Сменить при активировании услуг!
            </td>
        </tr>
        </table>
        ';
        return $str;
    }

    public static function collacationAdditional($data)
    {
        if ($data['unlimit'])
        {
            $limit = 'нелимитированный';
        }
        elseif ($data['limit'])
        {
            $limit = $data['limit'] . ' МБ';
        }


        /**
        *  Метод рисует таблицу для COLLACATION
        */
        $str .= '
        <table border="1" width="506" style="font-family: arial" cellpadding="2">
            <tr>
                <td colspan=2><b style="font-family:arialbd">Характеристики услуги</b></td>
            </tr>
            <tr>
                <td>Подключение к 100 Mb/s порту</td>
                <td>да</td>
            </tr>
            <tr>
                <td>Текущий объем трафика</td>
                <td>' . $limit .'</td>
            </tr>
            <tr>
                <td>IP-адрес</td>
                <td>' . $data['ip_address'] .'</td>
            </tr>
            <tr>
                <td>Скорость подключения на порту</td>
                <td>'. $data['speed'] .' Кбит/с</td>
            </tr>
        </table>
        ';
        return $str;
    }

    public static function hostingAdditional($data)
    {
        /**
        *  Метод для услуги HOSTING
        */

        $str .= '
        <table border="1" width="100%"  cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Характеристики услуги</b>
            </td>
        </tr>
        <tr>
            <td>
                Доменное имя
            </td>
            <td>
                '.$data['domain_addres'].'
            </td>
        </tr>
        <tr>
            <td>
                IP адреса
            </td>
            <td>
                '.$data['ip_address'].'
            </td>
        </tr>
        <tr>
            <td>
                Дисковое пространство
            </td>
            <td>
                '.$data['limit'].' мб
            </td>
        </tr>
        </table>
        ';
        return $str;
    }

    public static function ngnAdditional($data)
    {
        /**
        *  Метод для услуги HOSTING
        */
        $str_range = AdditionalHelper::getNumbers($data['id'], $data['tablelink']);

        if($data['foreign_access'] == "1")
        {
            $foreign_status = 'Доступ открыт';
        }
        else
        {
            $foreign_status = 'Нет доступа';
        }
        $str .= '
        <table border="1" width="100%"  cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Характеристики услуги</b>
            </td>
        </tr>
        <tr>
            <td>
                Номера телефонов
            </td>
            <td>
                '.$str_range['numbers'].'
            </td>
        </tr>
        <tr>
            <td>
                IP адреса
            </td>
            <td>
                IP - '.$data['ip_address'].'<br />
                GW - '.$data['gw_address'].'<br />
                Mask - '.$data['mask'].'<br />
                VLan - '.$data['vlan'].'
            </td>
        </tr>
        <tr>
            <td>
                Доступ к международной связи
            </td>
            <td>
                '.$foreign_status.'
            </td>
        </tr>

        </table>

        <br />

        <table border="1" width="100%"  cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Точка подключения</b>
            </td>
        </tr>
        <tr>
            <td>
                Адрес предоставления услуги
            </td>
            <td>
                '.$data['connect_address'].'
            </td>
        </tr>
        </table>
        <br />
        ';
        return $str;
    }

    public static function tasixAdditional($data)
    {
        /**
        *  метод для услуги TAs-IX
        */

        $str .= '
        <table border="1" width="100%"  cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Характеристики услуги</b>
            </td>
        </tr>
        <tr>
            <td>
                IP адреса
            </td>
            <td>
                IP - '.$data['ip_address'].'<br />
                GW - '.$data['gw_address'].'<br />
                Mask - '.$data['mask'].'<br />
                VLan - '.$data['vlan'].'
            </td>
        </tr>
        <tr>
            <td>
                Cерийный номер модема
            </td>
            <td>
                '.$data['modem_serial'].'
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%"  cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Маршрутизация и IP-адресация</b>
            </td>
        </tr>
        <tr>
            <td>
                Тип маршрутизации
            </td>
            <td>
                Динамическая
            </td>
        </tr>
        <tr>
            <td>
                Имеющиеся IP-адреса
            </td>
            <td>
                empty
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%"  cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Начальные идентификационные данные</b>
            </td>
        </tr>
        <tr>
            <td>
                Логин
            </td>
            <td>
                '.$data['u_login'].'
            </td>
        </tr>
        <tr>
            <td>
                Пароль
            </td>
            <td>
                '.$data['u_passwd'].'
            </td>
        </tr>
        <tr>
            <td>
                Телефон кроссировки
            </td>
            <td>
                '.$data['pcross'].'
            </td>
        </tr>
        <tr>
            <td colspan="2">
                пароль небезопасный! Сменить при активировании услуг!
            </td>
        </tr>
        </table>
        ';
        return $str;
    }

    public static function wifiAdditional($data)
    {
        /**
        *  метод для услуги WiFI
        */
        $str .= '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Характеристики услуги</b>
            </td>
        </tr>
        <tr>
            <td>
                IP адреса
            </td>
            <td>
                IP - '.$data['ip_address'].'<br />
                GW - '.$data['gw_address'].'<br />
                Mask - '.$data['mask'].'<br />
                VLan - '.$data['vlan'].'
            </td>
        </tr>
        <tr>
            <td>
                Cерийный номер модема
            </td>
            <td>
                '.$data['modem_serial'].'
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Маршрутизация и IP-адресация</b>
            </td>
        </tr>
        <tr>
            <td>
                Тип маршрутизации
            </td>
            <td>
                Динамическая
            </td>
        </tr>
        <tr>
            <td>
                Имеющиеся IP-адреса
            </td>
            <td>
                empty
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Начальные идентификационные данные</b>
            </td>
        </tr>
        <tr>
            <td>
                Логин
            </td>
            <td>
                '.$data['u_login'].'
            </td>
        </tr>
        <tr>
            <td>
                Пароль
            </td>
            <td>
                '.$data['u_passwd'].'
            </td>
        </tr>
        <tr>
            <td>
                Телефон кроссировки
            </td>
            <td>
                '.$data['pcross'].'
            </td>
        </tr>
        <tr>
            <td colspan="2">
                пароль небезопасный! Сменить при активировании услуг!
            </td>
        </tr>
        </table>
        ';
        return $str;
    }

    public static function vpnAdditional($data)
    {
        /**
        *  Метод рисует таблицу для VPN
        */
        $str .= '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Характеристики услуги</b>
            </td>
        </tr>
        <tr>
            <td>
                Требуемая скорость порта
            </td>
            <td>
                '.$data['speed'].'
            </td>
        </tr>
        <tr>
            <td>
                Требуемый лимит трафика
            </td>
            <td>
                '.$data['traffic'].'
            </td>
        </tr>
        <tr>
            <td>
                Место включения в сеть ООО "Sharq Telekom"
            </td>
            <td>
                empty
            </td>
        </tr>
        <tr>
            <td>
                Интерфейс на порту
            </td>
            <td>
                ADSL
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Маршрутизация и IP-адресация</b>
            </td>
        </tr>
        <tr>
            <td>
                Тип маршрутизации
            </td>
            <td>
                Динамическая
            </td>
        </tr>
        <tr>
            <td>
                Имеющиеся IP-адреса
            </td>
            <td>
                empty
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Начальные идентификационные данные</b>
            </td>
        </tr>
        <tr>
            <td>
                Логин
            </td>
            <td>
                '.$data['u_login'].'
            </td>
        </tr>
        <tr>
            <td>
                Пароль
            </td>
            <td>
                '.$data['u_passwd'].'
            </td>
        </tr>
        <tr>
            <td>
                Телефон кроссировки
            </td>
            <td>
                '.$data['pcross'].'
            </td>
        </tr>
        <tr>
            <td colspan="2">
                пароль небезопасный! Сменить при активировании услуг!
            </td>
        </tr>
        </table>
        ';
        return $str;
    }

    public static function tradtelAdditional($data)
    {

        $str_range = AdditionalHelper::getNumbers($data['id'], $data['tablelink']);

        if($data['foreign_access'] == "1")
        {
            $foreign_status = 'Доступ открыт';
        }
        else
        {
            $foreign_status = 'Нет доступа';
        }

        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Характеристики услуги</b>
            </td>
        </tr>
        <tr>
            <td>
                Номер телефона
            </td>
            <td>
                '.$str_range['numbers'].'
            </td>
        </tr>
        <tr>
            <td>
                Место включения в сеть ООО "Sharq Telekom"
            </td>
            <td>
                порт:  / рамка:
            </td>
        </tr>
        <tr>
            <td>
                Доступ к МГ и МН связи
            </td>
            <td>
                '.$foreign_status.'
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Точка подключения</b>
            </td>
        </tr>
        <tr>
            <td>
                Адрес предоставления услуги
            </td>
            <td>
                '.$data['connect_address'].'
            </td>
        </tr>
        <tr>
            <td>
                Номер прямого провода Заказчика
            </td>
            <td>
                '.$str_range['directlines'].'
            </td>
        </tr>
        </table>
        ';

        return $htmlcontent;
    }

    public static function isdnAdditional($data)
    {
        $str_range = AdditionalHelper::getNumbers($data['id'], $data['tablelink']);

        if($data['foreign_access'] == "1")
        {
            $foreign_status = 'Доступ открыт';
        }
        else
        {
            $foreign_status = 'Нет доступа';
        }

        $htmlcontent = '
        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Характеристики услуги</b>
            </td>
        </tr>
        <tr>
            <td>
                Диапозон предоставленных DDI номеров
            </td>
            <td>
                '.$str_range['numbers'].'
            </td>
        </tr>
        <tr>
            <td>
                Количество одновременных голосовых трактов
            </td>
            <td>
                30
            </td>
        </tr>
        <tr>
            <td>
                Место включения в сеть ООО "Sharq Telekom"
            </td>
            <td>
                порт:  / рамка:
            </td>
        </tr>
        <tr>
            <td>
                Доступ к МГ и МН связи
            </td>
            <td>
                '.$foreign_status.'
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%" cellpadding=2>
        <tr>
            <td colspan="2">
                <b style="font-family:arialbd">Точка подключения</b>
            </td>
        </tr>
        <tr>
            <td>
                Адрес предоставления услуги
            </td>
            <td>
                '.$data['connect_address'].'
            </td>
        </tr>
        <tr>
            <td>
                Номер прямого провода Заказчика
            </td>
            <td>
                '.$str_range['directlines'].'
            </td>
        </tr>
        </table>
        ';
        return $htmlcontent;
    }
}
?>
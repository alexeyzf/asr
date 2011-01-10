<?php
class protocolHelper
{
    public static function adslProtocol($data)
    {

        $htmlcontent .= '
        <table border="1" width="100%">
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
                Скидка на услуги при подключении
            </td>
            <td>
                Стоимость услуги при подключении с учетом скидки, USD
            </td>
        </tr>
        <tr>
            <td>---</td>
            <td>
                ADSL
            </td>
            <td>
                ' . ($data['modem_serial'] ? 'Без модема' : 'Свой модем') . '
            </td>
            <td>
                ' . ($data['modem_serial'] ? 'В аренду' : 'Свой модем') . '
            </td>
            <td>
                '.$data['discount'].'
            </td>
            <td>
                '.$data['reg_pay_total'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого за подключение: '.$data['reg_pay_total'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        <br />

        <table border="1" width="100%">
        <tr>
            <td>
                Периодические услуги
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
                0%
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$data['reg_pay'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%">
            <tr>
                <td>Дополнительные услуги</td>
                <td>Стоимость 1МБ, USD</td>
                <td>Скидка %</td>
                <td>Стоимость 1МБ с учетом скидки, USD</td>
            </tr>
        ';
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

        $htmlcontent .= '
        </table>
        ';

        return $htmlcontent;
    }

    public static function vpnProtocol($data)
    {
        $htmlcontent = '
        <table border="1" width="100%">
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

        <table border="1" width="100%">
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
                ADSL
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
                '.$data['reg_pay_total'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого за подключение: '.$data['reg_pay_total'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%">
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
                0%
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$data['tarif_price'].'$, Скидка: 0%
            </td>
        </tr>
        </table>
        ';

        return $htmlcontent;
    }

    public static function hostingProtocol($data)
    {
        $htmlcontent = '
        <table border="1" width="100%">
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
                Поддержка DNS в других зонах, USD
            </td>
            <td>
                Оплата в месяц, USD
            </td>
            <td>
                Итого оплата, USD
            </td>
        </tr>
        <tr>
            <td>
                '.$data['tarif_name'].'
            </td>
            <td>
                '.$data['limit'].'
            </td>
            <td>
                Нет
            </td>
            <td>
                '.$data['traffic'].'
            </td>
            <td>
                Да
            </td>
            <td>
                '.$data['tarif_price'].'
            </td>
            <td>
                '.$data['reg_pay'].'
            </td>
        </tr>
        </table>
        ';

        return $htmlcontent;
    }

    public static function tasixProtocol($data)
    {
        $htmlcontent = '
        <table border="1" width="100%">
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

        <table border="1" width="100%">
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
                ADSL
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

        <table border="1" width="100%">
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
                Итого ежемесячная абонентская плата: '.$data['reg_pay'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>
        ';

        return $htmlcontent;
    }

    public static function ngnProtocol($data)
    {
        $htmlcontent = '
        <table border="1" width="100%">
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
                '.$data['reg_pay_total'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого за подключение: '.$data['reg_pay_total'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%">
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
                '.$data['tarif_price'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$data['tarif_price'].'$, Скидка: 0%
            </td>
        </tr>
        </table>

        <br />

        <table border="1" width="100%">
        <tr>
            <td>
                Дополнительные услуги
            </td>
            <td>
                Стоимость дополнительной услуги, USD
            </td>
        </tr>
        <tr>
            <td>
                Вызов специалиста для установки и настройки оборудования
            </td>
            <td>
                '.$data['traffic_excess'].'
            </td>
        </tr>
        </table>
        <br />
        ';

        return $htmlcontent;
    }

    public static function wifiProtocol($data)
    {
        $htmlcontent = '
        <table border="1" width="100%">
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

        <table border="1" width="100%">
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
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$data['reg_pay'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>
        <br />
        ';

        return $htmlcontent;
    }

    public static function collacationProtocol($data)
    {
		//var_dump($data);
		//exit();
        $htmlcontent = '
        <table border="1" width="100%">
        <tr>
            <td>
                Перечень
            </td>
            <td>
                Сумма списания (единоразовая)
            </td>
            <td>
                Абонентская плата в месяц, USD
            </td>
        </tr>
        <tr>
            <td>
                '.$data['discountcomment'].'
            </td>
            <td>
                '.$data['reg_pay'].'
            </td>
            <td>
                '.$data['tarif_price'].'
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

    public static function tradtelProtocol($data)
    {
        $htmlcontent = '
        <table border="1" width="100%">
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

        <table border="1" width="100%">
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
                Итого ежемесячная абонентская плата: '.$data['reg_pay'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>
        <br />
        ';
        return $htmlcontent;
    }

    public static function isdnProtocol($data)
    {
        $htmlcontent = '
        <table border="1" width="100%">
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

        <table border="1" width="100%">
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
                '.$data['tarif_price'].'
            </td>
        </tr>
        </table>
        <table border="0" width="100%">
        <tr>
            <td>
                Итого ежемесячная абонентская плата: '.$data['reg_pay'].'$, Скидка: '.$data['discount'].'%
            </td>
        </tr>
        </table>
        <br />
        ';

        return $htmlcontent;
    }
}
?>
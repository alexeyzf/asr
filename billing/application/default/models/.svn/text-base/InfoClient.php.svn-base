<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('Zend/Db/Table.php');

class InfoClient extends Zend_Db_Table
{
    protected $_name = 'clients';

    protected $_sequence = 'clients_seq';

    public function getClientInfo($clientID)
    {
        $query = "
        select
        ---------------------------------------Общ. данные-------------------
        CLA.client_id,
        --ID клиента

        CLA.client_type_id as cla_type,
        -- Ти лица

        PTS.contact_name, --Контактное лицо для данной т.

        PTS.connect_address, -- адрес подключ.

        (select typename_id from asrtypes
        where typename_value = '7' and typename_id = CLA.client_type_id ) as client_type,
        --Тип клиента (Тип клиента !!!!НЕ НОМЕР)

        (select typename from asrtypes
        where typename_value = '2' and typename_id = CLA.ruvd_id) as ruvd_name,
        --Наим. РУВД

        (select typename from asrtypes
        where   typename_value = '4' and typename_id = CLA.boss_id) as boss,
        --Кто подписал (Имя нальника)

        (select typename from asrtypes
        where   typename_value = '1' and typename_id = PTS.country_id) as town,
        --Город typename (country_name)

        (select typename from asrtypes
        where   typename_value = '3' and typename_id = CLA.bank_id) as bank_name,
        --Наим. Банка typename (bank_name)

        (select typename_id from asrtypes
        where   typename_value = '1' and typename_id = PTS.country_id) as country_id,
        --Город typename_id (country_id)

        (select first_name ||' '|| last_name from users
        where   id = CNTR.manager_id ) as manager,
        --Имя Менеджер (manager_full_name)

        (select phone from users
        where   id = CNTR.manager_id ) as manager_contact_tel,
        --Имя Менеджер (manager_contact_tel)

        (select id from users
        where   id = CNTR.manager_id ) as manager_id,
        --Менеджер (manager_id)

        (select typename from asrtypes
        where typename_value = '7' and typename_id = CLA.client_type_id) as client_type_name,
        --Тип клиента (Юрик, физик и т.д.ы)

        CLA.client_name, -- Имя клиента
        CLA.client_id, -- ИД клиента
        CLA.address, --Адресс прописки
        CLA.phone, --Телефон для свя склиентом
        CLA.fax, --Факс клиента
        CLA.email, --Email клиента
        CLA.passp_n, -- номер паспорта
        CLA.legaladdress, -- юр адрес
        CLA.currency, -- Валюта
        CLA.inn, -- INN
        CLA.mfo, -- MFO
        CLA.okonx, -- OKONX
        CLA.bank_id, -- ID банка
        CNTR.dateagree, -- Дата подпсаиня
        CNTR.contract_id, --ID контракта
        CNTR.contract_number, --Номер контракта для документов
        CLA.client_orient, --Ориентаир клиент
        CLA.imoney,
        CLA.omoney,
        CLA.ballance,
        PTS.point_id, -- ID главнойй точки
        PTS.pcross, --  Телефон кроссирофвки для главной точки
        PTS.phone as ptsphone -- телефон для выставления
        ---------------------------------------Общ. данные конец---------------
            from clients as CLA, contracts as CNTR,
            points as PTS
            where
            CLA.client_id = CNTR.client_id
            and
            CLA.client_id = PTS.client_id
            and
            CNTR.contract_type_id = 1
            and
            CLA.client_id = '{$clientID}'
        ";

         return $this->_db->fetchAll($query);
    }

    public function getPointInfo($client_id)
    {
        /*
        * Метод данной модели возвращает информацию о точке
        */
        $sql ="
            select
            PTS.*,
            CLA.*,
            CNTR.*,
            PTS.phone as ptsphone,

            (select typename from asrtypes
            where typename_value = '7' and typename_id = CLA.client_type_id) as client_type,
            -- Тип клиента

            (select typename_id from asrtypes
            where typename_value = '7' and typename_id = CLA.client_type_id) as client_type_id,
            -- ID Тип клиента

            (select typename from asrtypes
            where typename_value = '1' and typename_id = PTS.country_id) as town,
            -- Наим. города

            (select typename from asrtypes
            where typename_value = '3' and typename_id = CLA.bank_id) as bank_name,
            -- Наим. банка

            (select typename from asrtypes
            where typename_value = '2' and typename_id = CLA.ruvd_id) as ruvd_name,
            -- Наим. РУВД

            (select first_name ||' '|| last_name from users
            where id = CNTR.manager_id) as manager_full_name
            -- Менеджер

            from points as PTS, clients as CLA, contracts as CNTR
            where
                PTS.client_id = CLA.client_id
            and
                CLA.client_id = CNTR.client_id
            and
                CLA.client_id = {$client_id}
        ";
        return $this->_db->fetchAll($sql);
    }

    public function dateGenerator($need_data)
    {
        $sql = "
            SELECT (month('{$need_data}'));
        ";
        return $this->_db->fetchRow($sql);
    }

    public function rschetClient($client_id)
    {
        $sql = "
            select
                    schet  --Сам номер счета
            from rschet
            where
                    client_id = '{$client_id}'
        ";
        return $this->_db->fetchAll($sql);
    }

    public function selectCountryPrefix($country)
    {
        $sql = "
            select typename_desc from asrtypes
            where
                typename_value = '1'
            and
                typename_id = '{$country}';
        ";
        return $this->_db->fetchAll($sql);
    }


    public function selectClientName($point_id)
    {
        $sql = "
        select * from clients as CLA, points as PTS
        where
            PTS.point_id = {$point_id}
        and
            PTS.client_id = CLA.client_id
        ";
        return $this->_db->fetchRow($sql);
    }

    public function showServices($client_id, $tablename = NULL, $distinct = null)
    {
        $sql = "
        select
            PTS.*,
            COLLA.*,
            (select tarif_name from tarifs where
                tarif_id = COLLA.tarif_id
            ) as tarif_name,

            (select tarif_price from tarifs where
                tarif_id = COLLA.tarif_id
            ) as tarif_price,

            (select servicetype_name from service_type, tarifs
            where
                service_type.servicetype_id = tarifs.servicetype_id
                and
                tarifs.tarif_id = COLLA.tarif_id
            ) as servicetype_name,

            (select short_name from service_type, tarifs
            where
                service_type.servicetype_id = tarifs.servicetype_id
                and
                tarifs.tarif_id = COLLA.tarif_id
            ) as short_name,

            (select ss.servicetype_id from service_type ss, tarifs as tt
            where
                ss.servicetype_id = tt.servicetype_id
                and
                tt.tarif_id = COLLA.tarif_id
            ) as servicetype_id,

            (select label from point_statuses_view as PSV
            where
            PSV.code = PTS.statuscross) as label

        from
            {$tablename} as COLLA
            left join points as PTS on(COLLA.point_id = PTS.point_id)
        where
            PTS.client_id = {$client_id}
        and
            current_date between COLLA.startdate and COLLA.enddate
        and
            COLLA.enddate > current_date
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getClientName($clientID)
    {
        $clientID = intval($clientID);

        if ( ! $clientID )
        {
            return null;
        }

        return $this->fetchRow("client_id = {$clientID}")->client_name;
    }

    public function getDeliveryInvoice($client_id, $point_id)
    {
        $sql = "
            select * from additional_services where point_id = {$point_id} and client_id = {$client_id}
            and
                current_date between startdate and enddate
            and
                enddate > current_date
        ";
        return $this->_db->fetchAll($sql);
    }

    public function setDeliveryInvoice($data)
    {
            $startdate = $data['startdate_year']. $data['startdate_month']. $data['startdate_day'];
            $enddate = $data['enddate_year']. $data['enddate_month']. $data['enddate_day'];

            if($data['penable'] == 1)
            {
                $state = 'true';
            }
            else
            {
                $state = 'false';
            }

            $sql = "
                select delivery_invoices
                    (
                        {$data['tarif_id']},
                        {$data['point_id']},
                        '{$startdate}',
                        '{$enddate}',
                        {$state},
                        {$data['client_id']},
                        {$data['userid']}
                    )
            ";

            return $this->_db->fetchAll($sql);
    }

    public function deleteDeliveryInvoice($data)
    {
        $sqlDelete = "
        delete from additional_services where client_id = {$data['client_id']}
        ";
        return $this->_db->fetchAll($sqlDelete);
    }
}


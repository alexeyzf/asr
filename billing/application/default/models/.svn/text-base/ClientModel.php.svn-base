<?php
/**
 * Model of clients table
 */

require_once('Zend/Db/Table.php');
require_once('TarifListModel.php');
require_once('AdminUser.php');
require_once('Porttasks.php');
require_once('AsrHelp.php');

class ClientModel extends Zend_Db_Table
{
    protected $_name = 'clients';
    protected $_sequence = 'clients_seq';

    const CLIENT_TYPE_JURIDICAL = 0;
    const CLIENT_TYPE_PHYSICAL = 1;

	protected $_EXCHEQUER_ACCOUNT = '23402000400100001006'; // Р.с казначейства

	public function getCityName($city_id)
	{
		$sql = "
			select typename from asrtypes
			where
				typename_value = '1'
			and
				typename_id = {$city_id}
		";
		return $this->_db->fetchOne($sql);
	}

	public function getBankName($bank_id)
	{
		$sql = "
			select typename from asrtypes
			where
				typename_value = '0'
			and
				typename_id = {$bank_id}
		";
		return $this->_db->fetchOne($sql);
	}

	public function verifyPcross($pcross, $country_id)
	{
		$pcross = trim($pcross);
		$sql = "
		select * from points as PTS
		where
			 PTS.pcross = '{$pcross}'
		and
			 PTS.country_id = {$country_id}
		";

		$data = $this->_db->fetchAll($sql);


		if(count($data) > 0)
		{
			return 'bad';
		}
	}

	public function verifyArhivPcross($pcross, $countryID)
	{
		$pcross = trim($pcross);

		$sql = "
			SELECT
				client_id
			FROM
				points_arhiv
			WHERE
				pcross = '{$pcross}'
				AND country_id = {$countryID}
		";

		$clientID = $this->_db->fetchOne($sql);

		if ($clientID)
		{
			$clientSql = "
				SELECT
					*
				FROM
					clients_arhiv
				WHERE
					client_id = {$clientID}
			";

			return $this->_db->fetchRow($clientSql);
		}
		else
		{
			return false;
		}
	}

	public function verifyInn($inn)
	{
		$inn = trim($inn);

		if ( ! $inn )
		{
			return false;
		}

		$sql = "
			SELECT
				client_id
			FROM
				clients
			WHERE
				inn = '{$inn}'
		";

		if ($this->_db->fetchOne($sql))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function verifyArhivInn($inn)
	{
		$inn = trim($inn);

		if ( ! $inn )
		{
			return false;
		}

		$sql = "
			SELECT
				*
			FROM
				clients_arhiv
			WHERE
				inn = '{$inn}'
		";

		$oldClient = $this->_db->fetchRow($sql);

		if ($oldClient && $oldClient['client_id'])
		{
			return $oldClient;
		}
		else
		{
			return false;
		}
	}

	public function getArhivClient($clientID)
	{
		$sql = "
			SELECT
				*
			FROM
				clients_arhiv
			WHERE
				client_id = {$clientID}
		";

		return $this->_db->fetchRow($sql);
	}

    public function getClientByID($clientID)
    {
        $clientID = intval($clientID);

        if ( ! $clientID )
        {
            return array();
        }

        $client = $this->fetchRow("client_id = {$clientID}");

        if ( ! $client )
        {
        	return array();
        }

        $client = $client->toArray();
		$client['rschets'] = $this->rschetClient($client['client_id']);
        $asrHelper = new AsrHelp();

        $client['town'] = $asrHelper->getAsrTypeName(AsrHelp::CITY_TYPE, $client['town']);
        $client['bank'] = $asrHelper->getAsrTypeName(AsrHelp::BANK_TYPE, $client['bank_id']);

        return $client;
    }

    public function getClientIDfromPointID($pointID)
    {
    	$sql = "
			select client_id from points where point_id = {$pointID} limit 1
		";
		return $this->_db->fetchOne($sql);
    }

	/**
     * Check account for client
     *
     * @param string $account - Account to check
     * @param int $clientID - Client ID
     * @return int Client ID
     */
    public function getClientByAccount($account, $clientID = 0, $arr = null)
    {
    	$prefix = substr($account, 0, 5);

		if($prefix == "23402")
		{

			// Значит оплата пришла от казначейства
			$clientID = $this->parseAccount($arr['notes']);

			if(!$clientID)
			{
				return;
			}
			else
			{
				return $clientID;
			}
		}
		else
		{
	    	$sql = "
	    		SELECT
	    			clients.client_id AS id
	    		FROM
	    			rschet
	    		JOIN
	    			clients ON clients.client_id = rschet.client_id
	    		WHERE
	    			schet='{$account}'";


	    	if ($clientID)
	    	{
	    		$sql .= " AND client_id = {$clientID} ";
	    	}
		}
    	return $this->_db->fetchOne($sql);

    }

    public function parseAccount($str)
    {
		//$resultSTR = iconv('cp1251','UTF8', $str);
		$resultSTR = $str;

		$sql_all = "
			select
				R.*,
				C.contract_number,
				CLA.inn
			from rschet as R, contracts as C, clients as CLA
			where
				R.schet = '{$this->_EXCHEQUER_ACCOUNT}'
			and
				R.client_id = C.client_id
			and
				C.client_id = CLA.client_id
		";

		$dataRows = $this->_db->fetchAll($sql_all);

		$sqlSchets = "select * from rschet order by client_id";
		$allRschets = $this->_db->fetchAll($sqlSchets);

		foreach($dataRows as $item)
		{
			$resultBoolean = strpos($resultSTR, $item['inn']);

			if($resultBoolean)
			{
				return $item['client_id'];
			}
			else
			{
				foreach($allRschets as $lines)
				{
					$resultBooleanShet = strpos($resultSTR, $lines['schet']);

					if($resultBooleanShet)
					{
						return $lines['client_id'];
					}
				}
			}
		}

    }

    public function getClientIDByINN($inn)
    {
		$sql = "
			select client_id from clients where inn = '{$inn}'
		";


		return $this->_db->fetchOne($sql);
    }

    public function getClientByInn($inn)
    {
    	return $this->fetchRow("inn = '{$inn}'");
    }

    public function getClientByRschetTest($rschet)
    {
		$sql = "
			select
				*
			from clients as CLA, rschet as R
			where
				CLA.client_id = R.client_id
			and
				R.schet = '{$rschet}'
			limit 1
		";

		return $this->_db->fetchRow($sql);

    }

    public function getClientByContract($contractN)
    {
    	$contract = trim($contractN);
		$sql = "
			select
				*
			from clients as CLA, rschet as R, contracts as C
			where
				CLA.client_id = R.client_id
			and
				CLA.client_id = C.client_id
			and
				C.contract_number = '{$contract}'
			limit 1
		";

		return $this->_db->fetchRow($sql);

    }

    public function searchToPaginatorDbAdapter($param, $value)
    {

    	if($param == "clients.client_id")
    	{
			$where = " {$param} = {$value}";
    	}
    	else
    	{
    		$where = "LOWER({$param}) like LOWER('%{$value}%')";
    	}


    	$dbSelect = $this->_db->select()
    		->from('clients', array(
    						'client_id',
    						'client_name', 'ballance'))
    		->joinLeft('points AS points', 'points.client_id = clients.client_id', array(
    						'point_id',
    						'u_login',
    						'phone AS contact_phone',
    						'pcross AS cross_phone',
    						'connect_address'))
    		->joinLeft('ats_list AS ats_list', 'ats_list.id = points.ats_id', array(
    						'name AS ats'))
    		->joinLeft('ports AS ports', 'ports.id = points.port_id', array(
    						'state'))
    		->where($where)
    		->where('client_type_id != 4');

    	return new Zend_Paginator_Adapter_DbSelect($dbSelect);
    }

    /**
     * Searches and return list of clients where $param mathing $value
     *
     * @param string $param - criteria to search
     * @param string $value - values to match
     */
    public function search($param, $value, $limit = null, $offset = null)
    {
        $sql ="
            SELECT
                points.point_id,
                clients.client_id,
                clients.client_name,
                ats_list.name AS ats,
                points.u_login,
                points.phone AS contact_phone,
                points.pcross AS cross_phone,
                points.connect_address,
                ports.state
            FROM
                clients
            LEFT JOIN
                points ON points.client_id = clients.client_id
            LEFT JOIN
                ats_list ON ats_list.id = points.ats_id
            LEFT JOIN
                ports ON ports.id = points.port_id
            WHERE
                LOWER({$param}) like LOWER('%{$value}%')
        ";

        if ($limit)
        {
        	$sql .= " LIMIT {$limit} ";
        }

        if ($offset)
        {
        	$sql .= " OFFSET {$offset} ";
        }

        return $this->_db->fetchAll($sql);
    }

    /**
     * Gets cleint info - used in techcleint pages
     *
     * @param integer $pointID - Point ID to show infromation for
     */
    public function getInfo($pointID)
    {
        $pointID = intval($pointID);

        if ( ! $pointID )
        {
            return array();
        }

        $sql = "
            SELECT
                clients.client_id,
                clients.client_name,
                clients.client_type_id,
                clients.ballance,
                client_type.typename,
                points.point_id,
                points.connect_address,
                contracts.dateagree,
                contracts.manager_id,
                points.crossdate,
                points.u_login,
                points.country_id,
                points.pcross,
                points.notes,
                points.pcross_type,
                points.pcross_owner,
                points.ats_id,
                points.dslam_id,
                points.port_id,
                points.statuscross,
                point_statuses_view.label AS statuscross_label,
                points.engineer_id,
                ports.number AS port_number,
                ports.frame_number AS frame_number,
                ports.line_number1,
                ports.line_number2,
                ports.status,
                ports.state AS port_state,
                dslam_list.ip_address AS dslam_ip,
                dslam_list.name AS dslam_name,
                ats_list.name AS ats_name,
                ats_list.phone_hub_id,
                (select tablename from client_services where u_login = points.u_login and points.point_id = {$pointID} limit 1 ) as tablename
            FROM
                clients AS clients
            LEFT JOIN
                points AS points ON points.client_id = clients.client_id
            LEFT JOIN
                asrtypes AS client_type ON client_type.typename_id = clients.client_type_id
                    AND client_type.typename_value = '7'
            LEFT JOIN
                point_statuses_view ON point_statuses_view.code = points.statuscross
            LEFT JOIN
                contracts AS contracts ON contracts.client_id = clients.client_id
            LEFT JOIN
                ports ON ports.id = points.port_id
            LEFT JOIN
                ats_list ON ats_list.id = points.ats_id
            LEFT JOIN
                dslam_list ON dslam_list.id = points.dslam_id
            WHERE
                points.point_id = {$pointID}
        ";

        $info = $this->_db->fetchRow($sql);
        $info['dateagree'] = date('d.m.y', strtotime($info['dateagree']));

        if ($info['crossdate'])
        {
            $info['crossdate'] = date('d.m.y', strtotime($info['crossdate']));
        }
        else
        {
            $info['crossdate'] = 'Не скроссирован';
        }

        $tarifListModel = new TarifListModel();
        $info['portspeed'] = $tarifListModel->getSpeed($pointID);

        $adminUser = new AdminUser();
        $info['manager_name'] = $adminUser->getUserFullName($info['manager_id']);
        $info['engineer_name'] = $adminUser->getUserFullName($info['engineer_id']);
        return $info;
    }

    public function getCrossServiceForPoint($pointID, $flag = null)
    {
    	$sql = "
    		SELECT
    			tablename,
    			service_id
    		FROM
    			client_services
    		WHERE
    			need_cross = 1
    			AND point_id = {$pointID}
    	";

    	$service = $this->_db->fetchRow($sql);

    	if ( ! $service['tablename'])
    	{
    		return array();
    	}

		if($flag == 1)
		{

		}
		else
		{
			// Обн. дату успеш. кросса
			$sqlUpdateCrossDate = "
				update points set crossdate = now() where point_id = {$pointID}
			";
			$this->_db->fetchRow($sqlUpdateCrossDate);
		}

    	$sql = "
    		SELECT
    			*
    		FROM
    			client_services
    		WHERE
    			service_id = {$service['service_id']}
    	";

    	return $this->_db->fetchRow($sql);
    }

    public function addRschet($clientID,$schet)
    {
        /**
        *  Метод записывает все расчетные счита клиентов в таблицу rschet
        */
        $sql = "insert into rschet (client_id, schet)
                values ('{$clientID}', '{$schet}')
        ";
        return $this->_db->fetchRow($sql);
    }

    public function varifyRschet($arrRschet)
    {
        /*
        *  метод (ajax) проверяет есть ли переданные счета в таблице rschet
        */
        $sql = "select rschet_id from rschet  where schet = '{$arrRschet}'";
        $id = $this->_db->fetchOne($sql);
        if($id)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function markAsEmployee($clientID)
    {
        if ( ! $clientID )
        {
            return;
        }

        $data['is_employee'] = new Zend_Db_Expr('true');;
        $data['is_donate'] = new Zend_Db_Expr('false');

        $this->update($data, "client_id = {$clientID}");
    }

    public function markAsDonate($clientID)
    {
        if ( ! $clientID )
        {
            return;
        }

        $data['is_employee'] = new Zend_Db_Expr('false');
        $data['is_donate'] = new Zend_Db_Expr('true');

        $this->update($data, "client_id = {$clientID}");
    }

    public function updateClientInfo($clientinfo,$cl_uid, $point_id, $client_type_id)
    {

         if($client_type_id == "1")
         {
            $sqlClients = "
                update clients set
                    client_name   = '{$clientinfo['client_name']}',
                    address       = '{$clientinfo['address']}',
					legaladdress  = '{$clientinfo['legaladdress']}',
                    phone         = '{$clientinfo['phone']}',
                    email         = '{$clientinfo['email']}',
                    passp_n       = '{$clientinfo['passp_n']}',
                    client_orient = '{$clientinfo['client_orient']}',
                    fax           = '{$clientinfo['fax']}',
                    ruvd_id       = {$clientinfo['ruvd_id']}
                where
                client_id = {$cl_uid}
                ";

         }
         else
         {
            $sqlClients = "
                update clients set
                    client_name   = '{$clientinfo['client_name']}',
                    address       = '{$clientinfo['address']}',
					legaladdress  = '{$clientinfo['legaladdress']}',
                    phone         = '{$clientinfo['phone']}',
                    email         = '{$clientinfo['email']}',
                    passp_n       = '{$clientinfo['passp_n']}',
                    client_orient = '{$clientinfo['client_orient']}',
                    fax           = '{$clientinfo['fax']}',
                    inn           = '{$clientinfo['inn']}',
                    mfo           = '{$clientinfo['mfo']}',
                    okonx         = '{$clientinfo['okonx']}',
                    bank_id       = '{$clientinfo['bank_id']}'
                where
                client_id = {$cl_uid}
                ";
         }
        $this->_db->fetchAll($sqlClients);
    }

    public function searchCardClient($param, $value)
    {

    	if($param == "client_id")
    	{
			$add = " AND CL.{$param} = {$value} ";
    	}
    	else
    	{
    		$add = " AND CL.{$param} like '%{$value}%' ";
    	}
        $query = "
            SELECT
                CL.*
            FROM
                clients as CL
            LEFT JOIN
                contracts as CNTR ON CNTR.client_id = CL.client_id
            WHERE
                CL.client_type_id = 4
			{$add}
            ";

         return $this->_db->fetchAll($query);
    }

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
        where   typename_value = '0' and typename_id = CLA.bank_id) as bank_name,
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
		PTS.sign_name,
		PTS.post_sign_name,
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
			CLA.client_id,
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
            where typename_value = '0' and typename_id = CLA.bank_id) as bank_name,
            -- Наим. банка

            (select typename from asrtypes
            where typename_value = '2' and typename_id = CLA.ruvd_id) as ruvd_name,
            -- Наим. РУВД

            (select first_name ||' '|| last_name from users
            where id = CNTR.manager_id) as manager_full_name
            -- Менеджер

            from
				 points as PTS
			right join
				 clients as CLA
				 on(PTS.client_id = CLA.client_id)
			left join
				 contracts as CNTR
			     on(CLA.client_id = CNTR.client_id)
		    where
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
    	if ( ! $client_id )
    	{
    		return null;
    	}

        $sql = "
            select
                    schet  --Сам номер счета
            from rschet
            where
                    client_id = {$client_id}
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

            (select client_type_id from clients where client_id = PTS.client_id limit 1 ) as ctype,

            (select tarif_name from tarifs where
                tarif_id = COLLA.tarif_id
            ) as tarif_name,

            (select tarif_price from tarifs where
                tarif_id = COLLA.tarif_id
            ) as tarif_price,
			(select gw_address from network_params where point_id = PTS.point_id limit 1) as gw_address,
			(select mask from network_params where point_id = PTS.point_id limit 1) as mask,

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
            (select group_name from service_type, tarifs
            where
                service_type.servicetype_id = tarifs.servicetype_id
                and
                tarifs.tarif_id = COLLA.tarif_id
            ) as group_name,
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

        ";

        if($distinct == "1")
        {

            $sql .= "
            and
                    COLLA.startdate > current_date
            ";
        }
        elseif($distinct != "2")
        {

            $sql .= "
            AND 'now'::text::date >= COLLA.startdate
            AND 'now'::text::date < COLLA.enddate
            ";
        }
        else
        {
        	$sql .= "AND current_date >= COLLA.startdate ";
        }

        $sql .= " order by PTS.point_id ASC, COLLA.enddate DESC";

        $data = $this->_db->fetchAll($sql);

        $pointIPAddressModel = new PointIpAddresses();

        $points = array();
        $result = array();

        foreach ($data as $key => $row)
        {
        	if ( in_array($row['point_id'], $points))
        	{
        		continue;
        	}

        	$points[] = $row['point_id'];

        	$ips = $pointIPAddressModel->getPointIpAddresses($row['point_id']);
        	$row['ip_address'] = implode(' ', $ips);
        	array_push($result, $row);
        }

		if($result[0]['group_name'] == "special")
		{
			$s = new DynamicUnlimModel();
			$dynamicUnlimData = $s->getSpeedAndPrice($result[0]['id']);
			$result[0]['tarif_price'] = $dynamicUnlimData['tarif_price'];
			$result[0]['speed'] = $dynamicUnlimData['speed'];
		}

        return $result;
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

    public function getDeliveryInvoice($client_id)
    {
        $sql = "
            select * from additional_services where client_id = {$client_id}
            and
                current_date between startdate and enddate
            and
                enddate > current_date
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getAllDeliveryInvoice($clientID)
    {
    	$sql = $this->_db->select()
    		->from('additional_services')
    		->where('client_id = ?', $clientID)
    		->order('startdate');
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

    public function saveChanges($clientData, $clientID = 0)
    {
        $clientID = intval($clientID);

        $columns = $this->_getCols();

        foreach ($clientData as $key => $value)
        {
            if ( ! in_array($key, $columns) )
            {
                unset($clientData[$key]);
            }
        }

        if ($clientID)
        {
            $this->update($clientData, "client_id = {$clientID}");
        }
        else
        {
            unset($clientData['client_id']);
            $clientID = $this->insert($clientData);
        }

        return $clientID;
    }

    public function decreaseBallance($clientID, $amount)
    {
        $clientID = intval($clientID);

        if ( ! $clientID )
        {
            return;
        }

        if ( ! $amount )
        {
            return;
        }

        $data['ballance'] = new Zend_Db_Expr("ballance - $amount");
        $this->update($data, "client_id = {$clientID}");
    }

    public function increaseBallance($clientID, $amount)
    {
        $clientID = intval($clientID);

        if ( ! $clientID )
        {
            return;
        }

        if ( ! $amount )
        {
            return;
        }

        $data['ballance'] = new Zend_Db_Expr("ballance + $amount");
        $this->update($data, "client_id = {$clientID}");
    }

    public function enableAllServices($clientID)
    {
    	$data['penable'] = new Zend_Db_Expr('true');
    	$pointFilter = $this->_db->select()
    						->from('points')
    						->where('client_id = ?', $clientID);
    	$whereCond = array("point_id in {$pointFilter}", 'now() BETWEEN startdate AND enddate');

    	$this->_db->update('additional_services', $data, $whereCond);
		$this->_db->update('adsl', $data, $whereCond);
		$this->_db->update('collacation', $data, $whereCond);
		$this->_db->update('ngn', $data, $whereCond);
		$this->_db->update('dialup', $data, $whereCond);
		$this->_db->update('hosting', $data, $whereCond);
		$this->_db->update('tasix', $data, $whereCond);
		$this->_db->update('vpn', $data, $whereCond);
		$this->_db->update('wifi', $data, $whereCond);
    }

    public function getClientPoints($clientID, $status = false)
    {
    	if ( ! $clientID )
    	{
    		return null;
    	}

    	$select = $this->_db->select()->from('points', array('point_id'))
    		->where('client_id = ?', $clientID);

    	if ($status !== false)
    	{
    		$status = $status ? $status : '0';
    		$select = $select->where('statuscross = ?', $status);
    	}

    	$points = $this->_db->fetchCol($select);

	return $points;
    }

    public function switchOnAllPorts($clientID, $tasktype = null)
    {
    	if ($tasktype != 2)
    	{
    		$status = 25;
    	}
    	else
    	{
    		$status = null;
    	}

    	$points = $this->getClientPoints($clientID, $status);


    	if ( ! is_array($points) || count($points) == 0)
    	{
    		return false;
    	}

		if($tasktype == 2)
		{
			$task = Porttasks::TASK_TYPE_OFF;
		}
		else
		{
			$task = Porttasks::TASK_TYPE_ON;
		}

    	$porttaskModel = new Porttasks();
    	$porttaskModel->addPointsTasks($points, $task);
    }

    public function setBlock($date = NULL, $client_id, $flag)
    {
        if( $flag == 1 )
        {
            $sql_update = "
            update clients set block_from_date = '{$date}'
            where
                client_id = {$client_id}
            ";
        }
        elseif( $flag == 0 )
        {
            $sql_update = "
            update clients set block_from_date = NULL
            where
                client_id = {$client_id}
            ";
        }
        $this->_db->fetchAll($sql_update);
    }

  	public function callEngineer($client_id, $client_type_id, $value, $userid)
  {
    /**
     *  Метод снимает деньги за вызов специалиста
     */


        if($client_type_id == 1) // Если клиент физик, то это платно если нет то нет
        {
            $decMoney = $value;
        }
        else
        {
            $decMoney = '0';
        }

        $sql = "
        update clients set ballance = ballance - {$decMoney}
        where client_id = {$client_id}
        ";

        $sql2 = "insert into transactions (
                    trantype,
                    summa,
                    client_id,
                    userid,
                    summas
                )
        VALUES
                (
                    7124,
                    {$decMoney},
                    {$client_id},
                    {$userid},
                    dollar2sum({$decMoney})
                )";

		$sql_call_insert = "
		insert into engineer_calls (point_id, status)
		VALUES
			(

			)
		";
        $this->_db->fetchAll($sql);
        $this->_db->fetchAll($sql2);
    }

	public function getConnectLogs($u_login)
	{
		$sql = "SELECT * FROM loguser WHERE login='{$u_login}' ORDER BY currenttime DESC LIMIT 20;";
		return $this->_db->fetchAll($sql);
	}

	public function getCorpClientsIDs()
	{
		$sql = "
			SELECT
				client_id
			FROM
				clients
			WHERE
				client_type_id = 0
		";

		return $this->_db->fetchCol($sql);
	}

	public function getCorpClients()
	{
		$sql = "
			SELECT
				*
			FROM
				clients
			WHERE
				client_type_id = 0
			ORDER BY
				client_id
		";

		return $this->_db->fetchAll($sql);
	}

	public function getCorpsNotInEffect()
	{
		$sql = "
			SELECT
				CS.*,
				(select typename from asrtypes
					where
						typename_value = '6'
					and
						typename_id = CS.statuscross
				) as label
			FROM
				client_services as CS
			WHERE
				CS.client_type_id = 0
            AND
				CS.is_forced = false
			AND
				CS.servicetype_id <> 9999
		";

		return $this->_db->fetchAll($sql);
	}

	public function switchOnService($table, $ID)
	{
		$data['penable'] = new Zend_Db_Expr('true');
		$this->_db->update($table, $data, "id = {$ID}");
	}

	public function startAdditionalOffs($data)
	{
		// Доп. списания

		$sql = "
			select * from additional_offs(
											{$data['point_id']},
											{$data['client_id']},
											{$data['amount']},
											'{$data['commente']}',
											{$data['servicetype_id']},
											{$data['userid']}
										 )
		";
		$this->_db->fetchAll($sql);
	}

	public function unforceServices($clientID, $year, $pointID = null)
	{
		$serviceTypeModel = new ServiceType();
		$serviceTypes = $serviceTypeModel->getServicesTables();
		$startYear = "{$year}.01.01";

		foreach ($serviceTypes as $serviceType => $tableName)
		{
                    if ( ! $tableName
                    || $tableName == 'ivr'
                    || $tableName == 'tel'
                    || $tableName == 'lvs') // ivr, tel, lvs does not exist now
                    {
                            continue;
                    }
/*
                    $sql = "
                            UPDATE
                                    {$tableName}
                            SET
                                    is_forced = false
                            WHERE
                                    point_id IN (SELECT point_id FROM points WHERE client_id = {$clientID})
                    ";
*/
                    $sql = "
                            UPDATE
                                    {$tableName}
                            SET
                                    is_forced = false
                            WHERE
                                    point_id = {$pointID}
                                   	AND enddate > '{$startYear}'
                    ";

                    $this->_db->fetchOne($sql);
		}
	}

	public function getClientServicesFullHistory($clientID, $year)
	{
		$serviceTypeModel = new ServiceType();
		$serviceTypes = $serviceTypeModel->getServicesTables();
		$serviceData = array();
		$startYear = "{$year}"."-01-01";

		foreach ($serviceTypes as $serviceType => $tableName)
		{
			if ( ! $tableName
    			|| $tableName == 'ivr'
    			|| $tableName == 'tel'
    			|| $tableName == 'lvs') // ivr, tel, lvs does not exist now
    		{
    			continue;
    		}

			$sql = "
    			SELECT
    				{$tableName}.*,
    				tarifs.tarif_name,
    				tarifs.tarif_price,
    				tarifs.group_name,
                	tarifs.limit as tarif_limit,
                	tarifs.unlimit,
                	(SELECT
                		traffic_excess
                	FROM
                		tarif_components
                	WHERE
                		tarif_components.tarif_id = tarifs.tarif_id
                	LIMIT 1) AS traffic_excess,
                	service_type.servicetype_id,
                	service_type.servicetype_name
            	FROM
                	{$tableName}
                JOIN (SELECT * FROM points UNION ALL SELECT * FROM points_arhiv) points
                	ON points.point_id = {$tableName}.point_id
                JOIN (SELECT * FROM clients UNION ALL SELECT * FROM clients_arhiv) clients
                    ON clients.client_id = points.client_id
            	JOIN tarifs
                    ON tarifs.tarif_id = {$tableName}.tarif_id
            	JOIN service_type
                    ON service_type.servicetype_id = tarifs.servicetype_id
            	WHERE
                	clients.client_id = {$clientID}
                	AND clients.client_type_id = 0
                	AND clients.is_donate = false
                	AND clients.is_employee = false
              		--AND {$tableName}.is_deleted = false
              		AND service_type.servicetype_id = {$serviceType}
              		AND {$tableName}.enddate >= '{$startYear}'
              		AND {$tableName}.is_forced = true
   				ORDER BY
   					startdate
    		";

             $services = $this->_db->fetchAll($sql);

             foreach ($services as $service)
             {
             	array_push($serviceData, $service);
             }
		}

		return $serviceData;
	}

	public function getPointServiceFullHistory($pointID, $year, $tableName, $serviceTypeID = null)
	{
		//$serviceTypeModel = new ServiceType();
		//$serviceTypes = $serviceTypeModel->getServicesTables();
		$serviceData = array();
		$startYear = "{$year}"."-01-01";

		//foreach ($serviceTypes as $serviceType => $tableName)
		//{
			/*if ( ! $tableName
    			|| $tableName == 'ivr'
    			|| $tableName == 'tel'
    			|| $tableName == 'lvs') // ivr, tel, lvs does not exist now
    		{
    			continue;
    		}*/

    		$sql = "
    			SELECT
    				{$tableName}.*,
    				tarifs.tarif_name,
    				tarifs.tarif_price,
    				tarifs.group_name,
                	tarifs.limit as tarif_limit,
                	tarifs.unlimit,
                	(SELECT
                		traffic_excess
                	FROM
                		tarif_components
                	WHERE
                		tarif_components.tarif_id = tarifs.tarif_id
                	LIMIT 1) AS traffic_excess,
                	service_type.servicetype_id,
                	service_type.servicetype_name
            	FROM
                	{$tableName}
                JOIN
                	points ON points.point_id = {$tableName}.point_id
                JOIN
                	clients ON clients.client_id = points.client_id
            	JOIN
               		tarifs ON tarifs.tarif_id = {$tableName}.tarif_id
            	JOIN
                	service_type ON service_type.servicetype_id = tarifs.servicetype_id
            	WHERE
                	points.point_id = {$pointID}
                	AND clients.client_type_id = 0
                	AND clients.is_donate = false
                	AND clients.is_employee = false
              		AND {$tableName}.is_deleted = false
              		AND service_type.servicetype_id = {$serviceTypeID}
              		AND {$tableName}.enddate >= '{$startYear}'
              		AND {$tableName}.is_forced = true
   				ORDER BY
   					startdate
    		";

             $services = $this->_db->fetchAll($sql);

             foreach ($services as $service)
             {
             	array_push($serviceData, $service);
             }
    	//}

    	return $serviceData;
	}

    public function getLastSession($client_type_id, $point_id)
    {
        if($client_type_id == 0)
        {
            $sql = "
               select max(date_time) as last_session from data01 as D, point_ip_addresses as PIA
               where
                    D.ip_address = PIA.ip_address
               and
                    PIA.point_id = {$point_id}
               limit 1
            ";

        }
        elseif($client_type_id == 1)
        {
            $sql = "
                select max(acctstarttime) as last_session from radacct
                where
                    username = (select u_login from points where point_id = {$point_id} limit 1)
            ";
        }
        else
        {
            $sql = "select  'It is not defined' as last_session";
        }
        return $this->_db->fetchOne($sql);

    }

	public function addRovd($name)
	{
		$sql_maxID = "
			select max(typename_id) from asrtypes where typename_value = '2'
		";
		$maxID = $this->_db->fetchOne($sql_maxID);

		$maxID = $maxID + 1;

		$sql = "
			insert into asrtypes (typename, typename_id, typename_value)
			values
			(
				'{$name}',
				{$maxID},
				'2'
			)
		";
		$this->_db->fetchAll($sql);
	}

    public function addBank($name)
	{
		$sql_maxID = "
			select max(typename_id) from asrtypes where typename_value = '0'
		";
		$maxID = $this->_db->fetchOne($sql_maxID);

		$maxID = $maxID + 1;

		$sql = "
			insert into asrtypes (typename, typename_id, typename_value)
			values
			(
				'{$name}',
				{$maxID},
				'0'
			)
		";
		$this->_db->fetchAll($sql);
	}

	public function changeLoginByTech($data, $flag = 0)
	{
		/**
		 *  Смена логина и всех аттрибутов
		 */

		if($flag == 1)
		{
			$sql = "
				update points set pcross = '{$data['pcross']}' where point_id = {$data['point_id']}
			";
			$this->_db->fetchOne($sql);
			return 1;
		}
		else
		{
			$sql = "
				select * from change_login({$data['point_id']}, {$data['client_type_id']}, '{$data['pcross']}', '{$data['new_login']}')
			";
			return $this->_db->fetchOne($sql);
		}

	}


    public function getSuspiciousClients($month, $tarifID)
    {
    	$date = date('Y')."-".$month."-01";

		$sql = "
				select
					A.point_id,
					A.login,
					(select traffic_in from stream_traffic
						where
							username = A.login
						and
							to_char(date,'YYYY-MM') = to_char('{$date}'::timestamp,'YYYY-MM')
					) as t_in,
					(select traffic_out from stream_traffic
						where
							username = A.login
						and
							to_char(date,'YYYY-MM') = to_char('{$date}'::timestamp,'YYYY-MM')
					) as t_out
				from adsl as A
				where
					A.tarif_id in(
						{$tarifID}
						)
				and
					'{$date}'::timestamp between A.startdate and A.enddate
		";

		$data = $this->_db->fetchAll($sql);

		$clientCounter = 0;

		foreach($data as $item)
		{
			$sumIN  = $sumIN  + $item['t_in'];
			$sumOUT = $sumOUT + $item['t_out'];

			if($item['t_in'] != "")
			{
				$clientCounter++;
			}
		}

		$midlleNumber = $sumIN / $clientCounter;

		$result['midlle'] = $midlleNumber;
		$result['data']   = $data;

		return $result;
    }


    public function getAndReturnConnectTransactionsSumm($clientID, $data)
    {
		$sql = "
			select summa from transactions where client_id = {$clientID} and trantype = 120 limit 1
		";

		$summa = $this->_db->fetchOne($sql);

		$sql_verify = "
			select id from transactions where trantype = 80 and client_id = {$clientID} limit 1
		";
		$ID = $this->_db->fetchOne($sql_verify);


		$sql_cross = "
			select statuscross from points where point_id = {$data['point_id']} limit 1
		";
		$status = $this->_db->fetchOne($sql_cross);

		if($status == 25)
		{
			return 4;
		}

		if(!$ID)
		{
			if($summa)
			{
				$sql_insert = "
					insert into transactions(trantype, client_id, summa, summas, point_id, userid)
					values
					(80, {$clientID}, {$summa}, dollar2sum({$summa}), {$data['point_id']}, {$data['userid']})
				";
				$this->_db->fetchAll($sql_insert);

				$sql_ballance = "
					update clients set ballance = ballance + {$summa}, omoney= omoney -  {$summa}
					where client_id = {$clientID}
				";
				$this->_db->fetchAll($sql_ballance);
				return 2;
			}
			else
			{
				return 1;
			}
		}
		else
		{
			return 3;
		}
    }

    public function startTransaction()
    {
        $this->_db->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->_db->commit();
    }

    public function rollbackTransaction()
    {
        $this->_db->rollBack();
    }

    public function getClientsDistricts()
    {
    	$pointSql = $this->_db->select()
    		->from('points')
    		->union('points_arhiv', 'UNION ALL');

    	$sql = $this->_db->select()
    		->from('clients', array('client_id'))
    		->join(array('points' => $pointSql), 'points.client_id = clients.client_id', array())
    		->join('ats_list', 'ats_list.id = points.ats_id', array())
    		->join('phone_hub_list', 'phone_hub_list.id = ats_list.phone_hub_id', array('name'));

    	return $this->_db->fetchPairs($sql);
    }

    public function getCurrencyWord($client_id)
    {
		$sql = "
			select currency from clients where client_id = {$client_id}
		";

		return $this->_db->fetchOne($sql);
    }

    public function getByPointID($pointID)
    {
        $pointID = intval($pointID);

        $sql = "
            SELECT
                *
            FROM
                clients
            JOIN
                points on points.client_id = clients.client_id
            WHERE
                points.point_id = {$pointID}
        ";

        return $this->_db->fetchRow($sql);
    }

    public function getDataForUncrossReasonReport($startdate, $enddate, $clientType=0)
    {
        $sql = "
            SELECT
                at.typename,
                count(*) AS amount
            FROM
                tech_history th
                INNER JOIN
                    (SELECT * FROM points
                     UNION ALL
                     SELECT * FROM points_arhiv) p
                    ON th.client_login = p.u_login
                INNER JOIN
                    (SELECT * FROM clients
                     UNION ALL
                     SELECT * FROM clients_arhiv) c
                    ON p.client_id = c.client_id
                LEFT JOIN asrtypes at
                    ON at.typename_value = '23'
                    AND at.typename_id = p.leaving_reason
            WHERE
                th.status = -3
                AND p.statuscross < 0
                AND th.date BETWEEN '{$startdate}' AND '{$enddate}'
                AND c.client_type_id = {$clientType}
            GROUP BY at.typename
        ";

        return $this->_db->fetchAll($sql);
    }
}

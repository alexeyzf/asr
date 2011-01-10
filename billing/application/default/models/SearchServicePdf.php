<?php
/*
 *  Метод ищет все услуги на которые имеются
 *  договора с указанным клиентом
 */

require_once('Zend/Db/Table.php');
require_once('AsrHelp.php');
require_once('TarifComponents.php');

class SearchServicePdf extends Zend_Db_Table
{
    public function startSearchService($client_id, $tablename, $client_type_id = NULL)
    {
        if ($tablename == 'collacation')
        {
            $additionalColumns = "(
                SELECT
                    typename
                FROM
                    asrtypes
                WHERE
                    typename_value = '" . AsrHelp::COLLACATION_EQ_TYPE . "'
                    AND typename_id = COLLA.equipment_type
            ) AS equipment_type_name,";
        }

        $sql = "
        select
            {$additionalColumns}
            COLLA.reg_pay - (COLLA.reg_pay * COLLA.discount / 100) AS reg_pay_total,
            TAR.*,
            TAR.startdate as tarif_startdate,
            COLLA.*,
            COLLA.startdate,
            PTS.*,
            PTS.contact_name AS point_contact_name,
            PTS.phone as pts_phone,
            CLA.*,
            AST.*,
            CNTR.*,
            CLA.phone as client_phone,

            (select first_name ||' '|| last_name from users
            where id = CNTR.manager_id) as user_name,
            -- Имя user

            (select phone from users
            where id = CNTR.manager_id) as user_phone,
            -- Имя phone

            (select email from users
            where id = CNTR.manager_id) as user_email,
            -- Имя email

            (select typename from asrtypes
            where typename_value = '7' and typename_id = CLA.client_type_id) as face,
            -- Тип лица

            (select typename from asrtypes
            where typename_value = '2' and typename_id = CLA.ruvd_id) as ruvd_name,
            -- Кем выдан паспорт

            (select typename from asrtypes
            where typename_value = '0' and typename_id = CLA.bank_id) as bank_name,
            -- Тип лица

            (select TC.traffic_excess from tarif_components as TC
            where TC.tarif_id = TAR.tarif_id
            limit 1) as traffic_excess
            -- Тариф компонент
        from
            {$tablename} as COLLA
        left join
            points as PTS on(COLLA.point_id = PTS.point_id)
        left join
            clients as CLA on(PTS.client_id = CLA.client_id)
        left join
            tarifs as TAR on(COLLA.tarif_id = TAR.tarif_id)
        left join
            service_type as AST on(TAR.servicetype_id = AST.servicetype_id)
        left join
            contracts as CNTR on(CLA.client_id = CNTR.client_id)
        where
            CLA.client_id = '{$client_id}'
        and
            CNTR.contract_type_id = 1
        and
            COLLA.is_deleted = false
        and 
        	COLLA.enddate > now()
        ";

        $services = $this->_db->fetchAll($sql);

        $tarifComponentsModel = new TarifComponents();
		$pointIPAddressModel = new PointIpAddresses();
		
        foreach ($services as $key => $service)
        {
        	$ips = $pointIPAddressModel->getPointIpAddresses($service['point_id']);
        	
        	if ( $ips && is_array($ips) )
        	{
        		$services[$key]['ip_address'] = implode(',', $ips);
        	}
        	
            $services[$key]['tarif_components'] = $tarifComponentsModel->getComponentsByTarifID($service['tarif_id']);
        }

        return $services;
    }
    
    public function getServiceInfo($table, $pointID)
    {
    	$sql = "
    		SELECT
    			service.*,
    			points.*,
    			clients.*,
    			(SELECT typename 
    			FROM asrtypes
        		WHERE typename_value = '1' AND typename_id = points.country_id) AS town
    		FROM
    			{$table} AS service
    		JOIN
    			points ON points.point_id = service.point_id
    		JOIN
    			clients ON clients.client_id = points.client_id
    		WHERE
    			service.point_id = {$pointID}
    			AND service.enddate > now()
    			AND service.penable = true 
    	";
    			
    	return $this->_db->fetchRow($sql);
    }
}
<?php
/**
 * Model of ats_list table
 *
 * @author marat
 */

require_once('BaseModel.php');

class AtsList extends BaseModel
{
    protected $_name = 'ats_list';
    protected $_sequence = 'ats_list_seq';

    public function getOptions()
    {
        $atsOptions = array();
        $atsList = $this->fetchAll('is_deleted = false', 'name');

        foreach($atsList as $ats)
        {
            $atsOptions[$ats->id] = $ats->name;
        }

        return $atsOptions;
    }

    public function atsClientsStream($ats_id)
    {
    	$sql = "
				select
					BIND.*

				from
				(select
					PTS.*,
					PTS.dslam_id as dslamid,
					(select number from ports where id = PTS.port_id) as portnumber,
					(select ip_address from dslam_list where id = PTS.dslam_id) as dslamip,
					(select clients.client_id from clients, points
							where clients.client_id = PTS.client_id limit 1) as port_clientid,
					(select clients.client_type_id from clients, points
							where clients.client_id = PTS.client_id limit 1) as ctype,
					'1024/512' as speed,
					'0' as task_type,
					(
						select tarif_id from adsl
						where
						point_id = PTS.point_id
						and
						now() between startdate and enddate
					) as tarif_id,

					(
					   select group_name from tarifs where tarif_id =
					   		(select tarif_id from adsl
							where
							point_id = PTS.point_id
							and
							now() between startdate and enddate)
					) as group_name
				from
					points as PTS
				where
					PTS.ats_id = {$ats_id}
				) as BIND
				where
					BIND.ctype = 1
				and
					BIND.portnumber is not null
				and
					BIND.tarif_id is not null
				and
					BIND.group_name not like '%reserved%'
		";
		return $this->_db->fetchAll($sql);
    }


    public function verifyAts($ats_id)
    {
        // Метод проверяет атс
    	if ( ! $ats_id )
    	{
    		return;
    	}

        $sql = "
                select expanded from ats_list where id = {$ats_id}
        ";
        $is_expanded = $this->_db->fetchOne($sql);
        return $is_expanded;
    }

    public function markExpended($ats_id)
    {
		$sql = "
			update ats_list set expanded = true where id = {$ats_id}
		";
		$this->_db->fetchAll($sql);
    }


}
<?php
/*
 * Created on 29.07.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once('BaseModel.php');

class AdslModel extends BaseModel
{
    protected $_name = 'adsl';
    protected $_sequence = 'adsl_seq';


	public function closeServiceAndOpenNew($sid_old, $startdate, $data, $tablename, $newtarifID)
	{
		if($sid_old)
		{
			$sql_close_old = " update {$tablename} set enddate = '{$startdate}' where id = {$sid_old}";

			$data['startdate'] = $startdate;
			$data['enddate']   = "2011-01-01";


			try
			{
				$this->startTransaction();

				$this->_db->fetchAll($sql_close_old);

				$data['penable']    = "true";
				$data['is_deleted'] = "false";
				$data['is_forced']  = "true";
				$data['auto_start'] = "false";
				$data['tarif_id'] = $newtarifID;
				unset($data['id']);
				$this->insert($data);

				$this->commitTransaction();
			}
			catch (Exception $e)
			{
				$this->rollBackTransaction();
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
	}

    public function getAdditionalInfo($serviceID = 0)
    {
        $serviceID = intval($serviceID);
        if ($serviceID)
        {
            $sql = "
                SELECT
                    *
                FROM
                    modems
                WHERE
                    service_id = {$serviceID}
                    AND table_name = '{$this->_name}'
            ";

            $array['modem'] = $this->_db->fetchRow($sql);
        }

        //Modem types
        $sql = "
            SELECT
                typename_id,
                typename
            FROM
                asrtypes
            WHERE
                typename_value = '20'
        ";

        $typesResult = $this->_db->fetchAll($sql);

        foreach ($typesResult as $type)
        {
            $types[$type['typename_id']] = $type['typename'];
        }

        $array['modem']['modem_types'] = $types;

        return $array;
    }

    public function saveChanges($data, $id = NULL)
    {
        $id = parent::saveChanges($data, $id);

        if ( is_array($data['modem']) && $data['modem']['modem_serial'] )
        {
            $modemData = $data['modem'];
            $modemData['service_id'] = $id;
            $modemData['table_name'] = $this->_name;

            if ($modemData['modem_id'])
            {
            	$modemID = $modemData['modem_id'];

            	unset($modemData['modem_id']);
                $this->_db->update('modems', $modemData, "modem_id = {$modemID}");
            }
            else
            {
                unset($modemData['modem_id']);
                $this->_db->insert('modems', $modemData);
            }
        }

        return $id;
    }


    public function getModem($client_id, $point_id = null)
    {
    	$sql = "
		select
			MM.*,
			(select typename from asrtypes where typename_value = '20' and typename_id = MM.modem_type) as modem_type
		 from modems as MM where client_id = {$client_id}
		";

		if($point_id)
		{
			$sql .= "
				and point_id = {$point_id}
			";
		}
		return $this->_db->fetchAll($sql);
    }

    public function startTransaction()
	{
		$this->_db->beginTransaction();
	}

	public function commitTransaction()
	{
		$this->_db->commit();
	}

	public function rollBackTransaction()
	{
		$this->_db->rollBack();
	}
}
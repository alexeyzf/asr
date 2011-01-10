<?php
require_once('BaseModel.php');

class VpnModel extends BaseModel
{
    protected $_name = 'vpn';
    protected $_sequence = 'vpn_seq';

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
}
<?php
require_once('BaseModel.php');

class TradtelModel extends BaseModel
{
    protected $_name = 'tradtel';
    protected $_sequence = 'tradtel_seq';


	public function getAdditionalInfo($ID = NULL)
    {
        $ID = intval($ID);

        if ( $ID )
        {
            $tradtelNumbersSql = "
                SELECT
	               number
                FROM
                    tradtel_numbers
                WHERE
                    point_id = (
                    	SELECT
                    		point_id
                    	FROM
                    		tradtel
                    	WHERE
                    		id = {$ID} 
                    )
            ";

            $result['numbers']['numbers'] = $this->_db->fetchAll($tradtelNumbersSql);
        }
        else
        {
        	$result = array();
        }

        return $result;
    }

	public function saveChanges($data, $id = NULL)
    {
        $id = parent::saveChanges($data, $id);

        if ( is_array($data['numbers']['numbers']) )
        {
            foreach ($data['numbers']['numbers'] as $numberID => $numberData )
            {
                if ($numberID == 'newCOUNTER')
                {
                    continue;
                }

                $numberData['point_id'] = $data['point_id'];

                if ( intval($numberID) )
                {
                    $this->_db->update('tradtel_numbers', $numberData, "id = {$numberID}");
                }
                else
                {
                    $this->_db->insert('tradtel_numbers', $numberData);
                }
            }
        }

        return $id;
    }

}
?>
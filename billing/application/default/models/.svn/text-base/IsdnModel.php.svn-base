<?php
require_once('BaseModel.php');

class IsdnModel extends BaseModel
{
    protected $_name = 'isdn';
    protected $_sequence = 'isdn_seq';


	public function getAdditionalInfo($ID = NULL)
    {
        $ID = intval($ID);

        if ( $ID )
        {
            $isdnNumbersSql = "
                SELECT
	            	number
                FROM
                    isdn_numbers
                WHERE
                    point_id = (
                    	SELECT
                    		point_id
                    	FROM
                    		isdn
                    	WHERE
                    		id = {$ID}
                    )
            ";

            $result['numbers']['numbers'] = $this->_db->fetchAll($isdnNumbersSql);
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
                    $this->_db->update('isdn_numbers', $numberData, "id = {$numberID}");
                }
                else
                {
                    $this->_db->insert('isdn_numbers', $numberData);
                }
            }
        }
        return $id;
    }
}
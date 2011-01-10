<?php
/**
 * Model of ats_numbers table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class AtsNumbers extends Zend_Db_Table
{
    protected $_name = 'ats_numbers';
    protected $_sequence = 'ats_numbers_seq';

    public function getAtsIDByNumber($number)
    {
        $sql ="
            SELECT
                ats_id
            FROM
                ats_numbers
            WHERE
                '$number' BETWEEN start_number AND end_number
        ";

        return $this->_db->fetchOne($sql);
    }
    
    public function insertNumbers($numbers, $atsID)
    {
        foreach ($numbers as $numberData)
        {
            unset($numberData['id']);
            $numberData['ats_id'] = $atsID;
            $this->insert($numberData);
        }
    }

    public function fetchAllByAtsID($atsID)
    {
        $atsID = intval($atsID);

        if ( ! $atsID )
        {
            return array();
        }

        return $this->fetchAll("ats_id = {$atsID}")->toArray();
    }

    public function deleteByAtsID($atsID)
    {
        $atsID = intval($atsID);

        if ( ! $atsID )
        {
            return;
        }

        $this->delete("ats_id = {$atsID}");
    }
}
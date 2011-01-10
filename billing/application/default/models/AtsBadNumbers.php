<?php
/**
 * Model of ats_bad_numbers table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class AtsBadNumbers extends Zend_Db_Table
{
    protected $_name = 'ats_bad_numbers';
    protected $_sequence = 'ats_bad_numbers_seq';

    public function fetchAllByAtsID($atsID)
    {
        $atsID = intval($atsID);

        if ( ! $atsID )
        {
            return array();
        }

        return $this->fetchAll("ats_id = {$atsID}")->toArray();
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
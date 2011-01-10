<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('Zend/Db/Table.php');

class AsrHelp extends Zend_Db_Table
{
    protected $_name = 'asrtypes';
    protected $_sequence = 'asrtypes_seq';

    const CITY_TYPE = 1;

    const RUVD_TYPE = 2;

    const BANK_TYPE = 0;

    const DIRECTOR_TYPE = 4;

    const DSLAM_TYPE = 5;

    const POINT_STATUS_TYPE = 6;

    const CLIENT_TYPE = 7;

    const LETTER_KIND_TYPE = 8;

    const LETTER_TYPE_TYPE = 9;

    const LETTER_SENT_WAY_TYPE = 10;

    const LETTER_SENT_DATE_TYPE = 11;

    const CLIENT_DSLAM_TYPE = 12;

    const FINANCIAL_TRANSACTIONS_TYPE = 13;

    const CALL_STATUS_TYPE = 15;

    const COLLACATION_EQ_TYPE = 16;

    const NGN_PHONE_TYPE = 17;

    const TARIF_GROUP_TYPE = 21;

    public function getAsrTypes($filter = array())
    {
        if ( is_array($filter) && count($filter) > 0 )
        {
            $condition = "typename_value IN (";

            foreach ($filter as $item)
            {
                $condition .= "{$connector}'{$item}'";
                $connector = ',';
            }

            $condition .= ')';
        }

        $rows = $this->fetchAll($condition);

        $asrTypes = array();

        foreach ($rows as $row)
		{
        	$asrTypes[$row['typename_value']][$row['typename_id']] = $row->toArray();
		}
		return $asrTypes;
    }

	/**
	 * getAsrTypeOptions
	 *
	 * Gets list of asr type as options with typename_id as key and typename as value
	 *
	 * @param array|integer $filter
	 * @return array
	 */
    public function getAsrTypeOptions($filter = array())
    {
        if ( ! is_array($filter) )
        {
            $f = $filter;
            $filter = array();
            $filter[0] = $f;
            $oneItem = true;
        }

        $asrTypes = $this->getAsrTypes($filter);

        foreach ($asrTypes as $type => $typeValues)
        {
            foreach ($typeValues as $key => $value)
            {
                $asrTypes[$type][$key] = $value['typename'];
            }
        }

        if ($oneItem)
        {
        	return $asrTypes[$filter[0]];
        }

        return $asrTypes;
    }

    public function getAsrTypeName($type, $value)
    {
        if ( ! $value )
        {
            $value = '0';
        }

        $sql ="
            SELECT
                typename
            FROM
                asrtypes
            WHERE
                typename_value = '{$type}'
                AND typename_id = {$value}
        ";

        return $this->_db->fetchOne($sql);
    }
}
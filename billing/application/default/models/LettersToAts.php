<?php
/**
 * Model of letters-to_ats table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class LettersToAts extends Zend_Db_Table
{
    protected $_name = 'letters_to_ats';
    protected $_sequence = 'letters_to_ats_seq';

    /**
     * Letter kind to cross
     */
    const LETTER_KIND_CROSS = 1;

    /**
     * Letter kind to uncross
     */
    const LETTER_KIND_UNCROSS = 2;

    /**
     * Letter kind to recross
     */
    const LETTER_KIND_RECROSS = 3;

    /**
     * Letter type - new letter
     */
    const LETTER_TYPE_NEW = 1;

    /**
     * Letter type - repeat letter
     */
    const LETTER_TYPE_REPEAT = 2;

    /**
     * Letter type - instead lost letter
     */
    const LETTER_TYPE_INSTEAD_LOST = 3;

    /**
     * Letter sent way by courier
     */
    const LETTER_SENT_WAY_COURIER = 1;

    /**
     * Letter sent way by fax
     */
    const LETTER_SENT_WAY_FAX = 2;

    /**
     * Letter wrist sent way
     */
    const LETTER_SENT_WAY_WRIST = 3;

    const LETTER_SENT_DATE_PLUS_ONE_DAY = 1;
    const LETTER_SENT_DATE_PLUS_TWO_DAYS = 2;
    const LETTER_SENT_DATE_TODAY = 3;

    private function getNextNumber($pointID, $type)
    {
        if ($type == self::LETTER_TYPE_REPEAT)
        {
            $oldNumberSql = "
                SELECT
                    number
                FROM
                    letters_to_ats
                WHERE
                    point_id = {$pointID}
                ORDER BY sent_date DESC
            ";

            $oldNumber = $this->_db->fetchOne($oldNumberSql);
            return $oldNumber;
        }
        else
        {
            $newNumberSql = "
                SELECT
                    MAX(number) + 1 AS next_number
                FROM
                    letters_to_ats
                WHERE
                    date_part('year', sent_date) = date_part('year', current_timestamp)
            ";

            $newNumber = $this->_db->fetchOne($newNumberSql);

            if ( ! $newNumber )
            {
                $newNumber = 1;
            }

            return $newNumber;
        }
    }

    public function createLetter($pointID, $atsID, $type, $kind, $sentWay, $sentDateType)
    {
        $number = $this->getNextNumber($pointID, $type);

        $data['number'] = $number;
        $data['point_id'] = $pointID;
        $data['ats_id'] = intval($atsID);
        $data['kind'] = $kind;
        $data['type'] = $type;
        $data['sent_way'] = $sentWay;

        if ($sentDateType == self::LETTER_SENT_DATE_TODAY)
        {
            $sentDate = new Zend_Date();
        }
        elseif ($sentDateType == self::LETTER_SENT_DATE_PLUS_ONE_DAY)
        {
            $sentDate = new Zend_Date();
            $sentDate->addDay(1);
        }
        elseif ($sentDateType == self::LETTER_SENT_DATE_PLUS_TWO_DAYS)
        {
            $sentDate = new Zend_Date();
            $sentDate->addDay(2);
        }

        $data['sent_date'] = $sentDate->toString('YMMdd');

        $data['date'] = new Zend_Db_Expr('now()');

        return $this->insert($data);

    }


    public function getLetterByID($letterID)
    {
        $letterID = intval($letterID);
        $row = $this->fetchRow("id = {$letterID}");

        return $row->toArray();
    }

    public function getLetters($startDate, $endDate)
    {
        $sql = "
            SELECT
                *,
                points.pcross,
                ats_list.name AS ats_name,
                phone_hub_list.name AS phone_hub_name
            FROM
                letters_to_ats
            JOIN
                points ON points.point_id = letters_to_ats.point_id
            JOIN
                ats_list ON ats_list.id = letters_to_ats.ats_id
            JOIN
                phone_hub_list ON phone_hub_list.id = ats_list.phone_hub_id
            WHERE
                \"date\" BETWEEN '{$startDate}' AND '{$endDate}'
            ";

        return $this->_db->fetchAll($sql);
    }

    public function getLettersByHub($hub, $date)
    {
        $sql = "
            SELECT
                letters_to_ats.*,
                points.pcross
            FROM
                letters_to_ats
            JOIN
                points ON points.point_id = letters_to_ats.point_id
            JOIN
                ats_list ON ats_list.id = letters_to_ats.ats_id
            WHERE
                letters_to_ats.sent_date = '{$date}'
            AND
				ats_list.phone_hub_id = {$hub}
			AND
				points.statuscross not in(-5, 23)
            ";

        return $this->_db->fetchAll($sql);
    }

    public function getLastLettersByKind($kinds = array())
    {
    	$sql = '
    		SELECT
    			point_id,
    			MAX(number) AS letter_number,
    			MAX(sent_date) AS letter_sent_date,
    			MAX(receive_date) AS letter_receive_date
    		FROM
    			letters_to_ats
    		WHERE
    			kind in (' . implode(',', $kinds) . ')
    		GROUP BY
    			point_id
    	';

    	$letters = $this->_db->fetchAll($sql);

    	foreach ($letters as $letter)
    	{
    		$result[$letter['point_id']] = $letter;
    	}

    	return $result;
    }
}
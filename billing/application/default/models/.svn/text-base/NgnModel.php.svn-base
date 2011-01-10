<?php
require_once('BaseModel.php');


class NgnModel extends BaseModel
{
    protected $_name = 'ngn';
    protected $_sequence = 'ngn_seq';

    public function selectNgnInPoint($point_id)
    {
        $sql = "
        select
            ngn.*,

            (select short_name from service_type as ST, tarifs as TAR
            where
            ST.servicetype_id = TAR.servicetype_id
            and
            TAR.tarif_id = ngn.tarif_id
            ) as short_name,

            (select tarif_name from tarifs
            where tarif_id = ngn.tarif_id) as tarif_name

        from ngn as ngn where ngn.point_id = '{$point_id}'
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getAdditionalInfo($ID = NULL)
    {
        $ID = intval($ID);

        if ( $ID )
        {
            $ngnNumbersSql = "
                SELECT
                    number,
                    type,
                    price,
					channel
                FROM
                    ngn_numbers
                WHERE
                    point_id = (
                    	SELECT
                    		point_id
                    	FROM
                    		ngn
                    	WHERE
                    		id = {$ID}
                    )
            ";

            $result['numbers']['numbers'] = $this->_db->fetchAll($ngnNumbersSql);
        }

        $asrHelp = new AsrHelp();
        $result['numbers']['types'] = $asrHelp->getAsrTypeOptions(AsrHelp::NGN_PHONE_TYPE);

        return $result;
    }

    /**
     * Saves changes to db
     *
     * @param array $data - Associative column => $value array
     * @param int $id - Record ID
     */
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

                // set password
                $numberData['password'] =  $this->generateNgnPass(8);

                if ( intval($numberID) )
                {
                    $this->_db->update('ngn_numbers', $numberData, "id = {$numberID}");
                }
                else
                {
                    $this->_db->insert('ngn_numbers', $numberData);
                }
            }
        }

        return $id;
    }

    public function searchNgnNumber($number)
    {
    	$sql = "
			select
				CLA.*,
				PTS.*
			from clients as CLA, points as PTS, ngn_numbers as NN
			where
				CLA.client_id = PTS.client_id
			and
				PTS.point_id = NN.point_id
			and
				NN.number like '%{$number}%'
			limit 1
		";
		$data = $this->_db->fetchAll($sql);

		if(!$data)
		{
			return null;
		}
		$sql_all_numbers = "
			select number from ngn_numbers where point_id in
			(
				select point_id from points where client_id = {$data[0]['client_id']}
			)
		";
		$all_numbers = $this->_db->fetchAll($sql_all_numbers);

		$data[0]['all'] = $all_numbers;

		return $data;

    }

    public function getNgnNumbersCount($pointID)
    {
		$sql = "
			SELECT
				count(point_id) AS counter
			FROM
				ngn_numbers
			WHERE
				point_id = {$pointID}
		";

		return $this->_db->fetchOne($sql);
    }

    public function getNgnCountChannel($pointID)
    {
		$sql = "
			SELECT
				sum(channel) AS channel_sum
			FROM
				ngn_numbers
			WHERE
				point_id = {$pointID}
		";
		return $this->_db->fetchOne($sql);
    }

    public function generateNgnPass($length)
    {
        $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++)
        {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }

    public function showNotRegisteredNumbers()
    {
        $sql = "
            select
                    NN.*,
                    (select u_login from points where point_id = NN.point_id) as u_login,
                    (select CLA.client_name from clients as CLA, points as PTS
                        where
                            CLA.client_id = PTS.client_id
                        and
                            PTS.point_id = NN.point_id limit 1) as client_name,
                    (select CLA.ballance from clients as CLA, points as PTS
                        where
                            CLA.client_id = PTS.client_id
                        and
                            PTS.point_id = NN.point_id limit 1) as ballance
            from ngn_numbers as NN
            where
                NN.registry_done = false
        ";
        return $this->_db->fetchAll($sql);
    }

    public function setIsDone($pid, $number)
    {
        $sql = "
            update ngn_numbers set registry_done = true
            where
                point_id = {$pid}
            and
                number = '{$number}'
        ";
        return $this->_db->fetchRow($sql);
    }


    public function getNGNCalls($number, $start, $end)
    {
			$end   = $end. " 23:59:59";
			$start = $start. " 00:00:00";

            $sql = "
                    SELECT
                            calldate,
                            src as abonent1,
                            dst as abonent2,
                            disposition,
                            round((duration::float + 59 ) / 60) ::float AS minutes_count
                    FROM
                            cdr
                    WHERE
                            calldate BETWEEN '{$start}' and '{$end}'
                    AND
                            src::character varying LIKE '113%'
                    AND
                            src = '{$number}'
            ";

            return $this->_db->fetchAll($sql);
    }

    public function getTarifList()
    {
        $sql = "
            select * from phone_services_tarifs order by prefix
        ";
        return $this->_db->fetchAll($sql);
    }
}
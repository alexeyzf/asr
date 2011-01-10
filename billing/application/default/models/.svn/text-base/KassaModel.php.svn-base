<?php
require_once('BaseModel.php');
require_once('Porttasks.php');

class KassaModel extends BaseModel
{
    protected $_name = 'rates';
    protected $_sequence = 'rateexchange_seq';

    public function saverate($startdate, $rate)
    {
      $year = date('Y', strtotime('+1 year'));
      $end = $year. '-12-31';

      $sql_update = "
	      update rates set enddate = '{$startdate}'
	      	where
	      current_date between startdate and enddate
      ";
      $this->_db->fetchAll($sql_update);

      $sql = "
       	insert into rates (startdate, rate, enddate) values ('{$startdate}', {$rate}, '{$end}')
      ";


      $this->_db->fetchAll($sql);
    }

    public function show()
    {
    	$sql = "
    		SELECT
        		rate
    		FROM 
    			rates
    		WHERE
        		now() BETWEEN startdate AND enddate
    		ORDER BY
    			startdate DESC
    	";

    	return $this->_db->fetchOne($sql);
    }

    public function getClient($type = null, $word = null)
    {
    if(!$word)
    {
      return null;
    }


    $sql = "
    select
      CLA.*,
      PTS.*
    from clients as CLA, points as PTS
    where
      CLA.client_id = PTS.client_id
    ";

    if($type == "CLA.client_id")
    {
      $sql .= " and {$type} = {$word}";
    }

    if($type == "CLA.client_name")
    {
      $sql .= " and {$type} = '{$word}'";
    }

    return $this->_db->fetchAll($sql);

    }

  public function recalculate($client_id = NULL)
  {
    $sql = "
      update clients set ballance =
      (select
        sum(
          (
          CASE
          WHEN
            T.trantype <= 100   THEN T.summa
          ELSE
            -T.summa
          END
          )
           )
      from
      transactions as T
      where
        T.client_id = clients.client_id

    ";

    if($client_id)
    {
      $sql .= " )  where clients.client_id = {$client_id} ";
    }
    else
    {
      $sql .= " )";
    }

    $this->_db->fetchAll($sql);
  }

  public function setOnService($client_id, $tablename = NULL, $distinct = null)
    {
        $sql = "
        update {$tablename} as COLLA
	      set penable = true
	        where
	            COLLA.point_id  in (select point_id from points where points.client_id = {$client_id})
	    and
	      current_date between COLLA.startdate and COLLA.enddate
	    and
	      COLLA.enddate > current_date
	    and
	      COLLA.is_deleted = false
        ";
        $this->_db->fetchAll($sql);
    }

  public function getOnPoints($client_id)
  {
    $arrPointsSql = "
	    select PTS.point_id, CLA.client_type_id, PTS.statuscross from points as PTS, clients as CLA
	    where
	      PTS.client_id = CLA.client_id
	    and
	      CLA.client_id = {$client_id}
    ";
    return $this->_db->fetchAll($arrPointsSql);
  }

  public function deleteTodayTransaction($tran_id, $user_id)
  {
    $sql = "
    select delete_payments_from_kassa({$tran_id}, {$user_id})
    ";
    $this->_db->fetchAll($sql);
  }
}
?>
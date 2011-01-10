<?php
require_once('Zend/Db/Table.php');

class AbonHistoryModel extends Zend_Db_Table
{

    protected $_name = 'logs';
    protected $_sequence = 'abon_history_seq';

	public function searchQuery($type, $word)
	{
		$clientSql = $this->_db->select()
			->from('clients')
			->union('clients_arhiv', 'UNION ALL');
			
		$sql = $this->_db->select()
			->from(array('lg' => 'logs'))
			->join(array('cla' => $clientSql), "cla.client_id = lg.client_id")
			->join(array('uss' => 'users'), 'uss.id = lg.user_id', array('first_name', 'last_name'));
			
		if($type == "LG.client_id")
		{
			$sql = $sql->where('LG.client_id = ?', intval($word));
		}
        elseif($type)
        {
        	$word = trim(strtolower($word));
        	$sql = $sql->where("LOWER({$type}) like '%{$word}%'");
		}
		
		$sql = $sql->order('lg.actiondate DESC');
		
		return new Zend_Paginator_Adapter_DbSelect($sql);
    }

    public function getClientLogs($clientID)
    {
        if ( ! $clientID )
        {
            return array();
        }

        $sql = "
            SELECT
                actiondate,
                action,
                action_url,
                users.first_name || users.last_name AS user_name
            FROM
                logs
            LEFT JOIN
                users ON users.id = logs.user_id
            WHERE
                client_id = {$clientID}
            ";

        return $this->_db->fetchAll($sql);
    }
}
?>

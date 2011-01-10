<?php
require_once('Zend/Db/Table.php');

class TrapModel extends Zend_Db_Table
{

    protected $_name = 'logs';
    protected $_sequence = 'abon_history_seq';

    public function init()
    {
        $this->_dbTrap = Zend_Registry::getInstance('trapAdapter')->trapAdapter;
    }

    public function getTraps($login)
    {
        $sql_data_clients = "
            select
                    DL.ip_address, PRT.number
            from ports as PRT, dslam_list as DL
            where
                    PRT.id = (select port_id from points where u_login  = '{$login}')
            and
                    PRT.dslam_id = DL.id
        ";
        $dataClients = $this->_db->fetchAll($sql_data_clients);

        $IP   = $dataClients[0]['ip_address'];
        $port = $dataClients[0]['number'];

        $sql_get_traps = "
            select * from traps where ipaddress = '{$IP}' and portnumber = {$port}
            order by action_date desc
        ";

        return $this->_dbTrap->fetchAll($sql_get_traps);
    }
}
?>

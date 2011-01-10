<?php
require_once('Zend/Db/Table.php');

class SearchClientModel extends Zend_Db_Table
{
    protected $_name = 'clients';

    protected $_sequence = 'clients_seq';

    public function searchClientInfo($stype,$sword)
    {
        $query = "
        select * from clients as CL, contracts as CNTR, points as PTS
        where
            CNTR.client_id = CL.client_id
        and
            CL.client_id = PTS.client_id
        and
            PTS.u_login like '%{$sword}%';
         ";

         return $this->_db->fetchAll($query);
    }

    public function searchCardClient($param, $value)
    {
        $query = "
            SELECT
                *
            FROM
                clients as CL
            LEFT JOIN
                contracts as CNTR ON CNTR.client_id = CL.client_id
            WHERE
                CL.client_type_id = 4
                AND {$param} like '%{$value}%';
            ";

         return $this->_db->fetchAll($query);
    }
}
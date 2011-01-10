<?php
require_once('Zend/Db/Table.php');

class IpaddressModel extends Zend_Db_Table
{
    protected $_name = 'point_ip_addresses';


    public function currentIP($point_id)
    {
        $sql = "
            select * from point_ip_addresses
            where
                    point_id = {$point_id}
            and
                    now() between start_date and end_date
        ";
        return $this->_db->fetchAll($sql);
    }
}
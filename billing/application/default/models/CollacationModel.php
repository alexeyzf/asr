<?php
require_once('BaseModel.php');

class CollacationModel extends BaseModel
{
    protected $_name = 'collacation';
    protected $_sequence = 'collacation_seq';

    public function selectCollaInPoint($point_id)
    {
        $sql = "
        select
            colla.*,

            (select short_name from service_type as ST, tarifs as TAR
            where
            ST.servicetype_id = TAR.servicetype_id
            and
            TAR.tarif_id = colla.tarif_id
            ) as short_name,

            (select tarif_name from tarifs
            where tarif_id = colla.tarif_id) as tarif_name

        from collacation as colla where colla.point_id = '{$point_id}'
        ";
        return $this->_db->fetchAll($sql);
    }


    public function deleteRow($point_id, $sid, $tarif_id, $tablename)
    {
        /*
        *  метод удаляет строку по указанным значениям
        */
        $sql = "
        delete from {$tablename}
            where
                point_id = '{$point_id}'
            and
                tarif_id = '{$tarif_id}'
            and
                id = '{$sid}'
        ";

        return $this->_db->fetchRow($sql);

    }
}

<?


require_once 'Zend/Db/Table/Abstract.php';

class SupportModel extends Zend_Db_Table_Abstract
{
	protected $_name = 'clients';

	public function getSupportInfoStream($data, $ctype)
	{
		$sql = "
			select * from retinfostream('{$data['u_login']}', {$ctype}, '{$data['tablename']}')
		";
		return $this->_db->fetchOne($sql);
	}

	public function getMegabitButtonService($pointID)
	{
		$sql = "
			select
				SAS.*,
				(select tarif_name from tarifs where tarif_id = SAS.tarif_id)  as tarif_name
			from stream_additional_services as SAS
			where
				point_id = {$pointID}
			order by enddate desc
		";

		return $this->_db->fetchAll($sql);
	}
}

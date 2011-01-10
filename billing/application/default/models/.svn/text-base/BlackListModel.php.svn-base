<?php
require_once('Zend/Db/Table.php');

class BlackListModel extends Zend_Db_Table
{
    protected $_name = 'black_list';
    protected $_sequence = 'black_list_seq';

	public function getBlackList()
	{
		$sql = "
			select * from black_list where is_deleted = false order by pcross desc
		";
		return $this->_db->fetchAll($sql);
	}

	public function addNewPost($dataForm)
	{

		$search = $this->searchDoublePcross($dataForm['pcross']);

		if($search == 1)
		{
			return $search;
		}

		$sql = "
			insert into black_list (client_name, pcross, phone, notes)
			values
			(
				'{$dataForm['client_name']}',
				'{$dataForm['pcross']}',
				'{$dataForm['phone']}',
				'{$dataForm['notes']}'
			)
		";
		$this->_db->fetchAll($sql);

		return 0;
	}

	public function searchDoublePcross($pcross)
	{
		$item = trim($pcross);
		$sql = "
			select * from black_list where pcross = '{$item}' and is_deleted = false
		";
		$result = $this->_db->fetchAll($sql);

		if(count($result) > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public function deleteBlackList($ID)
	{
		$sql = "
			update black_list set is_deleted = true where id = {$ID}
		";
		$this->_db->fetchAll($sql);
	}
}

?>

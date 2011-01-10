<?php
/**
 * Model for pintel table
 * 
 * @author marat
 */

require_once('BaseModel.php');

class PintelModel extends BaseModel 
{
	protected $_name = 'pintel';
	protected $_sequence = 'pintel_seq';
	
	public function saveChanges($data, $id = NULL)
	{
		if ( ! $id )
		{
			$pin = '';
			
			do
			{
				$pin = '';
			
				for ($i = 0; $i < 4; $i++)
				{
					$number = rand(100, 999);
					$pin .= $number;
				}
			}
			while( $this->checkPin($pin) );
			
			$data['login'] = $pin;
		}
		
		$pointData['u_login'] = $data['u_login'];
		$pointData['u_passwd'] = $data['u_passwd'];
		$this->_db->update('points', $pointData, "point_id = {$data['point_id']}");
		
		parent::saveChanges($data, $id);
	}
	
	public function checkPin($pin)
	{
		$sql = "
			SELECT
				id
			FROM
				cards
			WHERE
				pin = '{$pin}'
		";
		
		$cardID = $this->_db->fetchOne($sql);
		
		if ($cardID)
		{
			return true;
		}
		
		$sql = "
			SELECT
				id 
			FROM 
				pintel
			WHERE
				login = '{$pin}'
		";
		
		$pinID = $this->_db->fetchOne($sql);
		
		if ($pinID)
		{
			return true;
		}
		
		return false;
	}
}
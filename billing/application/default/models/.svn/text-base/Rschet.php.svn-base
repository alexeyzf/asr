<?
/**
 * Model for rschet table
 *  
 * @author marat
 */

require_once 'Zend/Db/Table/Abstract.php';

class Rschet extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'rschet';
	
	protected $_sequence = 'rschet_seq';
	
	public function getListByClientID($clientID)
	{
		return $this->fetchAll("client_id = {$clientID}");
	}
	
	public function saveAccount($id, $clientID, $account)
	{
		$data['client_id'] = $clientID;
		$data['schet'] = $account;
		
		if ($id)
		{
			$this->update($data, "rschet_id = {$id}");
		}
		else
		{
			$this->insert($data);	
		}
	}

        public function removeAccountRschet($rshcetID)
        {
            if($rshcetID[0] == "n")
            {
                return;
            }

            $sql = "
                delete from rschet where rschet_id = {$rshcetID}
            ";
            $this->_db->fetchAll($sql);
        }
	
	public function startTransaction()
	{
		$this->_db->beginTransaction();
	}
	
	public function rollbackTransaction()
	{
		$this->_db->rollBack();
	}
	
	public function commitTransaction()
	{
		$this->_db->commit();
	}
}

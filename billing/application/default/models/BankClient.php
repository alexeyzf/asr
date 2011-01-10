<?php
/**
 * Model for bank_client table
 *
 * @author marat
 */

require_once('Zend/Db/Table.php');

class BankClient extends Zend_Db_Table
{
	protected $_name = 'bank_client';

	const NEW_STATUS = 0;
	const DONE_STATUS = 1;
	const ERROR_STATUS = 2;
	const DUBLICATE_STATUS = 3;


 	public function getList($date = NULL, $status = 0, $order = NULL)
    {
    	if ( ! $status )
    	{
    		$status = '0';
    	}

    	$whereCond = "is_done = {$status}";

        if ($date)
        {
        	// ADDON
            $whereCond .= " AND date::character varying like '$date%' ";
        }

        return $this->fetchAll($whereCond, $order)->toArray();
    }

    public function getListExchequer($date = NULL, $status = 0, $order = NULL)
    {
    	if ( ! $status )
    	{
    		$status = '0';
    	}

    	$whereCond = "is_done = {$status} and account like '23402%'";

        if ($date)
        {
            $whereCond .= " AND currenttime::character varying like '$date%' ";
        }

        return $this->fetchAll($whereCond, $order)->toArray();
    }

 	private function setStatus($ID, $isDone)
    {
    	$data['is_done'] = $isDone;
    	$this->update($data, "id = {$ID}");
    }

	public function setError($ID)
    {
        $this->setStatus($ID, self::ERROR_STATUS);
    }

    public function setDublicate($ID)
    {
       $this->setStatus($ID, self::DUBLICATE_STATUS);
    }

    public function setDone($ID)
    {
        $this->setStatus($ID, self::DONE_STATUS);
    }

	public function addRow($date, $account, $docNumber, $amount, $clientID, $notes, $currency, $bankType, $orgName)
    {
    	if ( ! $clientID )
    	{
    		$clientID = '0';
    	}

    	$data['date']      = $date;
    	$data['account']   = $account;
    	$data['doc_num']   = $docNumber;
    	$data['amount']    = $amount;
    	$data['client_id'] = $clientID;
    	$data['notes']     = $notes;
    	$data['is_done']   = 0;
    	$data['currency']  = $currency;
    	$data['bank_type'] = $bankType;
    	$data['org_name']  = $orgName;

    	$this->insert($data);
    }

    public function CorrectBankExtract($from, $to, $date)
    {
    	$sql = "
			select * from bank_extract({$from}, {$to}, '{$date}')
		";
		return $this->_db->fetchAll($sql);
    }

	public function startTransaction()
    {
        $this->_db->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->_db->commit();
    }

    public function rollbackTransaction()
    {
        $this->_db->rollBack();
    }
}
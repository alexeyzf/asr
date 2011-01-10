<?php

require_once ('Zend/Db/Table/Abstract.php');

class HostingTaskModel extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name
	 * @var string
	 */
	protected $_name = 'hostingtasks';
	
	/**
	 * The default sequence name
	 * @var string
	 */
	protected $_sequence = 'hostingtasks_seq';
	
	public function switchOnAllHostingtByClient($clientID)
	{
		$hostingModel = new HostingModel();
		$hostings = $hostingModel->getByClientID($clientID);
		
		if ( ! is_array($hostings) ) {
			return false;
		}
		
		foreach ($hostings as $hosting) 
		{
			$this->insertRows($hosting['domain_addres'], $hosting['point_id'], 0, "Включаю");
		}
	} 
	
	private function insertRows($domain, $pointID, $taskType, $notes)
	{
		$domain = trim($domain);
			
		if ( strpos($domain, ',') )
		{
			$domains = explode(',', $domain);
		}
		elseif ( strpos($domain, ';') )
		{
			$domains = explode(';', $domain);
		}
		elseif ( strpos($domain, ' ') ) 
		{
			$domains = explode(' ', $domain);
		}  
		else 
		{
			$domains = array($domain);
		}
		
		$auth = Zend_Auth::getInstance();
        $user = $auth->getStorage()->read();
        $data['managerid'] = $user->id;
		$data['notes'] = $notes;
		
		foreach ($domains as $d)
		{
			$data['web_domain'] = trim($d);
  			$data['point_id'] = $pointID;
  			$data['task_type'] = $taskType;
  			$this->insert($data);
		}
	}
	
	public function switchOffAllHostingByClient($clientID)
	{
		$hostingModel = new HostingModel();
		$hostings = $hostingModel->getByClientID($clientID);
		
		if ( ! is_array($hostings) ) {
			return false;
		}
		
		foreach ($hostings as $hosting) 
		{
			$this->insertRows($hosting['domain_addres'], $hosting['point_id'], 2, "Выключаю");
		}
	}
}
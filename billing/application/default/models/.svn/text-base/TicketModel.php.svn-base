<?php
/**
 * Model for e_tickets table
 *  
 * @author marat
 */

require_once 'Zend/Db/Table/Abstract.php';

class TicketModel extends Zend_Db_Table_Abstract 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'e_tickets';

	public function getEvents()
	{
		$sql = "
			SELECT
				organization_code,
				event_time,
				COUNT(id) AS ticket_count,
				SUM(CASE WHEN client_id IS NOT NULL THEN 1 ELSE 0 END) AS ticket_sold_count
			FROM
				e_tickets
			GROUP BY
				organization_code,
				event_time
			ORDER BY 
				event_time DESC
		";
		
		return $this->_db->fetchAll($sql);
	}
	
	public function createEvent($orgCode, $eventTime, $rowNumber, $placeCount, $ticketPrice)
	{
		$sql = "
			SELECT
				*
			FROM
				ticket_create_event(?, ?, ?, ?, ?)
		";
		
		$result = $this->_db->fetchRow($sql, array(
			$orgCode, 
			$eventTime, 
			$rowNumber, 
			$placeCount, 
			$ticketPrice)
		);
		return $result;
	}
	
	public function checkSold($orgCode, $eventTime)
	{
		$sql = "
			SELECT
				id
			FROM
				e_tickets
			WHERE
				organization_code = ?
				AND event_time = ?
				AND client_id IS NOT NULL		
		";
		
		$id = $this->_db->fetchOne($sql, array($orgCode, $eventTime));
		
		if ($id)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function deleteEvent($orgCode, $eventTime)
	{
		$sql = "
			DELETE FROM
				e_tickets
			WHERE
				organization_code = ?
				AND event_time = ?
		";
		
		$this->_db->query($sql, array($orgCode, $eventTime));
	}
}

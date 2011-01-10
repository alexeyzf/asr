<?
/**
 * Model for phone_services_tarifs table
 *  
 * @author marat
 * @version 
 */

require_once 'BaseModel.php';

class PhoneServicesTarifs  extends BaseModel 
{
	protected $_name = 'phone_services_tarifs';
	protected $_sequence = 'peregovor_tarif_id_seq';
	
	public function getList($startDate, $endDate, $orderBy = 'prefix')
	{
		if ($orderBy == 'prefix')
		{
			$orderBy = "{$orderBy} DESC";
		}
		
		$tarifs = $this->fetchAll("
			phone_services_tarifs.start_date <= '{$endDate}'
			AND phone_services_tarifs.end_date >= '{$startDate}'
			
			",
			$orderBy);
		
		if ($tarifs)
		{
			return $tarifs->toArray();
		}
		else
		{
			return array();
		}
	}
	
    /**
     * Saves changes to db
     *
     * @param array $data - Associative column => $value array
     * @param int $id - Record ID
     */
    public function saveChanges($data, $id = NULL)
    {
        $columns = $this->_getCols();

        foreach ($data as $key => $value)
        {
            if ( ! in_array($key, $columns) )
            {
                unset($data[$key]);
            }
        }
		
        $id = intval($id);
        
        if ($id)
        {
        	$oldData['end_date'] = $data['start_date'];
            $this->update($oldData, "id = {$id}");
        }

        unset($data['id']);
        $id = $this->insert($data);

        return $id;
    }
}
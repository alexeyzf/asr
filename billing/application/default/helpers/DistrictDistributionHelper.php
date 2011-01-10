<?php
/**
 * Класс служит для разделения списка клиентов по районам
 * @author marat
 *
 */

class DistrictDistributionHelper 
{
	public static function distribute($clientsList)
	{
		$clientModel = new ClientModel();
		$clientsDistricts = $clientModel->getClientsDistricts();
		$result = array();
		
		foreach ($clientsList as $client)
		{
			if ( $clientsDistricts[ $client['client_id'] ] )
			{
				$result[ $clientsDistricts[ $client['client_id'] ] ] [ ] = $client;
			}
			else 
			{
				$result[ 'Не распознан' ] [ ] = $client;
			}
		}
		
		ksort($result);
		return $result;
	}
}
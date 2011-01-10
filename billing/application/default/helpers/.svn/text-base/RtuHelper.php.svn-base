<?php
class RtuHelper
{
	public static function	createUser()
	{
			$rs    = "20208000304118577004";
			$start = date('d.m.Y', strtotime($startdate));
			$end   = $start;


			$client = new Zend_Http_Client('https://bank24.uz:4443/SignIn.aspx');
			$client->setCookieJar();


			$responce = $client->request();

			$aspTags  = CacheHelper::getAspCacheParams($responce);

	    	$client->setParameterPost(array(
	        	'lgnLogin$LoginButton'  => 'Войти',
	        	'lgnLogin$UserName'   	=> 'sharqtelekom',
	        	'lgnLogin$Password'     => 'RG/eyC3r',
	        	'__EVENTVALIDATION'		=> $aspTags['event'],
	        	'__VIEWSTATE'			=> $aspTags['view']
	    	));
			unset($aspTags);
			$responce = $client->request('POST');



			$client->setUri('https://bank24.uz:4443/pages/reports/runreport.aspx');
			$client->setParameterGet(array(
				'id'					=> '4',
				'type'					=> '9'
	    	));
			$responce = $client->request('GET');
			$aspTags  = CacheHelper::getAspCacheParams($responce);



			$client->setParameterPost(array(
				'__VIEWSTATE' => $aspTags['view'],
				'rep_par_1'	  => $start,
				'rep_par_2'	  => $end,
				'rep_par_3'	  => $rs
	    	));
	    	$responce = $client->request('POST');

			$xml = simplexml_load_string($responce->getBody());

			foreach($xml as $item)
				{
					$row = array();

					foreach ($item->children() as $child)
					{
						if ($child->getName() == 'Дата')
						{
							$row['date'] = (string)$child;
						}

						if ($child->getName() == 'МФО1')
						{
							$row['mfo1'] = (string)$child;
						}

						if ($child->getName() == 'Счёт_клиента')
						{
							$row['account'] = (string)$child;
						}

						if ($child->getName() == 'Наименование_клиента')
						{
							$row['client_name'] = (string)$child;
						}

						if ($child->getName() == 'МФО2')
						{
							$row['mfo2'] = (string)$child;
						}

						if ($child->getName() == 'Счёт_корреспондента')
						{
							$row['account2'] = (string)$child;
						}

						if ($child->getName() == 'Наименование_корреспондента')
						{
							$row['client_name2'] = (string)$child;
						}

						if ($child->getName() == 'Номер_док_та')
						{
							$row['doc_num'] = (string)$child;
						}

						if ($child->getName() == 'Сумма')
						{
							$row['amount'] = (string)$child;
						}

						if ($child->getName() == 'Детали_платежа')
						{
							$row['notes'] = (string)$child;
						}

					}
					$rows[] = $row;

				}
			return $rows;
	}

}
?>
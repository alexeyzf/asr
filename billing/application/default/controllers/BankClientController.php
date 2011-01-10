<?php
/**
 * BankClientController
 *
 * @author
 * @version
 */

require_once('BaseController.php');
require_once('CacheHelper.php');
require_once('BankClientHelper.php');
require_once('Zend/Http/Client.php');

class BankClientController extends BaseController
{
	/**
	 * The default action - show the home page
	 */
	public function indexAction()
	{
		$this->view->options = $this->_options;

		$action = $this->_request->getParam('val');
		$this->view->action = $action;
	}

	private $_options  = array(
			'0'							  => 'Выбрать действие',
			'kapital'					  => 'Взять с Капитал-Банка',
			'national'					  => 'Взять с Нац-Банка',
			'post-bank-client' 			  => 'Разнести по счетам',
			'post-bank-client-exchequer' => 'Разнести по счетам (Казначейство)',
			'unposted-report'   		  => 'Отчет по неразнесенным',
			'posted-report'   			  => 'Отчет по разнесенным'
	);

	public function kapitalAction()
	{
		if ($this->_request->isPost())
		{

			$clientModel 	 = new ClientModel();
			$bankClientModel = new BankClient();

			$date  = $this->_request->getParam('date');
			$check = $this->_request->getParam('is_test');

			$this->view->date    = $date;
			$this->view->is_test = $check;

			try
			{
				$bankClientModel->startTransaction();

				$rows = BankClientHelper::getDataTransactions($date);
				$transactions = array();

				$total = 0;
				foreach($rows as $item)
				{
					if ( ($item['account'] != '20208000304118577004')
						  && ($item['account'] != '29801000200000974565') // online-bil
						  && ($item['account'] != '29814000504118577001') // inkass
						  && ($item['account'] != '')
					   )
					{

						$clientID = $clientModel->getClientByAccount($item['account'], 0, $item);

						if (!$clientID )
						{
							$item['notes'] = 'Не найден';
						}

						$item['client_id'] = $clientID;

						if($clientID)
						{
							$bankClientModel->addRow(
	          						$date,
	          						iconv('cp1251','UTF8', $item['account']),
	          						htmlspecialchars(iconv('cp1251','UTF8', $item['doc_num'])),
	          						$item['amount'],
	          						$clientID,
	          						htmlspecialchars(iconv('cp1251','UTF8', $item['notes'])),
	          						'000',
	          						'kapBank',
	          						htmlspecialchars(iconv('cp1251','UTF8', $item['client_name']))
          						);
						}

						array_push($transactions, $item);
						$total = $total + $item['amount'];
					}
				}

				$bankClientModel->commitTransaction();
			}
			catch(Exception $ex)
			{
				print $ex;
				$bankClientModel->rollbackTransaction();
			}

		}

		$this->view->result = $transactions;
		$this->view->total  = $total;
		$this->view->options = $this->_options;

		$this->view->action = 'kapital';

		$this->render('index');
		$this->render('kapital');
	}

	private function getNazBankData($url)
	{
		$Resultat = array();
		$my = file($url);

		for($i = 0; $i < count($my); $i++)
		{
			$my2[] = explode('|',$my[$i]);
		}

		$i=11;
		$kol = 0;

		while( $i < (count($my2)-8) )
		{
			$tmp = array();

			while( ! preg_match("/-+/",$my2[$i][0]) )
			{
				$tmp[0].=" ".trim($my2[$i][0]);
				$tmp[1].=" ".trim($my2[$i][1]);
				$tmp[2].=" ".trim($my2[$i][2]);
				$tmp[3].=" ".trim($my2[$i][3]);

				preg_match("/лтн:(\d+)/",$my2[$i][4],$e1) ;
				preg_match("/яВЕР:(\d+)/",$my2[$i][4],$e2) ;
				preg_match("/хмм:(\d+)/",$my2[$i][4],$e3) ;

				$tmp[4]['MFO'].= $e1[1];
			    $tmp[4]['SCHET'].= $e2[1];
			    $tmp[4]['INN'].= $e3[1];
				$tmp[4]['else'].=" ".trim($my2[$i][4]);
				$tmp[5].=" ".trim($my2[$i][5]);
				$tmp[6].=" ".trim($my2[$i][6]);
				$tmp[7].=" ".trim($my2[$i][7]);

				$i++;
			}

			preg_match("/лтн:(\d+)  яВЕР:(\d+)  хмм:(\d+)(.*)/",$tmp[4]['else'],$e4) ;

			$tmp[4]['else']= $e4[4];
	        $test= explode(' ',trim($tmp[1]));
	        $ndate = explode('.',trim($test[0]));
	        $ndate = $ndate[2]."-".$ndate[1]."-".$ndate[0];
	        $tmp[6] = str_replace(",","",trim($tmp[6]));
	        $tmp[5] = str_replace(",","",trim($tmp[5]));
	        $tmp[4]['else'] = preg_replace("/[\"']/",'`',$tmp[4]['else']);
		    $i++;

		    if( $tmp[6] > 0 )
		    {
	        	$Resultat[$kol][1]= trim($tmp[4]['SCHET']);
	            $Resultat[$kol][2]= trim($tmp[6]);
			    $Resultat[$kol][3]= trim($tmp[4]['else']);
			    $Resultat[$kol][4]= trim($ndate);
			    $Resultat[$kol][5]= trim($tmp[2]);
			    $kol ++;
		    }
		}

	    return  $Resultat;
	}

	public function nationalAction()
	{
		if ( $this->_request->isPost() )
		{
			$clientModel = new ClientModel();
			$bankClientModel = new BankClient();

			$fileName = $this->_request->getParam('file_name');
			$check = $this->_request->getParam('is_test');

			$this->view->file_name = $fileName;
			$this->view->is_test = $check;

			$res = array();

			try
			{
				$bankClientModel->startTransaction();

				$filePath = "/mnt/nciclbarh/naz/".$fileName;
				$clientID = 0;
				$vipiski =$this->getNazBankData($filePath);
				$i2 = 0;

				foreach ($vipiski as $tmp)
				{
					$tmp[11]=$tmp[3];

					if( preg_match_all("/(\d+)/is", iconv('cp1251','UTF8', $tmp[11]), $my) )
					{
						foreach ($my[0] as $mid)
						{
							$client = $clientModel->getClientByAccount($tmp[1], $mid);

							if( count($client) > 0 )
							{
								$clientID = $client[0][0];
							}
						}
					}

					if($clientID == 0)
					{
						$newClientID = $clientModel->getClientByAccount($tmp[1]);

						if( $newClientID )
						{
							$clientID = $newClientID;
						}
					}
					else
					{
						$newClientID = $clientModel->getClientByAccount($tmp[1], $clientID);

						if( $newClientID )
						{
							$clientID = $newClientID;
						}
						else
						{
							$newClientID = $clientModel->getClientByAccount($tmp[1]);

							if( $newClientID )
							{
								$clientID = $newClientID;
							}
							else
							{
								$clientID = 0;
							}
						}
					}

					$bankClientModel->addRow($tmp[4],
											iconv('cp1251', 'UTF8', $tmp[1]),
											iconv('cp1251', 'UTF8', $tmp[5]),
											$tmp[2],
											$clientID,
											iconv('cp1251', 'UTF8', $tmp[3]),
											'USD',
											'nazBank',
											'');

					$res[$i2][1] = iconv('cp1251', 'UTF8', $tmp[5]);
	    			$res[$i2][2] = iconv('cp1251', 'UTF8', $tmp[4]);
	    			$res[$i2][3] = iconv('cp1251', 'UTF8', $tmp[1]);
	    			$res[$i2][4] = iconv('cp1251', 'UTF8', $tmp[3]);
	    			$res[$i2]['clientid'] = iconv('cp1251', 'UTF8', $tmp[3]);
	    			$res[$i2]['summa'] = $tmp[2];
				}

				if ( ! $check == 0 )
	          	{
					$bankClientModel->commitTransaction();
	          	}
	          	else
	          	{
	          		$bankClientModel->rollbackTransaction();
	          	}
			}
			catch(Exception $ex)
			{
				print $ex;
				$bankClientModel->rollbackTransaction();
			}
		}

		$this->view->options = $this->_options;
	}

	public function postBankClientAction()
	{
		$bankClientModel  = new BankClient();
		$clientModel      = new ClientModel();
		$hostingTaskModel = new HostingTaskModel();
		$rateModel 		  = new Rates();
		$transactionModel = new FinanceModel();

		$res = array();
		$k 	 = 0;
		$j 	 = 0;

		if ($this->_request->isPost())
		{
			$date  = $this->_request->getParam('date');
			$isAll = $this->_request->getParam('is_all');

			if($isAll)
			{
				$rows = $bankClientModel->getList();
			}
			else
			{
				$rows = $bankClientModel->getList($date);
			}

			$this->view->date   = $date;
			$this->view->is_all = $isAll;

			if(count($rows))
			{
				try
				{
					$bankClientModel->startTransaction();

					foreach ($rows as $row)
					{
						$clientID 			 = $row['client_id'];
						$res[$k]['clientid'] = $clientID;

						if ($clientID == 0)
						{
							$bankClientModel->setError($row['id']);
						}
						else
						{
							$client = $clientModel->getClientByID($clientID);

							if ( ! $client['client_id'] )
							{
								$bankClientModel->setError($row['id']);
							}
							else
							{
								$rate 	   = $rateModel->getRate($row['date']);
								$docDate   = $row['date'];
								$amountUzs = round($row['amount'], 2);
								$amountUsd = $amountUzs / $rate;
								$commente  = $row['doc_num'];
								$newc 	   = str_replace(" ", "", $commente);

								$check = $transactionModel->checkTransactionOnExist($clientID, $date, $newc);

								if ( ! $check )
								{
									/*
									 * TODO: здесь должна быть проверка на валюту
									 */
									if($client['currency'] == "USD")
									{
										$amount = $amountUsd;
									}
									else
									{
										$amount = $amountUzs;
									}


									$tranType = ($row['bank_type'] == 'kapBank') ? 20 : 21;

									//обновляем баланс
									$oldBallance = $client['ballance'];
									$clientModel->increaseBallance($clientID, $amount);
									$newBallance = $client['ballance'] + $amount;

									//добавляем транзакцию
									$transactionModel->addRow($clientID, $tranType, $amountUsd, $amountUzs, 0, $date, $newc, $client['currency']);

									//фиксируем запись в банк клиенте
									$bankClientModel->setDone($row['id']);

									$res[$k]['id'] 	   		= $clientID;
									$res[$k]['amount'] 		= $amountUzs;
									$res[$k]['client_name'] = $client['client_name'];
									$res[$k]['notes'] 		= 'Разнесен';
									$k++;

									/**
									 *  Last changed at 14.05.2010 on 10:15
									 */
									if ($oldBallance <= -5 && $newBallance > -5)
									{
										if ($client['client_type_id'] == 0)
										{
											$clientModel->switchOnAllPorts($clientID);
											//ServiceHelper::activateClient($clientID);
										}
										else
										{
											$clientModel->enableAllServices($clientID);
										}

										$hostingTaskModel->switchOnAllHostingtByClient($clientID);
									}
								}
								else
								{
									$bankClientModel->setDublicate($row['id']);
									$res[$k]['id'] 		    = $clientID;
									$res[$k]['amount'] 		= $amountUzs;
									$res[$k]['client_name'] = $client['client_name'];
									$res[$k]['notes'] 		= 'Повторная транзакция. Не выполнена';
									$k++;
								}
							}
						}
					}

					$bankClientModel->commitTransaction();
				}
				catch (Exception $ex)
				{
					print $ex;
					$bankClientModel->rollbackTransaction();
				}
			}

			$this->uploadFile($date);

			$this->view->result = $res;
		}

		$this->view->options = $this->_options;
		$this->view->action = 'post-bank-client';

		$this->render('index');
		$this->render('post-bank-client');
	}

	public function postBankClientExchequerAction()
	{
		$bankClientModel  = new BankClient();

		if ($this->_request->isPost())
		{
			$date  = $this->_request->getParam('date');
			$isAll = $this->_request->getParam('is_all');

			if($isAll)
			{
				$rows = $bankClientModel->getListExchequer();
			}
			else
			{
				$rows = $bankClientModel->getListExchequer($date);
			}
			var_dump($rows);
			exit();
			$this->view->date   = $date;
			$this->view->is_all = $isAll;
		}

	}

	public function unpostedReportAction()
	{
		$bankClientModel = new BankClient();
		$rows = $bankClientModel->getList(NULL, BankClient::ERROR_STATUS, 'date DESC');
		$this->view->data = $rows;
		$this->view->options = $this->_options;
		$this->view->action = 'unposted-report';

		$this->render('index');
		$this->render('unposted-report');
	}

	public function postedReportAction()
	{
		if ($this->_request->isPost())
		{
			$date = $this->_request->getPost('date');
			$this->view->date = $date;
			$bankClientModel = new BankClient();
			$rows = $bankClientModel->getList($date, BankClient::DONE_STATUS, 'date DESC');
			$this->view->data = $rows;
		}

		$this->view->options = $this->_options;
		$this->view->action = 'posted-report';

		$this->render('index');
		$this->render('posted-report');
	}

	CONST DBASE_PATH = '/tmp/BANK/';

	private function uploadFile($date)
	{
		$fianceModel = new FinanceModel();
		$data = $fianceModel->getBankClient($date);

		$columns = array(
			array('DataDoc', 'C', 10, 0), //char(10)
			array('DocNum', 'C', 32, 0), //char(32)
			array('Dogovor', 'C', 50, 0), // char(50)
			array('DataDog', 'C', 10, 0), //char(10)
			array('KodAbon', 'N', 7, 0), //decimal(6, 0)
			array('RSchet', 'C', 50, 0), //char(50)
			array('Nazplat', 'C', 100, 0), //char(100)
			array('Summa', 'N', 20, 2), //decimal(20, 2)
			array('ValSumma', 'N', 20, 2), //decimal(20, 2)
			array('CType', 'N', 2, 0), //decimal(2 ,0)
			array('USDorSum',  "N", 1,  0), // deciamal(1, 1)
			array('BankType', 'C', 10, 0) //char(10)

		);

		$fileName = self::DBASE_PATH . $this->createFileName($date);

		if ( ! dbase_create($fileName, $columns) )
		{
			print 'Error creating dbase file ' . $fileName;
			exit;
		}

		$db = dbase_open($fileName, 2);

		if ($db)
		{
			foreach ($data as $tran)
			{

				$documentDate = date('d.m.Y', strtotime($tran['currenttime']));
				$documentNumber = $tran['commente'];
				$contractNumber = $tran['dogovor'];
				$contractDate = date('d.m.Y', strtotime($tran['dogovor_date']));
				$clientID = $tran['client_id'];
				$clientAccount = $tran['account'];
				$paymentNotes =  $tran['nazplat'];
				$amount = $tran['summas'];
				$amountUsd = $tran['summa'];
				$clientType = $tran['client_type_id'] == 4 ? 0 : 1; // if card client or not
				$bankType = $tran['trantype'] == 20 ? 'kapBank' : 'nazBank';
				$ctype = $fianceModel->getCurrencyCode($tran['client_id']);

				$row = array(
					$documentDate,
					$documentNumber,
					$contractNumber,
					$contractDate,
					$clientID,
					$clientAccount,
					$paymentNotes,
					$amount,
					$amountUsd,
					$clientType,
					$ctype,
					$bankType
				);

				dbase_add_record($db, $row);
			}

			dbase_close($db);
			$res = 1;
		}
		else
		{
			$res = 2;
		}
	}

	private function createFileName($date)
	{
		return 'b' . date('dmy', strtotime($date)) . '.dbf';
	}

	public function bankExtractAction()
	{
		if($this->_request->isPost())
		{
			$date   = $this->_request->getPost('need_date');
			$fromID = $this->_request->getPost('fromID');
			$toID   = $this->_request->getPost('toID');

			$bankModel = new BankClient();
			$result = $bankModel->CorrectBankExtract($fromID, $toID, $date);
			var_dump($result);
			exit();
		}

	}


}

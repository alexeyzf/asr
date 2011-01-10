<?php
require_once('Asr/InvoiceHelper.php');
require_once('Asr/SchetHelper.php');
require_once('Asr/FinanceHelper.php');

/**
 * Класс утилита для работы с услугами
 */
class ServiceHelper
{
	const STREAM_SERVICE_TYPE = 3000;
	const SIGMA_DIALUP_SERVICE_TYPE = 1000;
	const SMILE_DIALUP_SERVICE_TYPE = 1100;
	const NGN_CHANNEL_PRICE = 8;

	/**
	 * Активизация услуги
	 * Снимает при необходимости абон плату
	 *
	 * @param integer $pointID - идентификатор точки
	 * @param string $tableName - имя таблицы услуги
	 * @param integer $serviceID - идентификатор услуги
	 * @param string $startDate - дата начала действия услуги
	 * @param boolean $new - Флаг смены тарифа
	 * @return void
	 */
	public static function activateService($pointID, $tableName, $serviceID, $startDate, $new = false)
	{
		$authID = Zend_Auth::getInstance()->getStorage()->read()->id;

		$tarifModel 		= new TarifListModel();
		$clientServiceModel = new ClientServicesModel();
		$financeModel 		= new FinanceModel();
		$porttasksModel 	= new Porttasks();

		$service = $clientServiceModel->getServiceByID($pointID, $tableName, $serviceID);

		if ( ! $service )
		{
			// нечего включать
			return;
		}

		$serviceID = $service['service_id'];

		//списываем деньги сразу, только если услуга вступила в силу в этом месяце
		if ( date('Y-m-01') == date('Y-m-01', strtotime($startDate)) )
		{
			$tarifPrice = self::getAbonPrice($service);

			$trans = $financeModel->getTransactions($service['client_id'], FinanceHelper::AutomateAbonMinus,
									$service['servicetype_id'], date('Y-m-01'), date('Y-m-02'),  $pointID);

			//Если есть транзакция по снятию абон платы за эту точку и это не смена тарифа
			if (is_array($trans) && count($trans) && ! $new ) {
				//подсчитываем сколько нужно вернуть
				$amount = $tarifModel->calculateTarifPrice($tarifPrice, 2, $startDate);
				//возвращаем деньги
				$financeModel->addTransaction($service['client_id'], $service['servicetype_id'],
										FinanceHelper::AbonPlus, $amount, $authID, $pointID);
			}
			else
			{
				//подсчитываем сколько нужно списать
				if ( $service['group_name'] != 'reserved' )
				{
					$amount = $tarifModel->calculateTarifPrice($tarifPrice, 1, $startDate);
				}
				else
				{
					$amount = $tarifPrice;
				}

				//списываем деньги
				$financeModel->addTransaction($service['client_id'], $service['servicetype_id'],
										FinanceHelper::AbonMinus, $amount, $authID, $pointID);
			}
			//добавляем запись в счет-фактуру
			InvoiceHelper::insertInvoiceDetails($financeModel->getAdapter(), date('Y-m-d'),
												$service['client_id'], $pointID,
												$service['tarif_id'], $serviceID, $amount,
												1, $service['servicetype_id']);

			$clientServiceHistoryModel = new ClientServicesHistoryModel();
			$servicesStartDate = $clientServiceHistoryModel->getServicesStartDate($service['client_id']);

			if ($servicesStartDate && $servicesStartDate < date('Y-m-01') )
			{
				//если нет счет-фактуры за прошлый месяц (значит клиент  на расскроссе)
				//добавляет строчку в счет-фактуру этого месяца с абон платой за прошлый месяц
				InvoiceHelper::checkNInsertPreviosMonthAbonPay($financeModel->getAdapter(),
									$service['client_id'], date('Y-m-d'), $pointID, $tarifPrice);
			}

			//Если произошла смена тарифа
			if ($new)
			{
				$endMonth = date('Y-m-t');
				$startNextMonth = date('Y-m-d', strtotime('+1 day ' . $endMonth));

				//обновляем счет на следующий месяц
				SchetHelper::updateAmount($financeModel->getAdapter(), $service['client_id'], $startNextMonth,
										$service['servicetype_id'], $tarifPrice);
			}
		}

		// находим и выключаем старую услугу
		self::deactivateService($pointID, $tableName, NULL, $startDate, $serviceID);

		//включаем услугу
		$clientServiceModel->enableService($tableName, $serviceID, $startDate);

		//поднимаем порт
		$porttasksModel->addPointsTasks(array($pointID), Porttasks::TASK_TYPE_ON);
	}

	/**
	 *
	 * @var string
	 */
	const RESERV = 'резервирование';

	/**
	 * Дезактивизация услуги
	 * Возвращает при необходимости абон плату
	 * Метод не должен вызываться при блокировании услуги
	 *
	 * @param integer $pointID - идентификатор точки
	 * @param string $tableName - имя таблицы услуг
	 * @param integer $serviceID - идентификатор отключаемой услуги (при null отключает активную)
	 * @param string $endDate - дата окончания услуги
	 * @param integer $excludeID - идентификатор исключающей услуги (при включении новой)
	 * @return void
	 */
	public static function deactivateService($pointID, $tableName, $serviceID, $endDate, $excludeID = null)
	{
		$authID = Zend_Auth::getInstance()->getStorage()->read()->id;

		$tarifModel = new TarifListModel();
		$clientServiceModel = new ClientServicesModel();
		$financeModel = new FinanceModel();
		$porttasksModel = new Porttasks();

		$service = $clientServiceModel->getActiveService($pointID, $tableName, $serviceID, $excludeID);

		if ( ! $service )
		{
			// нечего выключать
			return;
		}

		$serviceID = $service['service_id'];

		//возвращаем деньги только если услуга отключилась в этом месяце и это был не резрев
		if ( date('Y-m-01') == date('Y-m-01', strtotime($endDate))
			&& $service['speed'] != self::RESERV)
		{
			$tarifPrice = self::getAbonPrice($service);

			//подсчитываем сколько нужно вернуть
			$amount = $tarifModel->calculateTarifPrice($tarifPrice, 1, $endDate);

			//возвращем деньги
			$financeModel->addTransaction($service['client_id'], $service['servicetype_id'], FinanceHelper::AbonPlus, $amount, $authID, $pointID);

			//обновляем счет-фактуру
			$invoiceID = InvoiceHelper::getInvoiceID($financeModel->getAdapter(), $service['client_id'], date('Y-m-d'));
			InvoiceHelper::insertOtherTran($financeModel->getAdapter(), date('Y-m-d'), $invoiceID,
										$pointID, FinanceHelper::AbonPlus, 'Смена тарифного плана', $amount);
		}

		//выключаем услугу
		$clientServiceModel->disableService($tableName, $serviceID, $endDate);
	}

	/**
	 * Плата за доп ip
	 * @var integer
	 */
	CONST ADD_IP = 1.5;

	/**
	 * Плата за dns для хостинга
	 * @var integer
	 */
	const HOSTING_DNS = 2.5;

	/**
	 * Подсчет абон платы
	 * Разнится для ngn (умножается на количество номеров);
	 * tradtel, collacation, isdn (берется из другой колонки);
	 * adsl, wifi (прибавляется сумма за доп адреса);
	 * hosting (прибавлется плата за dns)
	 *
	 * @param array $serviceData
	 * @return float
	 */
	public static function getAbonPrice($serviceData)
	{
		if ($serviceData['tablename'] == 'tradtel'
			|| $serviceData['tablename'] == 'collacation'
			|| $serviceData['tablename'] == 'isdn')
		{
			//для collacation и tradtel - абонентская плата каждый раз разная из-за
			// и храниться в колонке abon_price
			$tarifPrice = $serviceData['abon_price'];
		}
		elseif ($serviceData['tablename'] == 'ngn')
		{

			//для ngn - абонентская плата должна умножаться на количество номеров
			$ngnModel = new NgnModel();
			$numbersCount = $ngnModel->getNgnNumbersCount($serviceData['point_id']);
			$channelCount = $ngnModel->getNgnCountChannel($serviceData['point_id']);

			$tarifPrice = $serviceData['tarif_price'] * $numbersCount;
			$tarifPrice = $tarifPrice + ($channelCount * ServiceHelper::NGN_CHANNEL_PRICE);
		}
		elseif (($serviceData['tablename'] == 'adsl'
				|| $serviceData['tablename'] == 'wifi')
				&& $serviceData['group_name'] != 'not_garant')
		{
			//для adsl - прибавляется плата за доп адреса

			$pointIpModel = new PointIpAddresses();
			$date = date('Y-m-d', strtotime('+1 day ' . $serviceData['startdate']));
			$ipCount = count($pointIpModel->getPointIpAddressesOnDate($serviceData['point_id'], $date));

			$ipCount = $ipCount - 1;

			if ( $ipCount > 0 )
			{
				$ipAmount = self::ADD_IP * $ipCount;
				$tarifPrice = $serviceData['tarif_price'] += $ipAmount;
			}
			else
			{
				$tarifPrice = $serviceData['tarif_price'];
			}
		}
		elseif ($serviceData['tablename'] == 'hosting')
		{
			//для хостинга добавляется еще плата за DNS
			$tarifPrice = $serviceData['tarif_price'] += self::HOSTING_DNS;
		}
		else
		{
			 $tarifPrice = $serviceData['tarif_price'];
		}

		if ($serviceData['discount'] > 0 && $serviceData['discount'] <= 100
			&& $serviceData['tablename'] != 'additional_services')
		{
			return ((100 - $serviceData['discount']) / 100) * $tarifPrice;
		}
		else
		{
			return $tarifPrice;
		}
	}

	private $_serviceTypeModel;
	private $_pointModel;
	private $_clientModel;
	private $_financeModel;
	private $_serviceModel;
	private $_authID;

	/**
	 * Сохранение услуги
	 *
	 * @param $serviceData
	 * @return string
	 */
	public function saveService($serviceData)
	{
		$this->_serviceTypeModel = new ListServiceModel();
		$this->_pointModel 		 = new EditPointModel();
		$this->_clientModel 	 = new ClientModel();
		$this->_financeModel 	 = new FinanceModel();
		$this->_serviceModel 	 = Zend_Controller_Action_Helper_Template::createModel($serviceData['tablelink']);

		$serviceData['need_cross'] = $this->_serviceTypeModel->returnStatusNeedCross($serviceData['servicetype_id']);
		$serviceData['login'] 	   = $this->_pointModel->getLogin($serviceData['point_id']);
		$clientID 				   = $serviceData['client_id'];
		$client 				   = $this->_clientModel->getClientByID($clientID);

		$this->_authID = Zend_Auth::getInstance()->getStorage()->read()->id;

		$oldData 	= $this->_pointModel->getServiceInfo($serviceData['tablelink'], $serviceData['id']);


		$authID = Zend_Auth::getInstance()->getStorage()->read()->id;

		// Логируем
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::SERVICE_WRITTEN_DOWN, $_SERVER['REQUEST_URI'], $clientID, $serviceData['point_id']);

		try
		{
			$this->_financeModel->startTransaction();

            //снятие за модем
			if ( is_array($serviceData['modem']) && $serviceData['modem']['modem_serial'] && ! $serviceData['modem']['modem_id'] )
			{
				$this->_financeModel->addTransaction($serviceData['client_id'], $serviceData['servicetype_id'], FinanceHelper::ModemPrice, $serviceData['modem']['modem_price'], $authID, $serviceData['point_id']);
				$serviceData['modem']['client_id'] = $serviceData['client_id'];
			}

			if ( $client['client_type_id'] == 1 )
			{
				return $this->savePhysicalService($oldData, $serviceData);
			}
			else
			{
				return $this->saveCorpService($oldData, $serviceData);
			}
		}
        catch (Exception $ex)
        {
        	print $ex;
            $this->_financeModel->rollBackTransaction();
        }
	}

	/**
	 * Сохранение услуги для физических лиц
	 * @param array $oldData
	 * @param array $serviceData
	 * @return string
	 */
	private function savePhysicalService($oldData, $serviceData)
	{
		$ID = $serviceData['id'];
		$serviceTypeID = $serviceData['servicetype_id'];
		$serviceData['modem']['point_id'] = $serviceData['point_id'];

		$tarifListModel = new TarifListModel();
		$dynoTarifModel = new DynamicTarifModel();

		// Если это новая услуга
		if ( ! $ID )
		{
			// Eсли отмечено снять регистрационную плату
			if ( $serviceData['reg_pay_enable'] )
			{
				$this->_financeModel->addTransaction($serviceData['client_id'], $serviceTypeID, FinanceHelper::Register, $serviceData['reg_pay'], $this->_authID, $serviceData['point_id']);
			}

			$serviceData['penable'] = new Zend_Db_Expr('false');
			$ID = $this->_serviceModel->saveChanges($serviceData);


			$groupName  = $tarifListModel->getTarifData($serviceData['tarif_id']);
			if($groupName['group_name'] == 'special')
			{
				$flag = $dynoTarifModel->insertDynamicOptionsRow($serviceData['tarif_speed_up_unlim'], $serviceData['tarif_speed_unlim'], $serviceData['tarif_price_unlim'], 'empty', $ID);
			}

			//Если это стрим или диалап клиент
			if ( $serviceTypeID == ServiceHelper::STREAM_SERVICE_TYPE
				|| $serviceTypeID == ServiceHelper::SIGMA_DIALUP_SERVICE_TYPE)
			{
				$this->changeRadiusParams($serviceData['point_id'], $serviceTypeID);
			}

			$this->_financeModel->commitTransaction();

			// Если необходима кроссировка
			if ( $serviceData['need_cross'] )
			{
				return 'cross';
			}
			else
			{
				return 'main';
			}
		}
		else // Редактирование услуги
		{
			$oldID = $ID;
            unset($serviceData['id']);

            // Если тариф не изменился
            if ($oldData['tarif_id'] == $serviceData['tarif_id'] )
            {
            	$this->_serviceModel->saveChanges($serviceData, $ID);
            }
            else //Если тариф поменялся
            {
            	$serviceData['startdate'] = date('Y-m-01', strtotime('+1 month'));
            	$this->_serviceModel->saveChanges($serviceData);
            }

            $this->_financeModel->commitTransaction();
            return 'main';
		}
	}

	/**
	 * Текущий год, можно поставить -1 year,
	 * тогда пересчет будет работать за предыдущий год (правильность не гарантируется)
	 * @var string
	 */
	const CURRENT_YEAR = 'now';

	/**
	 * ПЕРЕСЧЕТ
	 * @param array $oldData - Старые данные по услуге
	 * @param array $serviceData -Новые данные по услуге
	 * @return array Результат пересчета
	 */
	private function recalcService($oldData, $serviceData)
	{
        //var_dump(array('old' => $oldData, 'serviceData' => $serviceData)); exit;
		if ($oldData['is_forced'] != $serviceData['is_forced'])
		{
			if ($serviceData['is_forced'] == false)
			{
				$serviceData['paidto'] = null;
			}
			else
			{
				$serviceData['paidto'] = date('Y-m-t', strtotime($serviceData['startdate']));
			}
		}

		$clientID = $serviceData['client_id'];
        $serviceTypeID = $serviceData['servicetype_id'];
        $ID = $serviceData['id'];
        $tableLink = $serviceData['tablelink'];
        $pointID = $serviceData['point_id'];

        $year = date('Y', strtotime(self::CURRENT_YEAR));

		// МЕХАНИЗМ

        /*
         * Допустим изменения вступили в силу
         * необходимо пересчитать всю сумму абонентской платы с начала года
         */

        //получаем всю историю услуг по данному клиенту
        $clientModel = new ClientModel();
        $allClientServices = $clientModel->getClientServicesFullHistory($clientID, $year); 

        //получаем информацию о выбранном тарифе для редиктируемой услуги
        $tarifListModel = new TarifListModel();
        $tarifComponentModel = new TarifComponents();
        $tarif = $tarifListModel->getTarifData($serviceData['tarif_id']);

        //заполняем необходимые данные по услуге, так как они не пришли из формы
        $serviceData['tarif_price'] = $tarif['tarif_price'];
        $serviceData['tarif_limit'] = $tarif['limit'];
        $serviceData['tarif_name'] = $tarif['tarif_name'];
        $serviceData['unlimit'] = $tarif['unlimit'];
        $serviceData['tablename'] = $tarif['tablelink'];
        $serviceData['traffic_excess'] = $tarifComponentModel->getTrafficExcess($serviceData['tarif_id']);
        $serviceData['old_tarif_id'] = $oldData['tarif_id'];
        $serviceData['old_startdate'] = $oldData['startdate'];
        $serviceData['old_enddate'] = $oldData['enddate'];

        $currentServicePosition = -1;

        //вставляем услугу в массив если она вступила в силу
        if ( $serviceData['is_forced'] )
        {
        	$founded = false;

        	//Заменяем если она уже была в силе
        	foreach ($allClientServices as $key => $service)
        	{
        		if ($service['servicetype_id'] == $serviceTypeID
        			&& $service['point_id'] == $serviceData['point_id']
        			&& $service['id'] == $serviceData['id'])
        		{
                    /*if (!$serviceData['discount'])
                    {
                        !$serviceData['discount'] = $oldData['discount'];
                        !$serviceData['discountcomment'] = $oldData['discountcomment'];
                    }*/

        			$currentServicePosition = $key;
        			$allClientServices[ $key ] = $serviceData;
        			$founded = true;
        			break;
        		}
        	}

        	//вставляем, если нет
        	if ( ! $founded )
        	{
        		$allClientServices[] = $serviceData;
        		$currentServicePosition = count($allClientServices) - 1;
        	}
        }
        else
        {
        	//убираем услугу из массива, если сняли галку "Вступило в силу"
        	foreach ($allClientServices as $key => $service)
        	{
        		if ($service['servicetype_id'] == $serviceTypeID
        			&& $service['point_id'] == $serviceData['point_id']
        			&& $service['id'] == $serviceData['id'])
        		{
        			$currentServicePosition = $key;
        			unset ($allClientServices[ $key ]);
        			break;
        		}
        	}
        }

        $monthServices = array();

        $maxEndDate = date('Y-01-01');
        $prev = -1;

        //ищем предыдущую услугу
        for ($j = 0; $j < count($allClientServices); $j++)
        {
        	if ($allClientServices[$j]['point_id'] == $serviceData['point_id']
        		&& $allClientServices[$j]['servicetype_id'] == $serviceData['servicetype_id']
        		&& $allClientServices[$j]['startdate'] < $serviceData['startdate']
        		&& $allClientServices[$j]['enddate'] > $serviceData['startdate']
        		&& $allClientServices[$j]['enddate'] >= $maxEndDate
        		&& $j != $currentServicePosition)
        	{
        		$maxEndDate = $allClientServices[$j]['enddate'];
        		$prev = $j;
        	}
        }

		//вставляем конец действия предыдущей услуги равным началу дествия текущей услуге
        if ($prev >= 0)
        {
        	$allClientServices[$prev]['enddate'] = $serviceData['startdate'];
        }

        $streamTrafficModel = new StreamTrafficModel();
        $pointIpModel = new PointIpAddresses();
        $directionControlModel = new DirectionControlModel();
        $ngnModel = new NgnModel();
        $tarifModel = new TarifListModel();
        $financeModel = new FinanceModel();

        $monthAmount = array();
        $monthServices = array();

        //посдсчет количества месяцев, за которые нужно пересчитать
        $minDate = date('Y-m-d', strtotime(self::CURRENT_YEAR));

        //Данная проверка работает только если мы осуществляем пересчет за предыдущий год
        if ($minDate < date('Y-12-31', strtotime(self::CURRENT_YEAR)))
        {
        	$lastMonth = date('n', strtotime( $minDate ));
        }
        else
        {
        	$lastMonth = 12;
        }

        //все траназакции по снятию, участвующие в генерации счет-фактур
        $transactionTypes = InvoiceHelper::getIncludedTransactions();
        //плюс переговоры
        $transactionTypes[] = 7122;

		$atsBonus = new AtsBonus();

		//для каждого месяца используем алгоритм генерации счет-фактур
        for ($month = 1; $month <= $lastMonth; $month++)
        {
        	if ($month == $lastMonth)
        	{
				unset($transactionTypes[0]);
				unset($transactionTypes[1]);
        	}

        	$monthAmount[$month] = 0;

        	//определяем начало и конец месяца
        	if ($month < 10)
        	{
        		$startMonthDate = date('Y', strtotime(self::CURRENT_YEAR)) . "-0{$month}-01";
        	}
        	else
        	{
        		$startMonthDate = date('Y', strtotime(self::CURRENT_YEAR)) . "-{$month}-01";
        	}
        	$startMonthStamp = strtotime($startMonthDate);
        	$endMonthStamp = strtotime('+1 month ' . $startMonthDate);
        	$endMonth = date('Y-m-01', $endMonthStamp);
        	$startMonth = date('Y-m-01', $startMonthStamp);

        	//получаем все транзакции, также учитываемые при генерации счет-фатур
        	$transactions = $financeModel->getTransactions($clientID, $transactionTypes, null, $startMonth, $endMonth);//, $pointID);
        	foreach ($transactions as $tran)
        	{
        		if ($tran['trantype'] > 100)
        		{
        			$monthAmount[$month] += $tran['summa'];
        		}
        		else
        		{
        			$monthAmount[$month] -= $tran['summa'];
        		}
        	}

            //var_dump($allClientServices); exit;
        	//подсчет суммы к списанию по данной услуге за месяц
        	foreach ($allClientServices as $key => $service)
        	{
        		//если услуга предоставлялась в месяце $month
        		if ($service['startdate'] < $endMonth
        			&& $service['enddate'] > $startMonth)
        		{
        			$traffic = 0;
					$tarifPrice = self::getAbonPrice($service);
                    //var_dump($month . ':' . $tarifPrice);
					//если тариф начал действовать в этом мясяце
					if ( date('n', strtotime($service['startdate'])) == $month
						&& date('Y', strtotime($service['startdate'])) == $year
						&& $service['tablename'] != 'additional_services'
						&& $service['group_name'] != 'reserved')
					{
						$tarifPrice = $tarifModel->calculateTarifPrice($tarifPrice, 1, $service['startdate']);
					}
					elseif ( date('n', strtotime($service['enddate'])) == $month //если тариф закончил действовать в текущем месяце
						&& date('Y', strtotime($service['enddate'])) == $year
						&& $service['tablename'] != 'additional_services'
						&& $service['group_name'] != 'reserved')
					{
						$tarifPrice = $tarifModel->calculateTarifPrice($tarifPrice, 2, $service['enddate']);
					}

					//Для adls и wifi также считается сумма по превышению лимита
                    //Нужно заметить, что скидка на перелимит не распростронятся
					if ($service['tablename'] == 'adsl'
						|| $service['tablename'] == 'wifi')
					{
						if ($service['servicetype_id'] == 3000)
						{
							$traffic = $streamTrafficModel->getStreamTraffic($service['login'], $startMonth, $endMonth);
						}
						else
						{
							//считаем траффик по ip адресам
							$ipData = $pointIpModel->getIpHistory($service['point_id'], $startMonth, $endMonth);

							$traffic = 0;
							foreach ($ipData as $ipRow)
							{
								$trafficStart = max(
									array (
										$ipRow['start_date'],
										$startMonth
									)
								);
								$trafficEnd = min(
									array (
										$ipRow['end_date'],
										$endMonth
									)
								);

								$traffic += $directionControlModel->getTraffic(
									array($ipRow['ip_address']),
									$trafficStart,
									$trafficEnd,
									$service['calc_traffic_type']);
							}
						}

						//>>>>>
						if ($service['enddate'] < $endMonth)
						{
							$days = date('j', strtotime($service['enddate']));
							$daysInMonth = date('t', strtotime($service['enddate']));
							$tarifLimit = $days / $daysInMonth * $service['tarif_limit'];
						}
						elseif ($service['startdate'] > $startMonth)
						{
							$startDay = date('j', strtotime($service['startdate']));
							$daysInMonth = date('t', strtotime($service['startdate']));
							$days = $daysInMonth - $startDay + 1;
							$tarifLimit = $days / $daysInMonth * $service['tarif_limit'];
						}
						else
						{
							$tarifLimit = $service['tarif_limit'];
						}
						//<<<<<

						$overLimit = $traffic - $tarifLimit;

						if ( $overLimit > 0 && ! $service['unlimit'] )
						{
							$tarifPrice = $tarifPrice + $service['traffic_excess'] * $overLimit;
		                }
					}
                    
					$atsBonusAmount = $atsBonus->getAmount($clientID, $service['point_id'], $startMonth, $endMonth);

					if ($atsBonusAmount)
					{
						$tarifPrice -= $atsBonusAmount;
					}

					$service['tarif_price'] = $tarifPrice;
					$monthServices[$month][] = $service;
					$monthAmount[$month] += $tarifPrice;
        		}
        	}
        }

        $invoiceModel = new InvoiceModel();
        $financeModel = new FinanceModel();

        //считаем по счет-фактурам сумму по данному клиенту
        $invoiceData = $invoiceModel->getClientAmounts($clientID, $year, $lastMonth);

        $compareTable = array();

        //записывает в массив суммы по счте-фактуре и суммы по пересчету
        foreach ($monthAmount as $month => $amount)
        {
        	$compareTable[$month]['invoice_amount'] = round($invoiceData[$month]['amount_usd'], 2);
        	$compareTable[$month]['recalc_amount'] = round($amount, 2);
        }

        $result['compare'] = $compareTable;
        $result['all_services'] = $monthServices;
        $result['point_id'] = $serviceData['point_id'];
        $result['year'] = $year;
        return $result;
	}

	/**
	 * Сохранение услуги для корпоративных клиентов
	 *
	 * @param array $oldData - Старые данные
	 * @param array $serviceData - Новые данные
	 * @return string|array - Результат сохранения
	 */
	private function saveCorpService($oldData, $serviceData)
	{
		$clientID = $serviceData['client_id'];
        $serviceTypeID = $serviceData['servicetype_id'];
        $ID = $serviceData['id'];
        $tableLink = $serviceData['tablelink'];

        $serviceData['modem']['point_id'] = $serviceData['point_id'];

        //за прошлый год ничего менять нельзя
        if ($serviceData['enddate'] < date('Y-01-01', strtotime(self::CURRENT_YEAR)))
        {
        	return 'error';
		}

		//Флаг изменения услуги
		$isChanged = is_array($oldData) && ($oldData['startdate'] != $serviceData['startdate']
        		|| $oldData['enddate'] != $serviceData['enddate']
        		|| $oldData['tarif_id'] != $serviceData['tarif_id']
        		|| $oldData['discount'] != $serviceData['discount']
        		|| $oldData['is_forced'] != $serviceData['is_forced']);

        //Флаг сдвига дат у услуги
        $isShiftEndDate = is_array($oldData) && ($oldData['startdate'] == $serviceData['startdate']
        		&& $oldData['enddate'] != $serviceData['enddate']
        		&& $oldData['tarif_id'] == $serviceData['tarif_id']
        		&& $oldData['discount'] == $serviceData['discount']
        		&& $oldData['is_forced'] == $serviceData['is_forced']);

		/*
		 * проверкa на пересчет
		 * 1) возникает при редактировании услуги
		 * 2) услуга вступает в силу или уже была в силе
		 * 3) изменили дату начала или дату окончания действия услуги или
		 *    изменили тариф у старой услуги или поставили/сняли галку "Вступило в силу" у старой услуги
		 */
        if ( (is_array($oldData) && $oldData['id']

        		//услуга вступает в силу или уже была в силе
        		&& ($oldData['is_forced'] || $serviceData['is_forced'])

        		//только по услугам начавшимся в прошлых месяцах
        		&& ($oldData['startdate'] < date('Y-m-01') || $serviceData['startdate'] < date('Y-m-01'))

        		//хоть что то поменялось
        		&& $isChanged)
        	|| (! $ID && $serviceData['startdate'] < date('Y-m-01')))
        {
        	return $this->recalcService($oldData, $serviceData);
        }

		// Если это новая услуга
		if ( ! $ID )
		{
			// Eсли отмечено снять регистрационную плату
			if ( $serviceData['reg_pay_enable'] )
			{
				$this->_financeModel->addTransaction($clientID, $serviceTypeID, FinanceHelper::Register,
								$serviceData['reg_pay'], $this->_authID, $serviceData['point_id']);

				if ($serviceData['reg_pay'] > 0)
				{
					$invoiceID = InvoiceHelper::getInvoiceID($this->_financeModel->getAdapter(), $clientID, date('Y-m-d'));
					InvoiceHelper::insertOtherTran($this->_financeModel->getAdapter(), date('Y-m-d'), $invoiceID,
							$serviceData['point_id'], FinanceHelper::Register, 'Регистрационная плата за подключение',
							$serviceData['reg_pay']);
				}
			}

			//снимаем галку при сохранении, так как услугу надо активировать (снять абон плату)
			$isEnable = $serviceData['is_forced'];
			$serviceData['is_forced'] = new Zend_Db_Expr('false');

			$ID = $this->_serviceModel->saveChanges($serviceData);

			//Если услуга всутпила в силу
			if ( $isEnable )
			{
				//Включаем услугу
				ServiceHelper::activateService($serviceData['point_id'], $tableLink, $ID, $serviceData['startdate'], true);
			}

			//Если это стрим или диалап услуга
			if ( $serviceTypeID == ServiceHelper::STREAM_SERVICE_TYPE
				|| $serviceTypeID == ServiceHelper::SIGMA_DIALUP_SERVICE_TYPE)
			{
				//меняем параметры для радиуса
				$this->changeRadiusParams($serviceData['point_id'], $serviceTypeID);
			}

			$this->_financeModel->commitTransaction();

			return 'main';
		}
		else // Редактирование услуги
		{
			$oldID = $ID;
            unset($serviceData['id']);

            $isEnable = $serviceData['is_forced'];
            $serviceData['is_forced'] = new Zend_Db_Expr('false');

            if ($serviceData['is_add_application'])
            {
            	$networkParamsModel = new NetworkParamsModel();
                $networkParamsModel->updateAdmin($serviceData['point_id'], $serviceTypeID, 0);
                unset($serviceData['is_add_application']);
            }

            //Если ничего не поменялось
            if ( ! $isChanged )
            {
            	//записываем все изменения и выходим
            	$this->_financeModel->commitTransaction();
                return 'main';
            }

            // Если тариф не изменился
            if ($oldData['tarif_id'] == $serviceData['tarif_id'] )
            {
            	if ( $isEnable && $oldData['is_forced'])
                {
                	$serviceData['is_forced'] = new Zend_Db_Expr('true');
                }

                $this->_serviceModel->saveChanges($serviceData, $ID);

                //Если услуга вступила в силу и не просто сдвиг окончания действия услуги
                if ( $isEnable && ! $isShiftEndDate )
                {
	             	//Включаем услугу
                 	ServiceHelper::activateService($serviceData['point_id'], $tableLink, $ID, $serviceData['startdate']);
                }

                $this->_financeModel->commitTransaction();
                return 'main';
			}
            else //Если тариф поменялся
            {
            	$ID = $this->_serviceModel->saveChanges($serviceData);

				//Если услуга всутпила в силу
                if ( $isEnable )
                {
                	//Включаем новую услугу
                    ServiceHelper::activateService($serviceData['point_id'], $tableLink, $ID, $serviceData['startdate']);
				}

                $this->_financeModel->commitTransaction();

				return 'add_agree';
            }
		}
	}

	/**
	 * Изменение параметров услуги для радиуса
	 *
	 * @param integer $pointID
	 * @param integer $serviceTypeID
	 * @return void
	 */
	private function changeRadiusParams($pointID, $serviceTypeID)
	{
		$radcheckModel  = new Radcheck();
		$radreplyModel  = new Radreply();
		$userGroupModel = new UserGroup();

		//Добавляем записи в radcheck, usergroup, radreply
		$point = $this->_pointModel->fetchRecordByID($pointID);

		$radcheckModel->deleteUser($point['u_login'], $serviceTypeID);
		$radcheckModel->addUser($point['u_login'], $point['u_passwd'], $serviceTypeID);

		if ( $serviceTypeID == ServiceHelper::STREAM_SERVICE_TYPE )
		{
			$userGroupModel->add($point['u_login'], UserGroup::STREAM_GROUP);
		}
		else
		{
			$userGroupModel->add($point['u_login'], UserGroup::CARD_USER_GROUP);
		}

		$radreplyModel->deleteUser($point['u_login'], $serviceTypeID);
	}

	/**
	 *  Метод при откате заявки позволяет списывать абон плату (Для корпов)
	 *  Метод следует переделать с проверкой на то было ли снята абон плата 1го числа тек месяца.
	 */
	public static function demandActivateService($point_id, $tablename = null)
	{
		$demandModel = new DemandModel();

		if ( $point_id )
		{
			$now = date('Y-m-d');

			$info = $demandModel->getDataSID($point_id);
			$id = $demandModel->starttest($info, $point_id);
			return array($info['tablename'], $id, $info['servicetype_id']);
			//ServiceHelper::activateService($point_id, $info['tablename'], $info['service_id'], $now, $flag = 1);
		}
		return 1;
	}

	/**
	 * Активизация клиента, ушедшего в заявки
	 * @param integer $clientID
	 * @return void
	 */
	public static function activateClient($clientID)
	{
		$clientModel =new ClientModel();
		$pointModel = new EditPointModel();
		$techHistoryModel = new TechHistory();
		$financeModel = new FinanceModel();
		$clientServiceModel = new ClientServicesModel();
		$points = $clientModel->getClientPoints($clientID, -1);

		if ( ! is_array($points) )
		{
			return;
		}

		foreach ($points as $pointID)
		{
			if ( $techHistoryModel->checkPointHistory($pointID) )
			{
				continue;
			}

			$pointModel->rollbackStatusCross($pointID);
			list($serviceTable, $serviceID, $serviceType) = self::demandActivateService($pointID);

			$transactions = $financeModel->getTransactions($clientID, 1001, $serviceType, date('Y-m-01'), date('Y-m-t'), $pointID);

			if ( ! count ($transactions) )
			{
				self::activateService($pointID, $serviceTable, $serviceID, date('Y-m-d'));
			}
			else
			{
				$clientServiceModel->enableService($serviceTable, $serviceID, date('Y-m-d'));
			}
		}
	}

	public static function activateStreamFromReserved($pointID, $serviceID, $tarifID, $startdate)
	{
		$tarifListModel = new TarifListModel();
		$adslModel      = new AdslModel();
		$financeModel   = new FinanceModel();
		$clientModel    = new ClientModel();
		$portTasksModel = new Porttasks();

        $auth    = Zend_Auth::getInstance();
        $manager = $auth->getStorage()->read();

		if($tarifID)
		{
			//selectOldTarif
			$oldTarif = $tarifListModel->selectOldTarif($pointID, 'adsl', $serviceID);

			// Переоткрываем услугу
			$adslModel->closeServiceAndOpenNew($serviceID, $startdate, $oldTarif, 'adsl', $tarifID);

			// Считаем деньги
			$amountBytarif = $tarifListModel->getTarifData($tarifID);
			$amount 	   = $tarifListModel->getTarifPrice($tarifID, 2, $startdate);

			$total = $amountBytarif['tarif_price'] - $amount;

			// Килаем транзакцию и списываем с баланса
			$financeModel->addTransaction($clientModel->getClientIDfromPointID($pointID), 3000, 1001, $total, $manager->id, $pointID, "Смена тарифа с резерва");

			$pointInfo = $clientModel->getInfo($pointID);

			$pointInfo['tarif_id']  = $tarifID;
			$pointInfo['portspeed'] = $portTasksModel->getSpeedByTarifID($tarifID);

			// Кидаем указание на порт.
			$portTasksModel->addTask($pointInfo, 0);
		}
	}
}

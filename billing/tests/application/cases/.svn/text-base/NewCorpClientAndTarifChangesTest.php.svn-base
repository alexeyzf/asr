<?php

class NewCorpClientAndTarifChangesTest extends ControllerTestCase
{
	private function CreateClient() 
	{
		// Login GET
		$this->loginAndTestGet('employee/index');
		// POST
		$this->request->setMethod('POST')->
			setPost(array(
				'client_type_id' => '0', // тестируем корпов
				'client_name' => $this->generateRandomStr(), // название организции
				'country_id' => '0', // ташкент
				'address' => 'TEST TEST TEST', // адрес
				'client_orient' => 'TEST TEST TEST', // ориентир
				'phone' => '2222222', // контактный телефон
				'contact_name' => 'TEST TEST TEST', // контактное лицо
				'pcross' => $this->generateRandomValidCrossPhone(), // телефон для кросса
				'pcross_owner' => 'TEST TEST TEST', // на кого кросс
				'email' => 'test@test.com', // e-mail
				'fax' => '2222222', // факс
				'passp_n' => '', // номер паспорта
				'ruvd_id' => '10011', // рувд
				'new_rovd'=> '', // какое-то скрытое поле
				'test_days'=> '0', // тестовые дни - нужно на них позже обратить особое внимание
				'some' => array($this->generateValidAccountNumber()), // расчетные счета
				'sign_name' => 'TEST TEST TEST', // контакное лицо
				'post_sign_name' => 'TEST TEST TEST', // повтор контактного лица
				'legaladdress' => 'TEST TEST TEST', // адресс прописки
				'inn' => $this->generateValidInn(), // ИНН
				'mfo' => '1234567', // МФО   похоже нигде не проверяется
				'okonx' => '1234567', // ОКОНХ похоже нигде не проверяется
				'bank_id' => '80005', // банк
				'boss_id' => '0' // ответсвенный начальник
		));
		$this->dispatch('employee/index');
		//var_dump($this->getResponse()->getHeaders());
		var_dump($this->getResponse()->getBody());
		$this->assertRedirectRegex('^/employee/modifyservice/client_id/\d+/point_id/\d+$');
	}
	
	public function testClientCreationAndTarifsManipulations() 
	{
		//$this->CreateClient();
	}
}
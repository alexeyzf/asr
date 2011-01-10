<?php

class SupportClientControllerTest extends ControllerTestCase
{    
    public function testSearch() 
    {
    	// Login GET
    	$this->loginAndTestGet('support-client/search');

        // POST
        $this->request->setMethod('POST')->
        		setPost(array(
        			'param' => 'points.pcross',
        			'value' => '2255572'
        		));
        $this->dispatch('support-client/search');        
        $this->assertXpathContentContains(
        	"//table[contains(@class, 'list')]/tr[2]/td[3]", 
        	'Мозгунов Цалик Менделевич', 
        	'Результат посика вернул неверное имя клиента.');
    }
 
    public function testAts()
    {
    	// Login GET
    	$this->loginAndTestGet('support-information/ats');
        
    	// делаем предположение что атс будет больше 61
        $this->assertXpathCountMin(
        	"//table[contains(@class, 'list')]/tr",
        	61,
        	'Ошибочное количество АТС.');
    }
    
    public function testPhoneCheck()
    {
    	// Login GET
    	$this->loginAndTestGet('support-information/phone-check');
    	
        // POST
        // подключенный абонент
        $this->request->setMethod('POST')->
        		setPost(array(
        			'number' => '2255572',
        			'city' => '0',
        			'client_type' => '0'
        		));
        $this->dispatch('support-information/phone-check');
        $this->assertXpathContentContains(
        	"//center/h2", 
        	'Данный номер уже занят', 
        	'Неверный результат провеки номера.');
        // неверный номер
        $this->request->setMethod('POST')->
        		setPost(array(
        			'number' => '225557222',
        			'city' => '0',
        			'client_type' => '0'
        		));
        $this->dispatch('support-information/phone-check');
        $this->assertXpathContentContains(
        	"//center/h2", 
        	'Длина номера превышает 7 символов', 
        	'Неверный результат провеки номера.');
        // неподключенный абонент
        $this->request->setMethod('POST')->
        		setPost(array(
        			'number' => '2257654',
        			'city' => '0',
        			'client_type' => '0'
        		));
        $this->dispatch('support-information/phone-check');
        $this->assertXpathContentContains(
        	"//center/h2", 
        	'Подключение возможно', 
        	'Неверный результат провеки номера.');
    }
    
    public function testCrossProblem()
    {
    	// Login GET
    	$this->loginAndTestGet('support-information/cross-problem');
    }
    
    public function testRadius()
    {
    	// Login GET
    	$this->loginAndTestGet('support-client/check-radius');
    	
    	// POST
    	$this->request->setMethod('POST')->
        		setPost(array(
        			'login' => '2255572',
					'password' => '123456', 
					'servicetype' => '3000'        		
        		));
        $this->dispatch('support-client/check-radius');
        $this->assertQueryContentContains('label', "Результат проверки пароля - Неправильны");
    }
    
    public function testAccident()
    {
    	// Login GET
    	$this->loginAndTestGet('accident/index');
    }
}

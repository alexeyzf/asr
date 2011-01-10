<?php
 
class IndexControllerTest extends ControllerTestCase
{
	public function testIndexAction()
	{
		$this->_doLogin();
		$this->dispatch('/');
		$this->assertModule('default');
		$this->assertController('index');
		$this->assertAction('index');
		
		$this->assertQueryCount('#hello', 1);
		$this->assertQueryContentContains('#hello', "Добро пожаловать!");
	}
}
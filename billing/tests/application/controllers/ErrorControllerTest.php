<?php

class ErrorControllerTest extends ControllerTestCase
{
    public function testErrorURL()
    {
    	$this->_doLogin();
        $this->dispatch('foo');
        $this->assertModule('default');
        $this->assertController('error');
        $this->assertAction('error');
    }
}
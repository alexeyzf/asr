<?php
/*
 * Created on 12.08.2009
 * Модель рисует форму для заполнения на странице /Employee/index
 * @author
 * @dateupdate NONE
 */
require_once('Zend/Form.php');

class Form_Addclient extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');

		// Рисуем форму
		$client_name = new Zend_Form_Element_Text('client_name');
		$client_name->setRequired(true)
					->setLabel('Название организации (клиент):  ')
					->setAttrib('id', 'some_id');
		$this->addElement($client_name);

		$country_id = new Zend_Form_Element_Select('country_id');
		$country_id->setLabel('Город:')
				   ->setAttrib('id', 'some_id');
		$this->addElement($country_id);

		$address = new Zend_Form_Element_Text('address');
		$address->setRequired(true)
				->setLabel('Укажите полный адрес клиента:  ')
				->setAttrib('id', 'some_id');
		$this->addElement($address);

		$client_orient = new Zend_Form_Element_Text('client_orient');
		$client_orient->setLabel('Ориентир:')
					  ->setAttrib('id', 'some_id');
		$this->addElement($client_orient);

		$phone = new Zend_Form_Element_Text('phone');
		$phone->setRequired(true)
			  ->setLabel('Телефон для связи:')
			  ->setAttrib('id', 'some_id');
		$this->addElement($phone);

		$phone = new Zend_Form_Element_Text('pcross');
		$phone->setRequired(true)
			  ->setLabel('Телефон для кроса:')
			  ->setAttrib('id', 'some_id');
		$this->addElement($phone);

		$pcross_owner = new Zend_Form_Element_Text('pcross_owner');
		$pcross_owner->setRequired(true)
			  ->setLabel('Владелец телефонного номера:')
			  ->setAttrib('id', 'some_id');
		$this->addElement($pcross_owner);

		$test_days = new Zend_Form_Element_Text('test_days');
		$test_days->setValue('0')
		    ->setLabel('Тестовый период (в днях):');
		$this->addElement($test_days);

		$contact_name = new Zend_Form_Element_Text('contact_name');
		$contact_name->setLabel('Контактное лицо:  ')
					 ->setAttrib('id', 'some_id');
		$this->addElement($contact_name);

		// Руков.
		$sign_name = new Zend_Form_Element_Text('sign_name');
		$sign_name->setLabel('Руководитель:  ')
					 ->setAttrib('id', 'some_id');
		$this->addElement($sign_name);

		// Руков.
		$post_sign_name = new Zend_Form_Element_Text('post_sign_name');
		$post_sign_name->setAttrib('id', 'some_id');
		$this->addElement($post_sign_name);


		$fax = new Zend_Form_Element_Text('fax');
		$fax->setLabel('Факс:  ')
			->setAttrib('id', 'some_id');
		$this->addElement($fax);

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email адрес клиента:  ')
			  ->setAttrib('id', 'some_id');
		$this->addElement($email);

		$passp_n = new Zend_Form_Element_Text('passp_n');
		$passp_n->setLabel('Номер паспорта:  ')
				->setAttrib('id', 'some_id');
		$this->addElement($passp_n);

		$ruvd_id = new Zend_Form_Element_Select('ruvd_id');
		$ruvd_id->setLabel('Кем выдан:  ')
				->setAttrib('id', 'some_id');
		$this->addElement($ruvd_id);


		$legaladdress = new Zend_Form_Element_Text('legaladdress');
		$legaladdress->setLabel('Укажите фактический адрес клиента:  ')
					 ->setAttrib('id', 'some_id');
		$this->addElement($legaladdress);

		$inn = new Zend_Form_Element_Text('inn');
		$inn->setLabel('ИНН:  ')
			->setAttrib('id', 'some_id');
		$this->addElement($inn);

		$mfo = new Zend_Form_Element_Text('mfo');
		$mfo->setLabel('МФО:  ')
			->setAttrib('id', 'some_id');
		$this->addElement($mfo);

		$okonx = new Zend_Form_Element_Text('okonx');
		$okonx->setLabel('ОКОНХ:  ')
			  ->setAttrib('id', 'some_id');
		$this->addElement($okonx);

		$bank_id = new Zend_Form_Element_Select('bank_id');
		$bank_id->setLabel('Наименование Банка:  ')
				->setAttrib('id', 'some_id');
		$this->addElement($bank_id);

		$is_accounting = new Zend_Form_Element_Checkbox('is_accounting');
		$is_accounting->setLabel('Казначейство:  ')
				->setAttrib('id', 'some_id');
		$this->addElement($is_accounting);

		$boss_id = new Zend_Form_Element_Select('boss_id');
		$boss_id->setLabel('Подписывающее лицо (ShT):  ')
				->setAttrib('id', 'some_id');
		$this->addElement($boss_id);
	}

	public function populate($values)
    {
    	$asrTypes = $values['asrtypes'];

    	foreach ($asrTypes[1] as $key => $value)
    	{
    		$this->country_id->addMultiOption($key, $value['typename']);
    	}

    	foreach ($asrTypes[2] as $key => $value)
    	{
    		$this->ruvd_id->addMultiOption($key, $value['typename']);
    	}

    	foreach ($asrTypes[0] as $key => $value)
    	{
    		$this->bank_id->addMultiOption($key, $value['typename']);
    	}

    	foreach ($asrTypes[4] as $key => $value)
    	{
    		$this->boss_id->addMultiOption($key, $value['typename']);
    	}

        return parent::populate($values);
    }

}

?>

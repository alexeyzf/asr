<?php
/*
 * Created on 12.08.2009
 *
 */
require_once('Zend/Form.php');

class Form_AddDopPoint extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
		$this->setAction('/Editpoint/attachpoint');


		$pcross = new Zend_Form_Element_Text('pcross');
		$pcross->setRequired(true)
			   ->setLabel('Телефон для кросса: ')
			   ->setAttrib('style', 'margin-bottom:10px;');
			   $this->addElement($pcross);

		$checkbut = new Zend_Form_Element_Button('check', 'Проверить');
		$checkbut->setAttrib('onClick','checkNumber()');
		$checkbut->removeDecorator('DtDdWrapper');
		$this->addElement($checkbut);



		$pcross_owner = new Zend_Form_Element_Text('pcross_owner');
		$pcross_owner->setRequired(true)
			   ->setLabel('Владелец телефонного номера: ')
			   ->setAttrib('id', 'some_id')
			   ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($pcross_owner);

		// Контакт. лицо
		$contact_name = new Zend_Form_Element_Text('contact_name');
		$contact_name->setRequired(true)
		             ->setLabel('Конт. лицо:  ')
		             ->setAttrib('id', 'some_id')
		             ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($contact_name);

		//Рук.
		$sign_name = new Zend_Form_Element_Text('sign_name');
		$sign_name->setRequired(true)
		             ->setLabel('Руководитель:  ')
		             ->setAttrib('id', 'some_id')
		             ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($sign_name);

				//Рук.
		$post_sign_name = new Zend_Form_Element_Text('post_sign_name');
		$post_sign_name->setRequired(true)
		             ->setLabel('Должность:  ')
		             ->setAttrib('id', 'some_id')
		             ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($post_sign_name);

		// телефон для контактов
		$phone = new Zend_Form_Element_Text('phone');
		$phone->setRequired(true)
			  ->setLabel('Контактный телефон:  ')
			  ->setAttrib('id', 'some_id')
			  ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($phone);

		// адрес предоставления услуг
		$connect_address = new Zend_Form_Element_Text('connect_address');
		$connect_address->setRequired(true)
						->setLabel('Адрес предоставления услуги:  ')
						->setAttrib('id', 'some_id')
						->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($connect_address);

		// Город
		$country_id = new Zend_Form_Element_Select('country_id');
		$country_id->setLabel('Город:  ')
				   ->setAttrib('id', 'some_id')
				   ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($country_id);

		// Сабмит
		$submit = new Zend_Form_Element_Submit('сохранить');
		$submit->setAttrib('style','margin-top:10px; margin-bottom:5px;');
		$this->addElement($submit);

		// Cancel
		$back = new Zend_Form_Element_Button('назад');
		$back->setAttrib('style','margin-top:-10px; margin-bottom:5px;')
			   ->setAttrib('onClick', 'history.go(-1)');
		$this->addElement($back);

		// client_id
		$client_id = new Zend_Form_Element_Hidden('client_id');
		$this->addElement($client_id);

		// client_type_id
		$client_type_id = new Zend_Form_Element_Hidden('client_type_id');
		$this->addElement($client_type_id);

		$submit->removeDecorator('lable');
		$back->removeDecorator('lable');
    }


    public function populate($values)
    {
        $asrTypes = $values['asrtypes'];

    	foreach ($asrTypes[1] as $key => $value)
    	{
    		$this->country_id->addMultiOption($key, $value['typename']);
    	}
    	
        return parent::populate($values);
    }
}
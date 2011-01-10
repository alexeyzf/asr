<?php
/*
 * Created on 12.08.2009
 *
 */
 require_once('Zend/Form.php');

class Form_EditPoint extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
		$this->setAction('/Editpoint/save');

		// Хиден поле point_id
		$pnt = new Zend_Form_Element_Hidden('point_id');

		// Хиден поле client_id
		$client_id = new Zend_Form_Element_Hidden('client_id');

		// Хиден поле client_id
		$client_type_id = new Zend_Form_Element_Hidden('client_type_id');

		// Конт. тел. клиента
		$phone = new Zend_Form_Element_Text('phone');
		$phone->setRequired(true)
			  ->setLabel('Телефон для связи:  ')
			  ->setAttrib('id', 'some_id')
			  ->setAttrib('style', 'margin-bottom:10px;');

		// Тел. кроссировки
		$pcross = new Zend_Form_Element_Text('pcross');
		$pcross->setRequired(true)
			   ->setLabel('Телефон для кросса:  ')
               ->setAttrib('id', 'some_id')
               ->setAttrib('READONLY', 'READONLY')
			   ->setAttrib('style', 'margin-bottom:10px;');

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

		// Тел. кроссировки
		$pcross_owner = new Zend_Form_Element_Text('pcross_owner');
		$pcross_owner->setRequired(true)
			   ->setLabel('Владелец телефонного номера:')
			   ->setAttrib('id', 'some_id')
			   ->setAttrib('style', 'margin-bottom:10px;');

		// Адрес подключения (предоставления услуги)
		$connect_address = new Zend_Form_Element_Text('connect_address');
		$connect_address->setRequired(true)
					    ->setLabel('Адрес подключения:  ')
					    ->setAttrib('id', 'some_id')
					    ->setAttrib('style', 'margin-bottom:10px;');
		// Конт. лицо
		$contact_name = new Zend_Form_Element_Text('contact_name');
		$contact_name->setRequired(true)
					 ->setLabel('Контактное лицо:  ')
					 ->setAttrib('id', 'some_id')
					 ->setAttrib('style', 'margin-bottom:10px;');

		// Город
		$country_id = new Zend_Form_Element_Select('country_id');
		$country_id->setLabel('Город:  ')
				   ->setAttrib('id', 'some_id')
				   ->setAttrib('style', 'margin-bottom:10px;');

		$submit = new Zend_Form_Element_Submit('сохранить');
		$submit->setAttrib('style','margin-top:10px; margin-bottom:5px;');

		$cancel = new Zend_Form_Element_Button('cancel');
		$cancel->setAttrib('onClick','redirect('.$_SESSION['back_url'].')');

		$this
			 ->addElement($phone)
			 ->addElement($pcross)
			 ->addElement($pcross_owner)
			 ->addElement($connect_address)
			 ->addElement($contact_name)
			 ->addElement($country_id)
			 ->addElement($submit)
			 ->addElement($cancel)
			 ->addElement($pnt)
			 ->addElement($client_id)
			 ->addElement($client_type_id);
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

?>

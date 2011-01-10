<?php
/*
 * Created on 12.08.2009
 *
 */
 require_once ('Zend/Form.php');

 class Form_ClientInfoModify extends Zend_Form
 {
 	public function init()
 	{
 		$this->setMethod('post');
 		$this->setAction('savemodify');

		$client_name = new Zend_Form_Element_Text('client_name');
		$client_name
					->setRequired(true)->setLabel('Название организации (клиент):  ')
					->setAttrib('id', 'some_id')
					->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($client_name);



		$address = new Zend_Form_Element_Text('address');
		$address
				->setLabel('Укажите полный адрес клиента (физ. и почтовый):  ')
				->setAttrib('id', 'some_id')
				->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($address);

		$legaladdress = new Zend_Form_Element_Text('legaladdress');
		$legaladdress
				->setLabel('Юр. адрес:  ')
				->setAttrib('id', 'some_id')
				->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($legaladdress);

		$client_orient = new Zend_Form_Element_Text('client_orient');
		$client_orient
					  ->setLabel('Ориентир:  ')
					  ->setAttrib('id', 'some_id')
					  ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($client_orient);

		$phone = new Zend_Form_Element_Text('phone');
		$phone
			  ->setLabel('Телефон клиента:  ')
			  ->setAttrib('id', 'some_id')
			  ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($phone);

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email адрес:  ')
			  ->setAttrib('id', 'some_id')
			  ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($email);

		$fax = new Zend_Form_Element_Text('fax');
		$fax->setLabel('Факс:  ')
			->setAttrib('id', 'some_id')
			->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($fax);

		// Номер пасспорта
		$passp_n = new Zend_Form_Element_Text('passp_n');
		$passp_n->setLabel('Паспорт:')
				->setAttrib('id', 'some_id')
				->setAttrib('style', 'margin-bottom:20px;');
		$this->addElement($passp_n);

		// РУВД
		$ruvd_id = new Zend_Form_Element_Select('ruvd_id');
		$ruvd_id->setLabel('Кем выдан: ')
				->setAttrib('id', 'some_id')
				->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($ruvd_id);



		// Bank
		$bank_id = new Zend_Form_Element_Select('bank_id');
		$bank_id->setLabel('Банк:')
				->setAttrib('id', 'some_id')
				->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($bank_id);

		// iNN
		$inn = new Zend_Form_Element_Text('inn');
		$inn->setLabel('ИНН:')
			->setAttrib('id', 'some_id')
			->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($inn);

		// MFO
		$mfo = new Zend_Form_Element_Text('mfo');
		$mfo->setLabel('МФО:')
			->setAttrib('id', 'some_id')
			->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($mfo);

		// OKONX
		$okonx = new Zend_Form_Element_Text('okonx');
		$okonx
			  ->setLabel('ОКОНХ:')
			  ->setAttrib('id', 'some_id')
			  ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($okonx);

		$submit = new Zend_Form_Element_Submit('сохранить');
		$submit->setAttrib('style', 'margin-bottom:5px;');
		$this->addElement($submit);

		$cancel = new Zend_Form_Element_Button('назад');
		$cancel->setAttrib(' onClick', 'history.go(-1)');
		$this->addElement($cancel);

		// Point iD
		$point_id = new Zend_Form_Element_Hidden('point_id');
		$this->addElement($point_id);

		// redirect_to
		$redirect_to = new Zend_Form_Element_Hidden('redirect_to');
		$this->addElement($redirect_to);

		// Клиент ID
		$client_id = new Zend_Form_Element_Hidden('client_id ');
		$this->addElement($client_id );

		// Тип клиента
		$client_type_id = new Zend_Form_Element_Hidden('client_type_id ');
		$this->addElement($client_type_id );

 	}

 	public function populate($values)
    {
        $asrTypes = $values['asrtypes'];

    	foreach ($asrTypes[2] as $key => $value)
    	{
    		$this->ruvd_id->addMultiOption($key, $value['typename']);
    	}

    	foreach ($asrTypes[0] as $key => $value)
    	{
    		$this->bank_id->addMultiOption($key, $value['typename']);
    	}

		$clientType = $values['client_type_id'];

		if ($clientType == 0)
		{
			$this->removeElement('passp_n');
			$this->removeElement('ruvd_id');
		}
		else
		{
			$this->removeElement('schet');
			$this->removeElement('legaladdress');
			$this->removeElement('okonx');
			$this->removeElement('mfo');
			$this->removeElement('inn');
			$this->removeElement('bank_id');
		}

        return parent::populate($values);
    }
 }
?>

<?php
require_once('Zend/Form.php');

class Form_AddHistory extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

		$client_name = new Zend_Form_Element_Text('client_name');
		$client_name->setRequired(true)
			   ->setLabel('Клиент: ')
			   ->setAttrib('style', 'margin-bottom:10px;')
			    ->setAttrib('readonly', 'true');
			   $this->addElement($client_name);

		$phone = new Zend_Form_Element_Text('phone');
		$phone->setRequired(true)
			   ->setLabel('Телефон кроса: ')
			   ->setAttrib('style', 'margin-bottom:10px;')
			    ->setAttrib('readonly', 'true');
			   $this->addElement($phone);


		$client_login = new Zend_Form_Element_Text('client_login');
		$client_login->setRequired(true)
			   ->setLabel('Логин: ')
			   ->setAttrib('style', 'margin-bottom:10px;')
			    ->setAttrib('readonly', 'true');
			   $this->addElement($client_login);


		$data_add = new Zend_Form_Element_Text('data_add');
		$data_add->setRequired(true)
			   ->setLabel('За дату: ')
			   ->setAttrib('style', 'margin-bottom:10px;');
			   $this->addElement($data_add);

		$status = new Zend_Form_Element_Select('status');
		$status->setRequired(true)
			   ->setLabel('Статус: ')
			   ->setAttrib('style', 'margin-bottom:10px;');
			   $this->addElement($status);



		$action_add = new Zend_Form_Element_Textarea('action_add');
		$action_add->setRequired(true)
			   ->setLabel('Действие: ')
			   ->setAttrib('style', 'margin-bottom:10px;');
			   $this->addElement($action_add);

		$reason_add = new Zend_Form_Element_Textarea('reason_add');
		$reason_add->setRequired(true)
			   ->setLabel('Причина: ')
			   ->setAttrib('style', 'margin-bottom:10px;');
			   $this->addElement($reason_add);


		// Сабмит
		$submit = new Zend_Form_Element_Submit('сохранить');
		$submit->setAttrib('style','margin-top:10px; margin-bottom:5px;');
		$this->addElement($submit);


		// client_id
		$client_id = new Zend_Form_Element_Hidden('client_id');
		$this->addElement($client_id);


		// client_id
		$client_type = new Zend_Form_Element_Hidden('client_type');
		$this->addElement($client_type);

		// ats_id
		$ats_id = new Zend_Form_Element_Hidden('ats_id');
		$this->addElement($ats_id);

		//dslam_id
		$dslam_id = new Zend_Form_Element_Hidden('dslam_id');
		$this->addElement($dslam_id);

		//frame_number
		$frame_number = new Zend_Form_Element_Hidden('frame_number');
		$this->addElement($frame_number);

		//port_number
		$port_number = new Zend_Form_Element_Hidden('port_number');
		$this->addElement($port_number);



		//PAIR_number
		$pair_number = new Zend_Form_Element_Hidden('pair_number');
		$this->addElement($pair_number);



		// client_type_id
		$client_type = new Zend_Form_Element_Hidden('client_type');
		$this->addElement($client_type);

    }


    public function populate($values)
    {
        $now = date('Y-m-d H:m:s');
        $this->data_add->setValue($now);

        $asrTypes = EmployeeFormHelper::getASRType();

    	
    	foreach ($asrTypes[6] as $key => $value)
    	{
    		$this->status->addMultiOption($key, $value['typename']);
    	}
    	
        return parent::populate($values);
    }
}
?>

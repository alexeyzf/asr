<?php
require_once('Zend/Form.php');

class Form_turnForm extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');

		$pcross = new Zend_Form_Element_Text('pcross');
		$pcross->setRequired(true)
			  ->setLabel('Телефон кросировки:');
		$this->addElement($pcross);


		$ats_id = new Zend_Form_Element_Select('ats_id');
		$ats_id->setLabel('АТС:  ');
		$this->addElement($ats_id);

		$contact_name = new Zend_Form_Element_Text('contact_name');
		$contact_name->setRequired(true)
			  ->setLabel('Контактное лицо:');
		$this->addElement($contact_name);

		$contact_phone = new Zend_Form_Element_Text('contact_phone');
		$contact_phone->setRequired(true)
			  ->setLabel('Контактный телефон:');
		$this->addElement($contact_phone);

	}

	public function populate($value)
    {
    	foreach ($value['ats_list'] as $key => $value)
    	{
    		$this->ats_id->addMultiOption($key, $value);
    	}

       //return parent::populate($value);
    }

}

?>

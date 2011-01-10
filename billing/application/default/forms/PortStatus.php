<?php
 require_once('Zend/Form.php');

class Form_PortStatus extends Zend_Form
{
    public function init()
    {
		$this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

		$status_type = new Zend_Form_Element_Select('status_type');
		$status_type->setLabel('Статус:')
			->addDecorator('Separator'); 
		$portsModel = new Ports();
		foreach ($portsModel->statuses as $statusCode => $statusLabel)
		{
			if ($statusCode <= 0)
			{
				$status_type->addMultiOption($statusCode, $statusLabel);
			}
		}
		$this->addElement($status_type);

		// Хиден submit
        $submit = new Zend_Form_Element_Submit('сохранить');
        $submit->setValue('save')
        	   ->addDecorator('Separator');
        $this->addElement($submit);

        // Хиден port_id
        $port_id = new Zend_Form_Element_Hidden('port_id');
        $this->addElement($port_id);


    }

    public function populate($values)
    {

        return parent::populate($values);
    }
}
<?php
require_once ('Zend/Form.php');
require_once ('forms/elements/Label.php');

class TransportForm extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        $numberElement = new Zend_Form_Element_Text('port_number');
        $numberElement->setLabel('Номер порта:')
        	->addValidator(new Zend_Validate_Int())
        	->setAttrib('disabled', 'disabled')
        	->addDecorator('Separator');
        $this->addElement($numberElement);

        $portType = new Zend_Form_Element_Text('port_type');
        $portType->setLabel('Тип порта:')
        	->addValidator(new Zend_Validate_Int())
        	->setAttrib('disabled', 'disabled')
        	->addDecorator('Separator');
        $this->addElement($portType);

        $looks = new Zend_Form_Element_Text('looks');
        $looks->setLabel('Направлен [АТС / IP switch]:')
        	->addValidator(new Zend_Validate_Int())
        	->setAttrib('disabled', 'disabled')
        	->addDecorator('Separator');
        $this->addElement($looks);


        $submitElement = new Zend_Form_Element_Submit('save', 'Удалить');
        $submitElement->addDecorator('Separator');
        $this->addElement($submitElement);

        $IDElement = new Zend_Form_Element_Hidden('switchbind_id');
        $IDElement->addDecorator('Separator');
        $this->addElement($IDElement);

	}

	public function populate($values)
	{
		if($values['is_transport'])
		{
			$this->port_type->setValue('Транспортный');

			$looks = $values['name']. " / ". $values['ip_address'];
			$this->looks->setValue($looks);
			$this->switchbind_id->setValue($values['switchbind_id']);
		}

		return parent::populate($values);
	}
}
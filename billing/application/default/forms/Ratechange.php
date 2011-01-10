<?php
require_once('Zend/Form.php');

require_once('forms/elements/DateSelect.php');

class Form_Ratechange extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

		$rate = new Zend_Form_Element_Text('rate');
		$rate->setLabel('Установить курс:')
            ->addDecorator('Separator');
        $this->addElement($rate);

        $startDateElement = new Form_Element_DateSelect('startdate');
        $startDateElement->setLabel('начиная с:')
            ->addDecorator('Separator');
        $this->addElement($startDateElement);

        $savebtn = new Zend_Form_Element_Submit('сохранить');
        $this->addElement($savebtn);

    }

    public function populate($data)
    {
        return parent::populate($data);
    }
}
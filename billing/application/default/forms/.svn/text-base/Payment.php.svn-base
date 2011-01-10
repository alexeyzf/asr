<?php
require_once('Zend/Form.php');

require_once('forms/elements/DateSelect.php');

class Form_Payment extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

		$services = new Zend_Form_Element_Select('servicetype');
		$services->setLabel('Услуга: ')
            ->addDecorator('Separator');
        $this->addElement($services);

        $payment_type = new Zend_Form_Element_Select('payment_type');
		$payment_type->setLabel('Тип платежа: ')
            		 ->addDecorator('Separator');
        $this->addElement($payment_type);

		$commente = new Zend_Form_Element_Text('commente');
		$commente->setLabel('Счет*: ')
            ->addDecorator('Separator');
        $this->addElement($commente);

		$summas = new Zend_Form_Element_Text('summas');
		$summas->setLabel('Сумма*: ')
            ->addDecorator('Separator');
        $this->addElement($summas);

        $currenttime = new Form_Element_DateSelect('currenttime');
        $currenttime->setLabel('от:')
            ->addDecorator('Separator');
        $this->addElement($currenttime);

        $savebtn = new Zend_Form_Element_Submit('Зачислить');
        $this->addElement($savebtn);

		$client_id = new Zend_Form_Element_Hidden('client_id');
        $this->addElement($client_id);

        $point_id = new Zend_Form_Element_Hidden('point_id');
        $this->addElement($point_id);

        $userid = new Zend_Form_Element_Hidden('userid');
        $this->addElement($userid);


    }

    public function populate($data)
    {
    	$data['userid'] = 3;

		$type_pay = array(
			0 => 'Нал.',
			1 => 'Без нал. (ПК)'
		);

		foreach($type_pay as $key => $value)
		{
			$this->payment_type->addMultiOption($key, $value);
		}

		for($i = 0; $i <count($data['servicetype']); $i++)
        {
        	for($k = 0; $k < count($data['servicetype']); $k++)
            $this->servicetype->addMultiOption($data['servicetype'][$i][$k]['servicetype_id'], $data['servicetype'][$i][$k]['short_name']);
        }

        return parent::populate($data);
    }
}
?>
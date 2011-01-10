<?php

require_once ('Zend/Form.php');

class Form_EngineerCall extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

		$typeElement = new Zend_Form_Element_Select('call');
		$typeElement->setLabel('Тип специалиста')
				->addMultiOption('5',  'Менеджер (10$)')
				->addMultiOption('10', 'Инженер (10$)')
				->addDecorator('Separator');
		$this->addElement($typeElement);

		$pointElement = new Zend_Form_Element_Select('point_id');
		$pointElement->setLabel('Точка')
				->addDecorator('Separator');
		$this->addElement($pointElement);

		$submit = new Zend_Form_Element_Submit('сохранить');
        $submit->setValue('save');
        $this->addElement($submit);

		$clientElement = new Zend_Form_Element_Hidden('client_id');
		$this->addElement($clientElement);
	}

	public function populate($data)
	{
		if ($data['client_id'])
		{
			$pointModel = new EditPointModel();
			$points = $pointModel->getClientPoints($data['client_id']);

			foreach ($points as $point)
			{
				$this->point_id->addMultiOption($point['point_id'], $point['u_login']);
			}
		}

		return parent::populate($data);
	}

	public function isValid($data)
	{
		if ($data['client_id'])
		{
			$pointModel = new EditPointModel();
			$points = $pointModel->getClientPoints($data['client_id']);

			foreach ($points as $point)
			{
				$this->point_id->addMultiOption($point['point_id'], $point['u_login']);
			}
		}



		return parent::isValid($data);
	}
}
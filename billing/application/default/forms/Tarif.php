<?php
require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');


class Form_Tarif extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');
    	$this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

		$startDateElement = new Form_Element_DateSelect('startdate');
		$startDateElement->setLabel('От')
			->addDecorator('Separator');
		$this->addElement($startDateElement);

		$endDateElement = new Form_Element_DateSelect('enddate');
		$endDateElement->setLabel('До')
			->addDecorator('Separator');
		$this->addElement($endDateElement);

		$serviceTypeElement = new Zend_Form_Element_Select('servicetype_id');
		$serviceTypeElement->setLabel('Тип услуги:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($serviceTypeElement);
		
		$tarifNameElement = new Zend_Form_Element_Text('tarif_name');
		$tarifNameElement->setLabel('Наименование тарифного плана:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($tarifNameElement);

		$tarifPriceElement = new Zend_Form_Element_Text('tarif_price');
		$tarifPriceElement->setLabel('Абонентская плата:')
			->addDecorator('Separator');
		$this->addElement($tarifPriceElement);

		$limitElement = new Zend_Form_Element_Text('limit');
		$limitElement->setLabel('Лимит:')
			->addDecorator('Separator');
		$this->addElement($limitElement);

		$unlimitElement = new Zend_Form_Element_Checkbox('unlimit');
		$unlimitElement->setLabel('Безлимитный:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($unlimitElement);

		$speedElement = new Zend_Form_Element_Text('speed');
		$speedElement->setLabel('Скорость:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($speedElement);

                $mindown_speed = new Zend_Form_Element_Text('mindown_speed');
		$mindown_speed->setLabel('Ниж-мин:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($mindown_speed);

                $maxdown_speed = new Zend_Form_Element_Text('maxdown_speed');
		$maxdown_speed->setLabel('Ниж-макс:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($maxdown_speed);


                $minup_speed = new Zend_Form_Element_Text('minup_speed');
		$minup_speed->setLabel('Вверх-мин:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($minup_speed);

                $maxup_speed = new Zend_Form_Element_Text('maxup_speed');
		$maxup_speed->setLabel('Вверх-макс:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($maxup_speed);

		$isfaceElement = new Zend_Form_Element_Select('isface');
		$isfaceElement->setLabel('Тип лица:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($isfaceElement);

		// $group_name for new tarif
		$groupNameElement = new Zend_Form_Element_Select('group_name');
		$groupNameElement->setLabel('Группа:')
			->setAttrib('id', 'some_id')
			->addDecorator('Separator');
		$this->addElement($groupNameElement);


                $tarif_id = new Zend_Form_Element_Hidden('tarif_id');
		$tarif_id->addDecorator('Separator');
		$this->addElement($tarif_id);
		
		$submitElement = new Zend_Form_Element_Submit('save', 'Сохранить');
		$submitElement->addDecorator('Separator');
		$this->addElement($submitElement);
    }

	public function populate($values)
    {
	array_push($values['client_type'], array('typename' => 'для всех', 'typename_id' => 2));

        $clientTypes = $values['client_type'];
		$services    = $values['servicetype_id'];

    	foreach ($clientTypes as $key => $value)
    	{
    		$this->isface->addMultiOption($value['typename_id'], $value['typename']);
    	}

    	foreach ($services as $key => $value)
    	{
    		$this->servicetype_id->addMultiOption($value['servicetype_id'], $value['servicetype_name']);
    	}

    	$asrHelpModel = new AsrHelp();
    	$groupTarifs = $asrHelpModel->getAsrTypeOptions(AsrHelp::TARIF_GROUP_TYPE);

    	foreach ($groupTarifs as $key => $value)
    	{
    		$this->group_name->addMultiOption(trim($value), $value);
    	}

        return parent::populate($values);
    }
}
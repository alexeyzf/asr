<?php
/**
 * Edit form for switch port
 *
 * @author marat
 */

require_once ('Zend/Form.php');
require_once ('forms/elements/Label.php');

class SwitchPortForm extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        $numberElement = new Zend_Form_Element_Text('port_number');
        $numberElement->setLabel('Номер порта:')
        	->addValidator(new Zend_Validate_Int())
        	->addDecorator('Separator');
        $this->addElement($numberElement);

        $isBrokenElement = new Zend_Form_Element_Checkbox('is_broken');
        $isBrokenElement->setLabel('Битый:')
        	->addDecorator('Separator')
        	->setAttrib('onchange', 'brokenChanged(this.checked)');
        $this->addElement($isBrokenElement);

        $equipmentTypeElement = new Zend_Form_Element_Select('equipment_type');
        $equipmentTypeElement->setLabel('Тип оборудования:')
        	->addDecorator('Separator')
        	->addMultiOption('dslam', 'Dslam')
        	->addMultiOption('modem', 'Modem')
        	->addMultiOption('transport', 'Transport')
        	->setAttrib('onchange', 'equipmentChanged(this.value)');
        $this->addElement($equipmentTypeElement);

        $atsElement = new Zend_Form_Element_Select('ats_id');
        $atsElement->setLabel('АТС:')
        	->addDecorator('Separator')
        	->setAttrib('onchange', 'getDslamList()')
        	->setAttrib('style', 'width: 200px')
        	->addMultiOption('', '');
        $atsModel = new AtsList();
        $atsElement->addMultiOptions($atsModel->getOptions());
        $this->addElement($atsElement);

        $dslamElement = new Zend_Form_Element_Select('dslam_id');
        $dslamElement->setLabel('Dslam:')
        	->addDecorator('Separator')
        	->setAttrib('style', 'width: 200px')
        	->addMultiOption('', '');
		$dslamModel =  new DslamList();
		$dslamElement->addMultiOptions($dslamModel->getOptionsWithIps(null));
        $this->addElement($dslamElement);

        $SwitchElement = new Zend_Form_Element_Select('switch_id');
        $SwitchElement->setLabel('Switch:')
        	->addDecorator('Separator')
        	->setAttrib('style', 'width: 200px')
        	->setAttrib('disabled', 'disabled')
        	->addMultiOption('', '');
        $this->addElement($SwitchElement);

        $clientNameElement = new Form_Element_Label('client_name');
        $clientNameElement->setLabel('Клиент:')
        	->addDecorator('Link')
        	->addDecorator('Separator')
        	->setAttrib('link_onclick', "openIframe('/searchclient/iframe/table/')")
        	->setAttrib('link_label', "Выбрать")
        	->setAttrib('link_href', "#")
        	->setAttrib('link_id', 'client_name_link');
        $this->addElement($clientNameElement);

        $pcrossElement = new Form_Element_Label('pcross');
        $pcrossElement->setLabel('Телефон кроссировки:')
        	->addDecorator('Separator');
        $this->addElement($pcrossElement);

        $submitElement = new Zend_Form_Element_Submit('save', 'Сохранить');
        $submitElement->addDecorator('Separator');
        $this->addElement($submitElement);

        $IDElement = new Zend_Form_Element_Hidden('id');
        $IDElement->addDecorator('Separator');
        $this->addElement($IDElement);

        $pointIDElement = new Zend_Form_Element_Hidden('point_id');
        $pointIDElement->addDecorator('Separator')
        	->addFilter(new Zend_Filter_Int());
        $this->addElement($pointIDElement);
	}

	private function prePopulate($values)
	{
		if ($values['ats_id'])
		{
			$dslamModel =  new DslamList();
	        $dslamList = $dslamModel->getOptionsWithIps("ats_id = {$values['ats_id']}");
	        $this->dslam_id->clearMultiOptions();

	        foreach ($dslamList as $dslamID => $dslamName)
	        {
	        	$this->dslam_id->addMultiOption($dslamID, $dslamName);
	        }
		}

		if ($values['is_broken'])
		{
			$this->dslam_id->setAttrib('disabled', 1);
			$this->ats_id->setAttrib('disabled', 1);
			$this->client_name->setAttrib('link_disabled', 1);
		}

		if ($values['dslam_id'])
		{
			$this->equipment_type->setValue('dslam');
			$this->client_name->setAttrib('link_disabled', 1);
		}
		elseif ($values['point_id'])
		{
			$this->equipment_type->setValue('modem');
			$this->dslam_id->setAttrib('disabled', 1);
		}
	}

	public function populate($values)
	{
		$this->prePopulate($values);
		return parent::populate($values);
	}

	public function isValid($values)
	{
		$this->prePopulate($values);
		return parent::isValid($values);
	}
}
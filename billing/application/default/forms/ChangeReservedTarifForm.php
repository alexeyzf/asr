<?php
 require_once('Zend/Form.php');
 require_once('forms/Startdate.php');
 require_once('forms/elements/DateSelect.php');
 require_once('TarifHelper.php');

class Form_ChangeReservedTarifForm extends Zend_Form
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


        //
        $tarif_id = new Zend_Form_Element_Select('tarif_id');
        $tarif_id->setLabel('Тариф: ')
        		->addDecorator('Separator');
        $this->addElement($tarif_id);


        $submit = new Zend_Form_Element_Submit('save');
        $submit->setValue('save');
        $this->addElement($submit);

		// Хиден point_id
        $pnt = new Zend_Form_Element_Hidden('point_id');
                $pnt->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($pnt);

		// Хиден service_id
        $pnt1 = new Zend_Form_Element_Hidden('service_id');
                $pnt1->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($pnt1);

		// Хиден unikey
        $unikey = new Zend_Form_Element_Hidden('unikey');
                $unikey->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($unikey);

    }

    public function populate($data)
    {
    	$tarifs = TarifHelper::getListTarifs();
    	foreach ($tarifs as $key => $value)
        {
            $this->tarif_id->addMultiOption($value['tarif_id'], $value['tarif_name']);
        }



		// For IF statement
		$this->unikey->setValue(rand(0, 100));

		if($data['flag_activ'] == 1)
		{
			$this->save->setAttrib('disabled', 'disabled');
			$this->tarif_id->setAttrib('disabled', 'disabled');
		}

    	return parent::populate($data);
    }
}
<?php
/*
 * Created on 15.08.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 require_once('Zend/Form.php');
 require_once('forms/Startdate.php');
 require_once('forms/elements/DateSelect.php');

class Form_Delivery extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        $startDateElement = new Form_Element_DateSelect('startdate');
        $startDateElement->setLabel('Начиная с:')
            ->addDecorator('Separator');
        $this->addElement($startDateElement);

        $endDateElement = new Form_Element_DateSelect('enddate');
        $endDateElement->setLabel('До: ')
            ->addDecorator('Separator');
        $this->addElement($endDateElement);

		// Рег. плата ($true - то penable = true)
        $penable_status = new Zend_Form_Element_Checkbox('penable');
        $penable_status->setLabel('Вступило в силу:')
                       ->addDecorator('Separator');
        $this->addElement($penable_status);

		// Submit
		$submit = new Zend_Form_Element_Submit('установить');
        $submit->setValue('save');
        $this->addElement($submit);

        // Тариф
        $tarif_id = new Zend_Form_Element_Hidden('tarif_id');
        $this->addElement($tarif_id);

        // Клиент ИД
        $client_id = new Zend_Form_Element_Hidden('client_id');
        $this->addElement($client_id);

        // Поитн ИД
        $point_id = new Zend_Form_Element_Hidden('point_id');
        $this->addElement($point_id);

    }

    public function populate($dataOld)
    {
        return parent::populate($dataOld);
    }
}

?>

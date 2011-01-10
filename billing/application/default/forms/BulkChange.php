<?php
require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');


class Form_BulkChange extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');
    	$this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        $current_tarif_id = new Zend_Form_Element_Select('current_tarif_id');
        $current_tarif_id->setLabel('Старый тариф: ')
                         ->addDecorator('Separator');
        $this->addElement($current_tarif_id);

        $new_tarif_id = new Zend_Form_Element_Select('new_tarif_id');
        $new_tarif_id->setLabel('Новый тариф: ')
                         ->addDecorator('Separator');
        $this->addElement($new_tarif_id);

        $submitElement = new Zend_Form_Element_Submit('save', 'Сохранить');
		$submitElement->addDecorator('Separator');
		$this->addElement($submitElement);
    }

    public function populate(array $values)
    {
        foreach ($values as $row)
        {
            $this->current_tarif_id->addMultiOption($row['tarif_id'], $row['tarif_name']);
            $this->new_tarif_id->addMultiOption($row['tarif_id'], $row['tarif_name']);
        }

        parent::populate($values);
    }
}
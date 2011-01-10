<?php
 require_once('Zend/Form.php');
 require_once('forms/Startdate.php');
 require_once('forms/elements/DateSelect.php');
 require_once('forms/elements/Modem.php');

class Form_AdditionalWriteOffs extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        // Summa
        $amount = new Zend_Form_Element_Text('amount');
        $amount->setLabel('Сумма списания:')
               ->addDecorator('Separator');
        $this->addElement($amount);

        // Summa
        $commente = new Zend_Form_Element_Textarea('commente');
        $commente->setLabel('Комментарии:')
               ->addDecorator('Separator');
        $this->addElement($commente);


        $submit = new Zend_Form_Element_Submit('сохранить');
        $submit->setValue('save');
        $this->addElement($submit);

        // Хиден point_id
        $pnt = new Zend_Form_Element_Hidden('point_id');
                $pnt->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($pnt);

        //
        $sid = new Zend_Form_Element_Hidden('servicetype_id');
                $sid->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($sid);

        //
        $client_id = new Zend_Form_Element_Hidden('client_id');
                $client_id->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($client_id);

    }
}
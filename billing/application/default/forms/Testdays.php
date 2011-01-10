<?php
require_once('Zend/Form.php');

class Form_Testdays extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');


        // Скидка
        $commente_test = new Zend_Form_Element_Textarea('commente_test');
        $commente_test->setLabel('Комментарии к тестам: ')
					  ->addDecorator('Separator');
        $this->addElement($commente_test);



        $submit = new Zend_Form_Element_Submit('create');
        $submit->setValue('save');
        $this->addElement($submit);

                // Хиден point_id
        $pnt = new Zend_Form_Element_Hidden('point_id');
                $pnt->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($pnt);

        $client_id = new Zend_Form_Element_Hidden('client_id');
		$client_id->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($client_id);

    }

    public function populate($dataOld)
    {


        return parent::populate($dataOld);
    }
}
<?php
require_once('Zend/Form.php');

class Form_Recalculation extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

		$type = new Zend_Form_Element_Select('typesearch');
		$type->setLabel('Поиск клиента по:')
            ->addDecorator('Separator');
        $this->addElement($type);

        $word = new Zend_Form_Element_Text('word');
        $word->addDecorator('Separator');
        $this->addElement($word);

        $savebtn = new Zend_Form_Element_Submit('найти');
        $this->addElement($savebtn);

    }

    public function populate($data)
    {
    	$data['type'] = array(
    		'CLA.client_name' => 'по наименованию клиента',
    		'CLA.client_id'   => 'по ID клиента'
    	);

    	foreach ($data['type'] as $key => $value)
        {
            $this->typesearch->addMultiOption($key, $value);
        }

        return parent::populate($data);
    }
}
<?php
/*
 * Created on 12.08.2009
 *
 */
require_once('Zend/Form.php');

class Form_SearchClient extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
		$this->setAction('/Searchclient/index');

        $type_search = new Zend_Form_Element_Select('type_search');
        $type_search->setLabel('Поиск по:  ');

        $searchword = new Zend_Form_Element_Text('searchword');
        $searchword->setAttrib('size','20');

        $submit = new Zend_Form_Element_Submit('submit');

        $this->addElement($type_search)
             ->addElement($searchword)
             ->addElement($submit);

        $type_search->removeDecorator('label');
        $searchword->removeDecorator('label');
    }


    public function populate($values)
    {
        $types = $values['type_search_options'];

    	foreach ($types as $key => $value)
    	{
    		$this->type_search->addMultiOption($key, $value);
    	}
    	
        return parent::populate($values);
    }
}
?>

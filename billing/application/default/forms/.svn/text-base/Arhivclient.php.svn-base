<?php
class Form_Arhivclient extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $type_search = new Zend_Form_Element_Select('type_search');
        $type_search->setLabel('Поиск по:');

        $searchword = new Zend_Form_Element_Text('searchword');

        $submit = new Zend_Form_Element_Submit('поиск');
        $submit->setAttrib('style','float:left; margin-left:10px;');

        $this->addElement($type_search)
             ->addElement($searchword)
             ->addElement($submit);

        $type_search->removeDecorator('label');
        $searchword->removeDecorator('label');

        $type_search->removeDecorator('DtDdWrapper');
        $searchword->removeDecorator('DtDdWrapper');
        $submit->removeDecorator('DtDdWrapper');

    }


    public function populate($values)
    {
    	foreach ($values as $key => $value)
    	{
    		$this->type_search->addMultiOption($key, $value);
    	}
        return parent::populate($values);
    }
}
?>

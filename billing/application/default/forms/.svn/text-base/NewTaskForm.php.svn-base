<?php
/*
 * Created on 2010-11-17
 * Частичная формочка для добавления заданий в документооброт
 * @author tsalik
 * @dateupdate NONE
 */
require_once('Zend/Form.php');
require_once('forms/elements/DateSelect.php');

class Form_NewTaskForm extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
 		$this->setAction('#');
        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));  

		/*$type = new Zend_Form_Element_Select('type');
		$type->setLabel('Тип задания:');
		$this->addElement($type);*/

        $priority = new Zend_Form_Element_Select('priority');
		$priority->setLabel('Преоритет:')->
            setDecorators(array(
                'ViewHelper',
                'Errors',
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'th', 'class' => 'label'),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            )));
		$this->addElement($priority);

		$title = new Zend_Form_Element_Text('title');
        $title->setAttrib('style', 'width: 98.3%');
		$title->setRequired(true)->setLabel('Заголовок:')->
            setDecorators(array(
                'ViewHelper',
                'Errors',
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'th', 'class' => 'label'),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            )));
        $this->addElement($title);

        $startDateElement = new Form_Element_DateSelect('deadline');
        $startDateElement->setLabel('Крйний срок:')->
            addDecorator('Label', array('tag' => 'th', 'class' => 'label'), array(array('row' => 'HtmlTag'), array('tag' => 'tr'))) ->
            addDecorator('HtmlTag', array('tag' => 'td', 'id'  => $this->getName() . '-element'));
            

        $this->addElement($startDateElement);

        $body = new Zend_Form_Element_Textarea('body');
		$body->setRequired(true)->setLabel('Сообщение:')->
            setDecorators(array(
                'ViewHelper',
                'Errors',
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'th', 'class' => 'label'),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            )));
		$this->addElement($body);

        /*$assignee = new Zend_Form_Element_Select('assignee');
		$assignee->setLabel('На кого:');
		$this->addElement($assignee);

        $document = new Zend_Form_Element_Select('document');
		$document->setLabel('Документ:');
		$this->addElement($document);*/
	}

	public function populate($values)
    {
        $this->priority->addMultiOption('', 'Выберите');
        foreach ($values['priorities'] as $row)
    	{
    		$this->priority->addMultiOption($row['id'], $row['description']);
        }

        /*foreach ($values['types'] as $row)
    	{
    		$this->type->addMultiOption($row['id'], $row['description']);
        }

        foreach ($values['docs'] as $row)
    	{
    		$this->document->addMultiOption($row['id'], $row['title']);
        }

    	foreach ($values['users'] as $row)
    	{
    		$this->assignee->addMultiOption($row['user_id'], $row['full_name']);
        }*/

        return parent::populate($values);
    }
}

?>

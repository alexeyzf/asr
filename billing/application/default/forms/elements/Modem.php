<?php
/**
 * Form element for modem
 *
 * @author marat
 */

require_once('Zend/Form/Element.php');

class Form_Element_Modem extends Zend_Form_Element
{
    public function loadDefaultDecorators()
    {
        $this->addPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        $this->addDecorator('Modem')
            ->addDecorator('Errors')
            ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
            ->addDecorator('HtmlTag', array('tag' => 'dd',
                                            'id'  => $this->getName() . '-element'))
            ->addDecorator('Label', array('tag' => 'dt'));
    }
}
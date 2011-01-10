<?php
require_once('Zend/Form/Element.php');

class Form_Element_TradtelNumbers extends Zend_Form_Element
{
    public function loadDefaultDecorators()
    {
        $this->addPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');
        $this->addDecorator('TradtelNumbers')
            ->addDecorator('Errors')
            ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
            ->addDecorator('HtmlTag', array('tag' => 'dd',
                                            'id'  => $this->getName() . '-element'))
            ->addDecorator('Label', array('tag' => 'dt'));
    }

    public function isValid($value, $context = null)
    {
        $value = array(
            'numbers'     => $value
        );

        return parent::isValid($value, $context);
    }
}
?>
<?php
/**
 * Form element for select date from select boxes
 *
 * @author marat
 */

require_once('Zend/Form/Element.php');

class Form_Element_DateSelect extends Zend_Form_Element
{
	private $_showDay = true;
	
	protected function getShowDay() 
	{
		return $this->_showDay;	
	}
	
	protected function setShowDay($value) 
	{
		$this->_showDay = $value;
	}
	
    public function loadDefaultDecorators()
    {
        $this->addPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');        
        $this->addDecorator('DateSelect', array('showDay' => $this->getShowDay()))
            ->addDecorator('Errors')
            ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
            ->addDecorator('HtmlTag', array('tag' => 'dd',
                                            'id'  => $this->getName() . '-element'))
            ->addDecorator('Label', array('tag' => 'dt'));
    }

    public function isValid($value, $context = null)
    {
    	if ($this->getShowDay()) 
    	{
        	$value = $context["{$this->_name}_year"] . '-'. $context["{$this->_name}_month"] . '-' . $context["{$this->_name}_day"];
    	}
    	else
    	{
    		$value = $context["{$this->_name}_year"] . '-'. $context["{$this->_name}_month"];
    	}
        return parent::isValid($value, $context);
    }
}

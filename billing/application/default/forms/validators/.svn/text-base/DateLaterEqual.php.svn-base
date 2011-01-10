<?php
/**
 * Validator для проверки того что одна дата больше или равна другой.
 * http://cogo.wordpress.com/2008/04/16/custom-validators-for-zend_form_element/
 *
 * @author tsalik
 * @version 0.1
 */

require_once 'Zend/Validate/Abstract.php';

class Validate_DateLaterEqual extends Zend_Validate_Abstract {
    const NOT_MATCH = 'otherFieldIsBigger';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Другое поле больше'
    );
	

    protected $_fieldToCheck = NULL;

    /**
     * Конструктор
     *
     * С каким элементом нужно сравнить. 
     *
     * @param string $fieldToCheck
     */
    public function __construct($fieldToCheck) {
    	$this->_fieldToCheck = (string) $fieldToCheck;
    }

    /**
     * Проверяет использующий элемент на валидность.
     *
     * @param $value string знчание нашего элемента
     * @param $context array ВСЕ остальные элементы формы, при чем элементы из html формы
     * @return boolean Возвращает true если элемент валидный
     */
    public function isValid($value, $context = null) {
        $value = (string) $value;
        $this->_setValue($value);

        
    	if (isset($context["{$this->_fieldToCheck}_day"])) 
    	{
        	$otherValue = $context["{$this->_fieldToCheck}_year"] . '-'. $context["{$this->_fieldToCheck}_month"] . '-' . $context["{$this->_fieldToCheck}_day"];
    	}
    	else
    	{
    		$otherValue = $context["{$this->_fieldToCheck}_year"] . '-'. $context["{$this->_fieldToCheck}_month"];
    	}
        
        if ($value < (string)$context[$this->_fieldToCheck]) 
        {
        	$this->_error(self::NOT_MATCH);
        	return false;
        }

        return true;
    }
}

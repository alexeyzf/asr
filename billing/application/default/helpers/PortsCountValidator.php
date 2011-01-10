<?php
/**
 * Description of PortsCountValidator
 *
 * @author marat
 */

require_once('Zend/Validate/Abstract.php');

class PortsCountValidator extends Zend_Validate_Abstract
{
    protected $_currentPortNumber;

    public function __construct($currentPortNumber)
    {
        $this->_currentPortNumber = $currentPortNumber;
    }

    const PORTS_COUNT_IS_LESS = 'portsCountIsLess';

    protected $_messageTemplates = array(
        self::PORTS_COUNT_IS_LESS => 'Количество портов не может быть меньше текущего'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if ($value < $this->_currentPortNumber)
        {
            $this->_error(self::PORTS_COUNT_IS_LESS);
            return false;
        }
        
        return true;
    }
}

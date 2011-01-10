<?php
/**
 * Validator for password confirmation
 *
 * @author marat
 */

require_once('Zend/Validate/Abstract.php');

class PasswordConfirmationValidator extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Подтверждение пароля не совпадает с паролем'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context))
        {
            if (isset($context['password_confirm'])
                && ($value == $context['password_confirm']))
            {
                return true;
            }
        } 
        elseif (is_string($context) && ($value == $context))
        {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}
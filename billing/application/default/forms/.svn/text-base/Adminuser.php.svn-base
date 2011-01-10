<?php
/**
 * Admin user modify page form
 *
 * @author marat
 */

require_once('Zend/Form.php');

class Form_Adminuser extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $firstNameElement = new Zend_Form_Element_Text('first_name');
        $firstNameElement
            ->setRequired()
            ->setLabel('Имя:')
            ->addValidator(new Zend_Validate_Alpha())
            ->addValidator(new Zend_Validate_StringLength(3, 50));

        $lastNameElement = new Zend_Form_Element_Text('last_name');
        $lastNameElement
            ->setRequired()
            ->setLabel('Фамилия:')
            ->addValidator(new Zend_Validate_Alpha())
            ->addValidator(new Zend_Validate_StringLength(3, 50));

        $appointmentElement = new Zend_Form_Element_Text('appointment');
        $appointmentElement
            ->setRequired()
            ->setLabel('Должность:')
            ->addValidator(new Zend_Validate_Alnum(true))
            ->addValidator(new Zend_Validate_StringLength(3, 50));

        $loginElement = new Zend_Form_Element_Text('login');
        $loginElement
            ->setRequired()
            ->addValidator(new Zend_Validate_Alnum())
            ->addValidator(new Zend_Validate_StringLength(3, 50))
            ->setLabel('Логин:');

        $passwordElement = new Zend_Form_Element_Password('password');
        $passwordElement
            ->setRequired()
            ->addValidator(new PasswordConfirmationValidator())
            ->addValidator(new Zend_Validate_StringLength(3))
            ->setLabel('Пароль:');

        $retypePasswordElement = new Zend_Form_Element_Password('password_confirm');
        $retypePasswordElement
            ->setRequired()
            ->setLabel('Повторите пароль:');

        $homePageElement = new Zend_Form_Element_Select('home_page');
        $homePageElement->setLabel('Домашняя страничка:');

        $this->addElement($firstNameElement)
            ->addElement($lastNameElement)
            ->addElement($appointmentElement)
            ->addElement($loginElement)
            ->addElement($passwordElement)
            ->addElement($retypePasswordElement)
            ->addElement($homePageElement);
    }

    /**
     * Populate form
     *
     * @param  array $values
     * @return Zend_Form
     */
    public function populate(array $values)
    {
        foreach ($values['home_page_options'] as $key => $value)
        {
            $this->home_page->addMultiOption($key, $value);
        }

        unset($values['home_page_options']);

        return parent::populate($values);
    }
}
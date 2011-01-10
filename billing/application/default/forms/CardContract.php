<?php
/**
 * Form for create or edit card contract
 *
 * @author marat
 */

require_once('Zend/Form.php');

class Form_CardContract extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        $nameElement = new Zend_Form_Element_Text('client_name');
        $nameElement->setLabel('Название организации:')
            ->setRequired()
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($nameElement);

        $isBrokerElement = new Zend_Form_Element_Checkbox('is_broker');
        $isBrokerElement->setLabel('Комиссионер:')
            ->addDecorator('Separator');
        $this->addElement($isBrokerElement);

        $factAddressElement = new Zend_Form_Element_Text('address');
        $factAddressElement->setLabel('Фактический адрес:')
            ->setRequired()
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($factAddressElement);

        $zipElement = new Zend_Form_Element_Text('zip_code');
        $zipElement->setLabel('Почтовый индекс:')
            ->setRequired()
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($zipElement);

        $legalAddressElement = new Zend_Form_Element_Text('legaladdress');
        $legalAddressElement->setLabel('Юридический адрес:')
            ->setRequired()
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($legalAddressElement);


        $sign_name = new Zend_Form_Element_Text('sign_name');
        $sign_name->setLabel('Имя рук:')
            ->setRequired(false)
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($sign_name);

        $post_sign_name = new Zend_Form_Element_Text('post_sign_name');
        $post_sign_name ->setLabel('Должн.:')
            ->setRequired(false)
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($post_sign_name);

        $emailElement = new Zend_Form_Element_Text('email');
        $emailElement->setLabel('E-mail:')
            ->setAttrib('style', 'width: 270px')
            ->addValidator(new Zend_Validate_EmailAddress())
            ->addDecorator('Separator');
        $this->addElement($emailElement);

        $bankElement = new Zend_Form_Element_Select('bank_id');
        $bankElement->setLabel('В банке:')
            ->setRequired()
            ->setAttrib('style', 'width: 279px')
            ->addDecorator('Separator');
        $this->addElement($bankElement);

        $innElement = new Zend_Form_Element_Text('inn');
        $innElement->setLabel('ИНН:')
            ->setRequired()
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($innElement);

        $mfoElement = new Zend_Form_Element_Text('mfo');
        $mfoElement->setLabel('МФО:')
            ->setRequired()
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($mfoElement);

        $okonxElement = new Zend_Form_Element_Text('okonx');
        $okonxElement->setLabel('ОКОНХ:')
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($okonxElement);

        $phoneElement = new Zend_Form_Element_Text('phone');
        $phoneElement->setLabel('Телефон для связи:')
            ->setRequired()
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($phoneElement);

        $faxElement = new Zend_Form_Element_Text('fax');
        $faxElement->setLabel('Факс:')
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($faxElement);

        $contactNameElement = new Zend_Form_Element_Text('contact_name');
        $contactNameElement->setLabel('Контактное лицо:')
            ->setRequired()
            ->setAttrib('style', 'width: 270px')
            ->addDecorator('Separator');
        $this->addElement($contactNameElement);

        $bossElement = new Zend_Form_Element_Select('boss_id');
        $bossElement->setLabel('Подписывающее лицо (ShT):  ')
                ->setAttrib('style', 'width: 279px')
                ->addDecorator('Separator');
        $this->addElement($bossElement);

        $submitElement = new Zend_Form_Element_Submit('save', 'Сохранить');
        $submitElement->addDecorator('Separator');
        $this->addElement($submitElement);
    }

    public function populate($values)
    {
        $values['client_name'] = stripcslashes($values['client_name']);
        $this->bank_id->addMultiOptions($values['bank_options']);
        $this->boss_id->addMultiOptions($values['boss_options']);

        return parent::populate($values);
    }
}

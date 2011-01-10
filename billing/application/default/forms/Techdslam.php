<?php
/**
 * Tech dslam modify page form
 *
 * @author marat
 */

require_once('Zend/Form.php');
require_once('PortsCountValidator.php');

class Form_Techdslam extends Zend_Form
{
    public function init()
    {
        $this->setMethod('POST');

        $nameElement = new Zend_Form_Element_Text('name');
        $nameElement->setLabel('Наименование:')
            ->setRequired();

        $ipElement = new Zend_Form_Element_Text('ip_address');
        $ipElement->setLabel('IP Адрес:')
            ->setRequired()
            ->addValidator(new Zend_Validate_Ip());

        $groupElement = new Zend_Form_Element_Text('group');
        $groupElement->setLabel('Группа:')
            ->setRequired();

        $clientTypeElement = new Zend_Form_Element_Select('client_type_id');
        $clientTypeElement->setLabel('Тип клиентов:')
            ->setRequired();

        $dslamTypeElement = new Zend_Form_Element_Select('type_id');
        $dslamTypeElement->setLabel('Тип DSLAM:')
            ->setRequired();

        $portCountElement = new Zend_Form_Element_Text('ports_count');
        $portCountElement->setLabel('Количество портов')
            ->setRequired();

        $submitElement = new Zend_Form_Element_Submit('save', 'Сохранить');

        $atsElement = new Zend_Form_Element_Hidden('ats_id');

        $this->addElement($nameElement)
             ->addElement($ipElement)
             ->addElement($groupElement)
             ->addElement($clientTypeElement)
             ->addElement($dslamTypeElement)
             ->addElement($portCountElement)
             ->addElement($submitElement)
             ->addElement($atsElement);
    }

    /**
     * Populate form
     *
     * @param  array $values
     * @return Zend_Form
     */
    public function populate($values)
    {
        $this->client_type_id->addMultiOptions($values['client_type_options']);
        $this->type_id->addMultiOptions($values['type_options']);
        
        if ($values['ports_count_min'])
        {
            $this->ports_count->addValidator(new PortsCountValidator($values['ports_count_min']));
        }

        parent::populate($values);
    }
}
?>

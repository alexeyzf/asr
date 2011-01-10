<?php
require_once('Zend/Form.php');

class Form_CorrectAccounting extends Zend_Form
{
    public function init()
    {

                $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

		$client_id = new Zend_Form_Element_Text('client_id');
		$client_id->setRequired(true)
			   ->setLabel('ID клиента: ')
			   ->setAttrib('style', 'margin-bottom:10px;');
			   $this->addElement($client_id);
                //Запись в акте сверки
                           
                $trantype = new Zend_Form_Element_Select('trantype');
                $trantype->setLabel('Тип операции: ')
                 ->addDecorator('Separator');
                $trantype->addMultiOption('7117', 'Снятие');
                $trantype->addMultiOption('31', 'Зачисление');
                $this->addElement($trantype);
                           
                           
		$rollback_sum = new Zend_Form_Element_Text('rollback_sum');
		$rollback_sum->setRequired(true)
			   ->setLabel('Сумма исправительной операций (Сум): ')
			   ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($rollback_sum);


		$rollback_dollar = new Zend_Form_Element_Text('rollback_dollar');
		$rollback_dollar->setRequired(true)
			   ->setLabel('Сумма исправительной операций (USD): ')
			   ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($rollback_dollar);

		$commente_rollback = new Zend_Form_Element_Textarea('commente_rollback');
		$commente_rollback->setRequired(true)
			   ->setLabel('Запись в акте сверки: ')
			   ->setAttrib('style', 'margin-bottom:10px;');
		$this->addElement($commente_rollback);


		// Сабмит
		$submit = new Zend_Form_Element_Submit('провести');
		$submit->setAttrib('style','margin-top:10px; margin-bottom:5px;');
		$this->addElement($submit);


		$submit->removeDecorator('lable');

    }


    public function populate($values)
    {
        $asrTypes = $values['asrtypes'];

    	foreach ($asrTypes[1] as $key => $value)
    	{
    		$this->country_id->addMultiOption($key, $value['typename']);
    	}
        return parent::populate($values);
    }
}
?>

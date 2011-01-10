<?php
require_once('Zend/Form.php');

class Form_Overdraft extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAction('#');

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        // выбор дня
        $overdraft = new Zend_Form_Element_Select('overdraft');
        $this->addElement($overdraft);

        // выбор ovedraft_type
        $ovedraft_type = new Zend_Form_Element_Select('overdraft_type');
        $this->addElement($ovedraft_type);

        $submit = new Zend_Form_Element_Submit('save', 'Установить');
        $this->addElement($submit);

		// Client_id
        $client_id = new Zend_Form_Element_Hidden('client_id');
        $this->addElement($client_id);

        $overdraft->removeDecorator('label');
        $overdraft->removeDecorator('DtDdWrapper');

        $client_id->removeDecorator('label');
        $client_id->removeDecorator('DtDdWrapper');

        $ovedraft_type->removeDecorator('label');
        $ovedraft_type->removeDecorator('DtDdWrapper');

        $submit->removeDecorator('label');
        $submit->removeDecorator('DtDdWrapper');
    }

    public function populate($data)
    {

		//var_dump($data);
		//exit();
    	for( $i = 1; $i <32; $i++)
    	{
			$arrdays[$i] = $i;
     	}

        foreach ($arrdays as $key => $value)
        {
            $this->overdraft->addMultiOption($key, $value);
        }

		$overtype[0] = 'Постоянный';
		$overtype[1] = 'Временный';
		foreach ($overtype as $key => $value)
        {
            $this->overdraft_type->addMultiOption($key, $value);
        }

        $this->client_id->setValue($data[0]['client_id']);
		$data['overdraft'] = $data[0]['overdraft'];
		$data['overdraft_type'] = $data[0]['overdraft_type'];

        return parent::populate($data);
    }
}
?>
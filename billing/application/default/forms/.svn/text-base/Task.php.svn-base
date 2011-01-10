<?php
/*
 * Created on 15.08.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 require_once('Zend/Form.php');

class Form_Task extends Zend_Form
{
    public function init()
    {

        $this->addElementPrefixPath('Form_Decorator', 'forms/decorators/', 'decorator');

        // $dslamip
        $dslamip = new Zend_Form_Element_Text('dslamip');
        $dslamip->setLabel('IP адрес DSLAM-а:')
                    ->addDecorator('Separator');
        $this->addElement($dslamip);

        // IP address
        $ip_address = new Zend_Form_Element_Text('ip_address');
       	$ip_address->setLabel('IP адрес клиента:')
                    ->addDecorator('Separator');
        $this->addElement($ip_address);

        // VLAN numer
        $vlan = new Zend_Form_Element_Text('vlan');
        $vlan->setLabel('VLAN клиента:')
                    ->addDecorator('Separator');
        $this->addElement($vlan);

        // Номер порта
        $portnumber = new Zend_Form_Element_Text('portnumber');
        $portnumber->setLabel('Номер порта:  ')
                    ->addDecorator('Separator');
        $this->addElement($portnumber);


        // Скорость
        $speed = new Zend_Form_Element_Text('speed');
        $speed->setLabel('Скорость:  ')
              ->addDecorator('Separator');
        $this->addElement($speed);

        // Доменное Имя
        $domain_address = new Zend_Form_Element_Text('domain_addres');
        $domain_address->setLabel('Доменное Имя:  ')
              ->addDecorator('Separator');
		$this->addElement($domain_address);

        $task_type = new Zend_Form_Element_Select('task_type');
        $task_type->setLabel('Тип операции:');
        $task_type->addMultiOption('0', 'Отпустить и затем поднять');
        $task_type->addMultiOption('2', 'Отпустить');
        $task_type->addDecorator('Separator');
        $this->addElement($task_type);

        $ctype = new Zend_Form_Element_Select('ctype');
        $ctype->setLabel('Тип клиента:');
        $ctype->addMultiOption('0', 'Юр. лицо');
        $ctype->addMultiOption('1', 'Физ. лицо');
        $ctype->addMultiOption('2', 'VPN');
        $ctype->addDecorator('Separator');
        $this->addElement($ctype);

        $submit = new Zend_Form_Element_Hidden('сохранить');
        $submit->setValue('save');
        $this->addElement($submit);

        // DSLAM ID
        $dslamid = new Zend_Form_Element_Hidden('dslamid');
                $dslamid->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($dslamid);


        // ID клиента
        $port_clientid = new Zend_Form_Element_Hidden('port_clientid');
                $port_clientid->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($port_clientid);


         // ID клиента
        $point_id = new Zend_Form_Element_Hidden('point_id');
                $point_id->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($point_id);

        // Хиден Тип клиента
        $ctype = new Zend_Form_Element_Hidden('ctype');
                $ctype->setDisableLoadDefaultDecorators(true)
                    ->addDecorator('ViewHelper');
        $this->addElement($ctype);

    }

    public function populate($data)
    {
		if($data['ctype'] == "1" or $data['group_name'] == 'corp_stream')
		{
			// у кого на атс 1мб т
			$this->ip_address->setValue('динамика');
			//$data['speed'] = "1024/512";
			$this->speed->setValue($data['speed']);
			$this->ip_address->setAttrib('readonly','readonly');
			$this->vlan->setAttrib('readonly','readonly');
		}
		else
		{
			// для корпов по тарифу
			$this->speed->setValue($data['speed']);
		}

		if($data['tablename'] == "VPN")
		{
			$this->ip_address->setValue($data['tablename']);
			$this->ip_address->setAttrib('readonly','readonly');
		}

		if($data['tablename'] == "hosting")
		{
			$this->removeElement('ip_address');
			$this->removeElement('dslamip');
			$this->removeElement('vlan');
			$this->removeElement('speed');
			$this->removeElement('portnumber');
		}
		else
		{
			$this->removeElement('domain_addres');
		}

        return parent::populate($data);
    }
}

?>

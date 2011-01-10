<?php

/*
 * Хелпер Template
 * Строит нужные шаблоны для заведения клиентов
 * Такие как заполнение полей для услуги ADSL, Collocation, Hosting, Dial-up, ISDN
 * система должна проверить какои тип лица имеет новый клиент и в зависимости
 * от этого перенаправить на нужный Template
 *
 */
class Zend_Controller_Action_Helper_GenerateData extends Zend_Controller_Action_Helper_Abstract
{
	public function direct()
	{

	}

    public static function genCross($city_id, $number, $client_type)
    {
        // Метод преобразует нужные данные в требуемый вид
        // shs + номер кросса, shsmr + номер и т.д.

        $gencrossModel = new ClientModel();
        $prefix 	   = $gencrossModel->selectCountryPrefix($city_id);
        if($client_type == 0)
        {
        	$prefix[0]['typename_desc'] = 'sll';
        }
        if($client_type == 2)
        {
			$prefix[0]['typename_desc'] = 'shsd';
        }
        if($client_type == 3)
        {
        	$prefix[0]['typename_desc'] = 'sml';
        }
        $new_number = $prefix[0]['typename_desc'] . $number;
        return trim($new_number);
    }

    public static function genPass($length)
    {
        // Метод создает пароль
        // принимает в качестве аргумента длину пароля
        $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++)
        {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }

	public static function showCountry($id_c)
	{
		$asrTypeModel = EmployeeFormHelper::getASRType();
		$data = $asrTypeModel[1];

		$country_id = new Zend_Form_Element_Select('country_id');
		$country_id->setValue($id_c)
				   ->setAttrib('id','some_id');
		foreach ($data as $country)
            {
                $country_id->addMultiOption($country['typename_id'], $country['typename']);
            }

		return $country_id;
	}
}
?>

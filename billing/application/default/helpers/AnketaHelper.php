<?php
class AnketaHelper
{

 public static function firstBlock($data, $rschet = "")
 {

 	foreach($rschet as $key => $value)
 	{
 		$scheta .= $value['schet']." ";
 	}

	$htmlcontent .= ('
   	 <table border="0">
	    <tr>
			<td>1. Наименование организации:</td>
			<td>'.$data[0]['client_name'].'</td>
	    </tr>
		<tr>
			<td>2. Юридический адрес:</td>
			<td>'.$data[0]['legaladdress'].'</td>
	    </tr>
		<tr>
			<td>3. Банковские реквизиты:</td>
			<td>'.$scheta.' в МФО '.$data[0]['mfo'].' ИНН '.$data[0]['inn'].' код '.$data[0]['okonx'].'</td>
	    </tr>
		<tr>
			<td>4. Телефон для связи:</td>
			<td>'.$data[0]['contact_phone'].'</td>
	    </tr>
		<tr>
			<td>5. Контактное лицо:</td>
			<td>'.$data[0]['service_contact_name'].'</td>
	    </tr>
		<tr>
			<td>6. Адрес точки включения:</td>
			<td>'.$data[0]['service_address'].'</td>
	    </tr>
		<tr>
			<td>7. Примечания:</td>
			<td>Скорость: '.$data[0]['speed'].' Телефон для кроссировки: '.$data[0]['pcross'].'</td>
	    </tr>
		<tr>
			<td>8. Дополнительные сервисы:</td>
			<td>---</td>
	    </tr>
		<tr>
			<td>9. Логин/Пароль для просмотра биллинга:</td>
			<td>'.$data[0]['u_login'].'/'.$data[0]['u_passwd'].'</td>
	    </tr>
		<tr>
			<td></td>
			<td></td>
	    </tr>
		<tr>
			<td>Исполнитель: </td>
			<td></td>
	    </tr>
		<tr>
			<td>Дата:</td>
			<td>Время: </td>
	    </tr>
		<tr>
			<td>
				Заключение технического директора:
				<br/>
				Подключение возможно
			</td>
			<td>
				____________________ М.П.
			</td>
	    </tr>
		<tr>
			<td>Дата:</td>
			<td>Время: </td>
	    </tr>
     </table>

	');
	return $htmlcontent;
	}

 public static function secondBlock($data)
 {
	$htmlcontent .= ('
	   	 <table border="0">
		    <tr>
				<td>1. Предполагаемый маршрут включения по АТС, расстояние: </td>
				<td>'.$data[0]['name'].' '.$data[0]['pcross'].'</td>
		    </tr>
			<tr>
				<td>2. Технология подключения: </td>
				<td>Выд. канал</td>
		    </tr>
			<tr>
				<td>3. Оборудование на стороне провайдера, порт: </td>
				<td>DSLAM: '.$data[0]['dslam_name'].'  порт:'.$data[0]['number'].'  рамка:'.$data[0]['frame_number'].'
					пары:'.$data[0]['line_number1'].'/'.$data[0]['line_number2'].'
				</td>
		    </tr>
		    <tr>
				<td>4. Оборудование на стороне клиента, порт: </td>
				<td>ADSL-модем, '.$data[0]['modem_serial'].'</td>
		    </tr>
		    <tr>
				<td>5. Скорость подключения (физическая): </td>
				<td>'.$data[0]['speed'].'</td>
		    </tr>
			<tr>
				<td>Исполнитель: </td>
				<td></td>
		    </tr>
			<tr>
				<td>Дата:</td>
				<td>Время: </td>
		    </tr>
		</table>
	');
	return $htmlcontent;
	}

 public static function thirdBlock($data)
 {
	$htmlcontent .= ('
	   	 <table border="0">
		    <tr>
				<td>1. Тип маршрутизации, протоколы: </td>
				<td></td>
		    </tr>
			<tr>
				<td>2. Предоставляемая пропускная способность:</td>
				<td>'.$data[0]['speed'].'</td>
		    </tr>
		    <tr>
				<td>3. Адрес клиента, шлюза и сервисов: </td>
				<td>
					IP:'.$data[0]['modem_serial'].'
					<br/>
					GW:'.$data[0]['modem_serial'].'
					<br/>
					DNS:'.$data[0]['modem_serial'].'
					<br/>
					Mask:'.$data[0]['modem_serial'].'
				</td>
		    </tr>
		    <tr>
				<td>4. Организация дополнительных сервисов: </td>
				<td>---</td>
		    </tr>
			<tr>
				<td>Исполнитель: </td>
				<td></td>
		    </tr>
			<tr>
				<td>Дата:</td>
				<td>Время: </td>
		    </tr>
		</table>
	');
	return $htmlcontent;
 }
}
?>
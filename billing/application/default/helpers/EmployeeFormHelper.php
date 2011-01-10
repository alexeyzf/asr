<?php
class EmployeeFormHelper
{
	//Далее идут вспомогательные методы
	public static function getASRType()
	{
            $asrModel = new AsrHelp();
            return $asrModel->getAsrTypes();
	}

	public static function getModifyTarif($selected_service)
	{
			/**
			 * Метод позволяет выбрать нужный тариф для выбр. ранее усл. и далее выдает
			 * соответствующие тарифы.
			 * Затем редирект на /employee/template
			 */
			// Данная модель нам нужна будет когда мы проверяем услуга
			// кроссированная или нет
			$verifyServiceModel = new ListServiceModel();
			$data = $verifyServiceModel->fetchRow('servicetype_id = ' . $selected_service['service_id']);

			// Модель которая вытаскивает все тарифы
			$tarifModel = new TarifListModel();
			$dataTarifs = $tarifModel->fetchAll('servicetype_id = ' . $selected_service['service_id']);

			// Создаем форму для выбора тарифа
			$form = new Zend_Form();
			$form->setAction('/employee/template')
				 ->setMethod('post');

			// тарифы
			$tarif_id = new Zend_Form_Element_Select('tarif_id');
			$tarif_id->setLabel('Тариф:  ')
					->setAttrib('id', 'some_id')
					->setAttrib('style', 'margin-bottom:15px; ');
			foreach ($dataTarifs as $tr)
			{
				$tarif_id->addMultiOption($tr->tarif_id, $tr->tarif_name);
			}

			// ID точки
			$lastPointid = new Zend_Form_Element_Hidden('point_id');
			$lastPointid->setValue($selected_service['point_id']);

			// NEED_CROSS
			$need_cross = new Zend_Form_Element_Hidden('need_cross');
			$need_cross->setValue($data['need_cross']);

			// ID сервиса
			$service_id = new Zend_Form_Element_Hidden('service_id');
			$service_id->setValue($selected_service['service_id']);

			// ID клиента
			$client_id = new Zend_Form_Element_Hidden('client_id');
			$client_id->setValue($selected_service['client_id']);

			// Back URL
			$back_url = new Zend_Form_Element_Hidden('back_url');
			$back_url->setValue($selected_service['back_url']);

			// Submit
			$submit = new Zend_Form_Element_Submit('выбрать');
			$submit->setValue('fix')
				   ->setAttrib('style', 'position:relative;');



			$form->addElement($tarif_id)
				 ->addElement($submit)
				 ->addElement($lastPointid)
				 ->addElement($client_id)
				 ->addElement($back_url)
				 ->addElement($need_cross)
				 ->addElement($service_id);

			// Рендерем форму во вьюшку
			return $form;
	}

	public static function getServiceList($post_point)
	{
			$listService = new ListServiceModel();
			$faceid = $listService->getFace($post_point['client_id']); // Узнаем тип клиента
			$data_list_service = $listService->fetchAll('isface in (2,' . $faceid . ')');

			// Строим форму для вывода списка услуг
			$form = new Zend_Form();
			$form->setAction('/employee/modifytarif')
				 ->setMethod('post');

			$service_id = new Zend_Form_Element_Select('servicetype_id');
			$service_id->setAttrib('id', 'some_id');
			foreach ($data_list_service as $s_list) {
				$service_id->addMultiOption($s_list->servicetype_id, $s_list->servicetype_name);
			}
			$form->addElement($service_id);

			// Хиден поле point_id
			$point_id = new Zend_Form_Element_Hidden('point_id');
			$point_id->setValue($post_point['point_id']);
			$form->addElement($point_id);

			// ID клиента
			$client_id = new Zend_Form_Element_Hidden('client_id');
			$client_id->setValue($post_point['client_id']);
			$form->addElement($client_id);

			// Back URL
			$back_url = new Zend_Form_Element_Hidden('back_url');
			$back_url->setValue($post_point['backurl']);
			$form->addElement($back_url);

			$submit = new Zend_Form_Element_Submit('дальше');
			$submit->setValue('далее');
			$form->addElement($submit);
			return $form;
	}

}

?>

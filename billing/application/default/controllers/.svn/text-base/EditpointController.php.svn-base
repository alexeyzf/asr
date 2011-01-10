<?php
/*
 *  Данный контроллер будет реализовывать возможность
 *  редактирования информации о точке
 */
require_once ('BaseController.php');
require_once ('EditPointModel.php');
require_once ('AsrHelp.php');
require_once ('EmployeeFormHelper.php');
require_once ('forms/EditPoint.php');
require_once ('GenCrossNumberModel.php');
require_once ('ClientModel.php');
require_once ('AbonDepartmentHistoryHelper.php');
require_once ('forms/AddDopPoint.php');

class EditpointController extends BaseController
{
    public function indexAction()
    {
        /**
        *  Принимаем значение из _GET и пытаемся посмотреть что делать
        *  дальше
        */

        $point_id = $this->_request->getParams('point_id');
        $editPointModel = new EditPointModel();
        $oldData = $editPointModel->selectOldInformationAboutPoint($point_id['point_id']);
        // Рисуем форму

        $values['asrtypes'] = EmployeeFormHelper::getASRType();
        if (is_array($oldData))
        {
            $values += $oldData;
        }
        $form = new Form_EditPoint();
        $form->populate($values);
        $this->view->formForEdit = $form;
    }

    public function saveAction()
    {

        /**
        *  Метод сохраняет изменения в БД
        */
        if ($this->_request->isPost())
        {
            // Получаем новые данные для точки
            $newdata = $this->_request->getPost();

            // формируем новый логин для точки
            $newlogin = $this->_helper->GenerateData->genCross($newdata['country_id'], $newdata['pcross'], $newdata['client_type_id'] );

            // Обновляем данные о точке
            $updatePointModel = new EditPointModel();

            $rowUpdate = $updatePointModel->saveNewData($newdata, $newlogin);

            // Логирование
            AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::EDIT_POINT_INFO, $_SERVER['REQUEST_URI'], $newdata['client_id'], $newdata['point_id']);

            // Делаем редерект
            $this->_redirect($_SESSION['back_url']);
        }
    }

    public function deletepointAction()
    {
    	/**
    	 *  Метод удаляет точку.
    	 * 	Если эта точка у клиента последная то и клиента за одно.
    	 *  Удаление происходит только в случае если на данной точке нет активных услуг
    	 */

        // Принимаем $_GET переменные
        $point_id = $this->_request->getParams();
        $deletePointModel = new EditPointModel();
        $row_forDelete = $deletePointModel->findAllPoints($point_id['point_id']);

        // Если у данной точки есть услуги, то сообщаем что нельзя удалять
        if(count($row_forDelete) > 0)
        {
            echo ('<center>Невозможно удалить точку, к ней все еще прикреплены
            услуги или же это единственная точка у данного клиента <a href="#" onClick="history.go(-1)" ">Назад</a>  </center>');
        }
        else
        {
            // Логирование
            AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::DELETE_POINT, $_SERVER['REQUEST_URI'] , $point_id['client_id']);
            $pointForDelete = $deletePointModel->deleteDataPoint($point_id['point_id']);
        }
    }

    public function attachpointAction()
    {
        /**
        *  Метод-экшен который уже непосредственно принимает значения формы в переменную
        *  $form проверяет его валидность, генерирует пароль для новой точки
        *  ($pswd = $this->genPass(N) и если все данные находятся в пригодном состоянии
        *  то вставляет данные в БД (points)
        *  @param integer $cid - ID клиента, приходит с формой Form_AddDopPoint
        *  @param integer $client_type_id - Тип клиента
        */

        $cid 			= $this->_request->getParam('client_id');
        $client_type_id = $this->_request->getParam('client_type_id');
        // Рисуем форму
        $values['asrtypes']       = EmployeeFormHelper::getASRType();
        $values['client_id']      = $cid;
        $values['client_type_id'] = $client_type_id;
        
        if ($client_type_id == 1) 
        {
        	$pointModel = new EditPointModel();
        	$pcross = $pointModel->getFirstPcross($cid);
        	$passwd = $this->_helper->GenerateData->genPass(8);
        	$newpointModel = new InsertClientPointModel();
        	$newPointID = $newpointModel->newPoint(array('phone' => $pcross), $cid, $pcross, $passwd);
        	
        	// Логируем
            AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::ADD_POINT, $_SERVER['REQUEST_URI'], $cid, $newPointID);

            // После записи новой точки, юзера кидает на выбор услуг для данной точке
            $this->_redirect('/employee/modifyservice/client_id/'.$cid.'/point_id/'.$newPointID.'');
        }

        $form = new Form_AddDopPoint();
        $form->populate($values);
        $this->view->form = $form;

        if ($this->_request->isPost())
        {
            $dop_data = $this->_request->getPost();

            if ($form->isValid($dop_data))
            {
                // и сам attach точки и если все ок , то редерект на show
                $pid = $this->saveAttachPoint($dop_data, $dop_data['client_id'], $client_type_id);

				// Логируем
                AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::ADD_POINT, $_SERVER['REQUEST_URI'], $cid, $pid);

                // После записи новой точки, юзера кидает на выбор услуг для данной точке
                $this->_redirect('/employee/modifyservice/client_id/'.$cid.'/point_id/'.$pid.'');
            }
        }
    }

    public function saveAttachPoint($f_data, $client_id, $client_type_id)
    {
        /**
        *  Метод добавляет новую точку для данного клиента
        *  В случае если новый клиент имеет принадлежность к частному лицу,
        *  то данный метод не используется, так как частное лицо может иметь
        *  только одну точку.
        *  @param array $f_data - Данные отправленные с формой
        *  @param integer $client_id - ID клиента
        *  @param integer $client_id - Тип клиента (1 - Стрим, 0 - Юр. лица, 4 - карт., остальные Dialup)
        */

        // Новый логин для точки, зависит от номера кросировки, и генерим пароль к ней
        $login  = $this->_helper->GenerateData->genCross($f_data['country_id'], $f_data['pcross'], $client_type_id);
        $passwd = $this->_helper->GenerateData->genPass(8);


        $newpointModel = new InsertClientPointModel();
        $row = $newpointModel->newPoint($f_data, $client_id, $login, $passwd);

        $connector = "";
        // Логирование
        AbonDepartmentHistoryHelper::addAbonLog(AbonDepartmentHistoryHelper::ADD_POINT, $_SERVER['REQUEST_URI'], $client_id);
        return $row;
    }
}
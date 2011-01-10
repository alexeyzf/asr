<?php
/*
 *  Хелпер для логирования всех действий которые будут
 *  происходить в Абонентском отделе
 */
require_once ('AbonHistoryModel.php');
require_once ('AdminUser.php');

class AbonDepartmentHistoryHelper
{
    const ADD_NEW_CLIENT = 'Добавление нового клиента';
    const SERVICE_WRITTEN_DOWN = 'Услуга записана';
    const POINT_ADDED_TO_PEREKROS = 'Точка оформлена на перекрос';
    const ATS_BONUS_ADDED = 'К точке применили Бонус на АТС';
    const FRAMED_PORT_TASK = 'Изменение состояния порта (Поднят / Отпущен)';
    const TEST_POINT = 'Точка оформлена как ТЕСТ';
    const BALLANCE_CARRYING = 'Перенос лицевого счета';
    const SERVICE_OFF = 'Выключение услуги';
    const WRITE_OFFS = 'Дополнительные снятия (ОП)';
    const OVERDRAFT_ADDED = 'Установка ОВЕРДРАФТ';
    const AS_EMPLOYEE = 'Точка оформлена как сотрудничество';
    const AS_DONATE = 'Точка оформлена как благотворительность';
    const TERMINATE_CONTRACT = 'Договор расторгнут';
    const EDIT_APPLICATION_ON_POINT = 'Редактирование услуги (Смена тарифа, вступило в силу)';
    const EDIT_CLIENT_INFO = 'При заведении нового клиента. Редактирование информации о клиенте';
    const PRINT_CONTRACT = 'При заведении нового клиента. Печать договора';
    const ADD_POINT = 'Добавление новой точки доступа';
    const EDIT_POINT_INFO = 'Редактирование информации о точке';
    const DELETE_POINT = 'Точка удалена';
    const ADD_SERVICE_ADSL = 'Добавлена новая услуга ADSL';
    const ADD_SERVICE_COLLA = 'Добавлена новая услуга Collacation';
    const ADD_SERVICE_NGN = 'Добавлена новая услуга NGN';
    const DELETE_SERVICE_ON_REG = 'Удаление сервиса';
    const END_REG = 'Завершение работы по добавлению нового клиента';
    const SEARCH_EDIT_BEGIN = 'Поиск клиента. Процедура изменения информации о клиенте ';
    const EDIT_POINT_AFTER_SEARCH = 'Поиск клиента. Изменение информации о точке';
    const POINT_ATTACH_PORT = 'Прикрепление порта к точке';
    const POINT_DETACH_PORT = 'Открепление порта от точки';
    const POINT_NAME_REWRITE = 'Смена логина';
    const IGNORE_CLIENT_WARNING = 'Игнорироване предупреждения об отрицательном балансе';
    const CANCEL_CLIENT_WARNING = 'Отмена заведения клиента из-за отрицательного баланса';
    const FIX_CLIENT_WARNING = 'Перенос баланса с архивного клиента на основного';
	const EDIT_PHONE_TARIF = 'Редактирование тарифа по телефонии';
	const REMOVE_MODEM = 'Возврат снятия за модем';
	const CONFIG_DSLAM = 'Настройка DSLAM';
	const EDIT_DSLAM   = 'Редактирование/Добавление DSLAM';
	const DELETE_DSLAM   = 'Удаление DSLAM';

    public static function addAbonLog($action, $url, $client_id, $pointID)
    {
        // ID юзера который делает что то
        $auth = Zend_Auth::getInstance();
        $user = $auth->getStorage()->read();

        // Логин пользователя
        $userModel   = new AdminUser();
        $techHistory = new TechHistory();

        $user_login = $userModel->getUserLogin($user->id);
        $login      = $techHistory->getPointLogin($pointID);

        // Модель для вставки данных в таблицу логирвания
        $insertAbonLogModel = new AbonHistoryModel();
        $row_log = $insertAbonLogModel->createRow();
        $row_log->action       = $action;
        $row_log->user_id      = $user->id;
        $row_log->action_url   = $url;
        $row_log->user_login   = $user_login;
        $row_log->client_id    = $client_id;
        $row_log->client_login = $login;
        $row_log->save();
    }
}
?>

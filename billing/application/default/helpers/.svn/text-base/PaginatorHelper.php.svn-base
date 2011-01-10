<?php
class PaginatorHelper
{
    /**
     * Собственно наш пагинатор
     */
    public static function getPaginator($data, $parentpage, $num = 25)
    {
		 $page = $parentpage; // Принимаем параметры
		 if($page < 1 or empty($page))
		 {
	 		$page = 1;
		 }
		 $start = $page * $num - $num; // нумерация страниц

		 // Создаем пагинатор объект и передаем ему данные из модели SearchAbonDepartment
		 $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($data));
		 $paginator->setCurrentPageNumber($page);
		 $paginator->setItemCountPerPage($num);
		 $paginator->setView();
		 Zend_View_Helper_PaginationControl::setDefaultViewPartial('/my_pagination.phtml');
		 return $paginator;
    }
}
?>

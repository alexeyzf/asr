<?php
require_once('TarifListModel.php');

class TarifHelper
{

    public static function getTarifPrice($tarifID)
    {
		$tarifListModel = new TarifListModel();

		$result = $tarifListModel->getTarifData($tarifID);
		return $result['tarif_price'];
    }

    public static function getListTarifs()
    {
		$tarifListModel = new TarifListModel();
		return $tarifListModel->getServiceTarifs(3000, 0);
    }
}
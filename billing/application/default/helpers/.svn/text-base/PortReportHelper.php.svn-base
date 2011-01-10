<?php

class PortReportHelper
{

    public static function getAtsIDHelper()
    {
        $portReportModel = new PortReportModel();

        return $portReportModel->RetAts();
    }

    public static function getHubAreaHelper()
    {
        $portReportModel = new PortReportModel();
        return $portReportModel->getHubIDs();
    }

    public static function getAtsByHubHelper($hub_id)
    {
        $portReportModel = new PortReportModel();
        return $portReportModel->getAtsByHub($hub_id);
    }

    public static function getAtsDslamsHelper($atsID)
    {
        $portReportModel = new PortReportModel();
        return $portReportModel->getDslamNamesByAtsID($atsID);
    }

    public static function arr1($dslamid)
    {
        $portReportModel = new PortReportModel();
        return $portReportModel->getStatus($dslamid);
    }

    public static function arr2($dslamid)
    {
        $portReportModel = new PortReportModel();
        return $portReportModel->getStateBroken($dslamid);
    }

}
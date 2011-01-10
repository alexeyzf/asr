<?php
require_once 'IpaddressModel.php';
class IpHelper
{
    public static function getValidIP($point_id)
    {
        $ipModel = new IpaddressModel();

        $ipData = $ipModel->currentIP($point_id);

        $connector = '';
        foreach ($ipData as $ip)
        {
            $validIP .= $connector . $ip['ip_address'];
            $connector = ' ';
        }

        return $validIP;
    }

    public function getCurrentValidIP($point_id, $ctype)
    {
        // От этого надо срочно придумать лекарство
            if($tablename == "vpn")
            {
                return "VPN";
            }
        //

        $ipModel = new IpaddressModel();

        $ipData = $ipModel->currentIP($point_id);

        $connector = '';
        foreach ($ipData as $ip)
        {
            $validIP .= $connector . $ip['ip_address'];
            $connector = ' ';
        }

        $data['ip_address'] = $validIP;
        $data['gw_address'] = $ipData[0]['gw_address'];
        $data['mask']       = $ipData[0]['mask'];
        $data['vlan']       = $ipData[0]['vlan'];

        return $data;
    }
}
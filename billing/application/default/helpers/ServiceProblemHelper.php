<?php

class ServiceProblemHelper
{
	const STREAM_SERVICE_TYPE = 3000;
	const SIGMA_DIALUP_SERVICE_TYPE = 1000;
	const SMILE_DIALUP_SERVICE_TYPE = 1100;

	public static function getInfoAboutServiceStream($data, $client_type_id)
	{
		$supportModel= new SupportModel();
		$result = $supportModel->getSupportInfoStream($data, $client_type_id);

        $test = explode(',', $result);
        $test[0] = substr($test[0], 1);
        $test[count($test) - 1] = substr($test[count($test) - 1], 0, -1);

        $test['u_login'] = $data['u_login'];
        $htmlcontent = self::buildViewStream($test);

        return $htmlcontent;
	}

        public static function buildViewStream($data)
        {
            $ballance    = "Отрицательный";
            $statuscross = "Не скросирован";
            $service     = "Выключена";

            if($data[0] > 0)
            {
                $ballance = "<td class='list_row'><b style=\"{color:green}\">Положительный </b></td>";
            }
            else
            {
                $ballance = "<td class='list_row'><b style=\"{color:red}\">Отрицательный </b></td>";
            }

            if($data[1] == 25)
            {
                $statuscross = "<td class='list_row'><b style=\"{color:green}\">Скросирован </b></td>";
            }
            else
            {
                $statuscross = "<td class='list_row'><b style=\"{color:red}\">Проблемы на кросе</b></td>";
            }

            if($data[2] == 'true')
            {
                $service = "<td class='list_row'><b style=\"{color:green}\">Включена</b></td>";
            }
            else
            {
                $service = "<td class='list_row'><b style=\"{color:red}\">Отключена</b></td>";
            }

            if($data[6] != "empty")
            {
                $session = "<img class=\"link\" title=\"Закрыть\" onclick=\"redirect('/support-client/close-session/id/$data[6]/l/$data[u_login]')\" src=\"/images/icons/close.png\"/>";
            }
            else
            {
                $session = "Нет";
            }


            $html = "
               <table class='list' cellpadding='3' cellspacing='0'>
                    <tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
                        <td  class='list_row'>Баланс:</td>
                        $ballance
                    </tr>
                    <tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
                        <td  class='list_row'>Крос:</td>
                        $statuscross
                    </tr>
                    <tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
                        <td  class='list_row'>Состояние услуги:</td>
                        $service
                    </tr>
                    <tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
                        <td  class='list_row'>Дата окончания услуги:</td>
                        <td  class='list_row'>$data[3]</td>
                    </tr>
                    <tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
                        <td  class='list_row'>Оплачено до:</td>
                        <td  class='list_row'>$data[4]</td>
                    </tr>
                    <tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
                        <td  class='list_row'>Трафик:</td>
                        <td  class='list_row'>$data[5]</td>
                    </tr>
                    <tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
                        <td  class='list_row'>Доверительный платеж:</td>
                        <td  class='list_row'><b style='color:red'>$data[7]</b></td>
                    </tr>
                    <tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
                        <td  class='list_row'>Открытая сессия:</td>
                        <td  class='list_row'>$session</td>
                    </tr>
                    <tr class='list_tr' onMouseOver=\"this.setAttribute('class', 'list_tr_hover')\" onMouseOut=\"this.setAttribute('class', 'list_tr')\">
                        <td  class='list_row'>АТС:</td>
                        <td  class='list_row'>$data[8]</td>
                    </tr>
                </table>
            ";

            return $html;
        }
}
<?php
/**
 * CrossLetterRtf
 *
 * Rtf helper for generate cross letter
 *
 * @author marat
 */

class CrossLetterRtfHelper extends RtfHelper
{
    public function __construct($companyName, $companyDirector, $clientName, $clientType)
    {
        parent::__construct($companyName, $companyDirector, $clientName, $clientType);
    }

    public function generate($letterNumber, $phoneHubInfo, $pointInfo, $isInsteadLostLetter = false)
    {
        $this->_content = $this->getTemplate('cross_letter');
        $this->replaceMainParams();
		
    	if (intval($letterNumber) < 10)
        {
        	$letterNumber = "000{$letterNumber}";
        }
        elseif (intval($letterNumber) < 100)
        {
        	$letterNumber = "00{$letterNumber}";
        }
        elseif (intval($letterNumber) < 1000)
        {
        	$letterNumber = "0{$letterNumber}";
        }
        
        $this->rtf_replace('number', $letterNumber);
        $this->rtf_replace('letter_date', date('d.m.Y'));
        $this->rtf_replace('phone_hub_case_name', $phoneHubInfo['case_name']);
        $this->rtf_replace('phone_hub_case_director', $phoneHubInfo['case_director']);
        $this->rtf_replace('phone_hub_director', $phoneHubInfo['director']);

        if ($pointInfo['pcross_type'] == 1)
        {
            $pcrossType = 'телефонный номер';
        }
        else
        {
            $pcrossType = 'прямой провод';
        }

        $this->rtf_replace('pcross_type', $pcrossType);
        $this->rtf_replace('pcrossowner', $pointInfo['pcross_owner']);
        $this->rtf_replace('pcross', $pointInfo['pcross']);
        $this->rtf_replace('ats_name', $pointInfo['ats_name']);
        $this->rtf_replace('frame_number', $pointInfo['frame_number']);
        $this->rtf_replace('line_number1', $pointInfo['line_number1']);
        $this->rtf_replace('line_number2', $pointInfo['line_number2']);


        if ($isInsteadLostLetter)
        {
            $this->rtf_replace('letter_type_desc', 'Дается взамен утерянного. Стоимость кроссировочных работ учтена ранее.');
        }
        else
        {
            $this->rtf_replace('letter_type_desc', 'Предоплата произведена.');
        }
        $this->sentResponce("CrossLetter{$letterNumber}");       
    }
}
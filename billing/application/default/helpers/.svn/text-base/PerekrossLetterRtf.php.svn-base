<?php
/**
 * Uncross
 */

class PerekrossLetterRtf extends RtfHelper
{
    public function __construct($companyName, $companyDirector, $clientName, $clientType)
    {
        parent::__construct($companyName, $companyDirector, $clientName, $clientType);
    }

    public function generate($letterNumber, $phoneHubInfo, $pointInfo, $isInsteadLostLetter = false, $oldDataPoint)
    {
        $this->_content = $this->getTemplate('perekros_letter');
        $this->replaceMainParams();

        $this->rtf_replace('number', $letterNumber);
        $this->rtf_replace('letter_date', date('d.m.Y'));
        $this->rtf_replace('phone_hub_case_name', $phoneHubInfo['case_name']);
        $this->rtf_replace('phone_hub_case_director', $phoneHubInfo['case_director']);
        $this->rtf_replace('phone_hub_director', $phoneHubInfo['director']);

        if ($pointInfo['pcross_type'] == 1)
        {
            $pcrossType = 'телефонного номера';
        }
        else
        {
            $pcrossType = 'прямого провода';
        }

        $this->rtf_replace('pcross_type', $pcrossType);
        $this->rtf_replace('pcross', $oldDataPoint['pcross']);
        $this->rtf_replace('ats_name', $pointInfo['ats_name']);
        $this->rtf_replace('frame_number_old', $oldDataPoint['frame_number']);
        $this->rtf_replace('line_number1_old', $oldDataPoint['line_number1']);
        $this->rtf_replace('line_number2_old', $oldDataPoint['line_number2']);

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

        $this->sentResponce("UncrossLetter{$letterNumber}");
    }
}

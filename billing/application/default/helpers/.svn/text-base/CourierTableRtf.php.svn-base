<?php

/**
 * Helper for generate rtf for courier table
 *
 * @author marat
 */

class CourierTableRtfHelper extends RtfHelper
{
    public function __construct($companyName)
    {
        parent::__construct($companyName, '', '', '');
    }

    private $_mass = array(
        1   => 'A',
        2   => 'B',
        3   => 'C',
        4   => 'D',
        5   => 'E',
        6   => 'F',
        7   => 'G',
        8   => 'H',
        9   => 'I',
        10  => 'J',
        11  => 'K',
        12  => 'L',
        13  => 'M',
        14  => 'N',
        15  => 'O',
        16  => 'P',
        17  => 'Q',
        18  => 'R',
        19  => 'S',
        20  => 'T',
        21  => 'U',
        22  => 'V',
        23  => 'W',
        24  => 'X',
        25  => 'Y',
        26  => 'Z',
        27  => 'a',
        28  => 'b',
        29  => 'c',
        30  => 'd'
    );

    public function generate($hubName, $date, $letters)
    {
        $this->_content = $this->getTemplate('courier_table');
        $this->replaceMainParams();

        $this->rtf_replace('hub', $hubName);
        $this->rtf_replace('date', date('d.m.Y', strtotime($date)));


        for ($i = 1; $i <= 30; $i++)
        {
            $number = '';
            $pcross = '';
            $type = '';

            if ( $i <= count($letters) )
            {
                $number = "КР-{$letters[$i - 1]['number']}";
                $pcross = $letters[$i - 1]['pcross'];

                if ($letters[$i-1]['kind'] == LettersToAts::LETTER_KIND_CROSS)
                {
                    $type = 'скросс';
                }
                else
                {
                    $type = 'раскросс';
                }
            }

            $char = $this->_mass[$i];

            $this->rtf_replace("number{$char}", $number);
            $this->rtf_replace("pcross{$char}", $pcross);
            $this->rtf_replace("pcross_type{$char}", $type);
        }

        $this->sentResponce("courier{$date}");
    }
}
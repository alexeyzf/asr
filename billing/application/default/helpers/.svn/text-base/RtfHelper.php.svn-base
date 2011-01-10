<?php
/**
 * Helper to generate rtf documents
 *
 * @author marat
 */

class RtfHelper
{
    private $_companyName;
    private $_companyDirector;
    private $_clientName;
    private $_clientType;
    protected $_content;

    public function __construct($companyName, $companyDirector, $clientName, $clientType)
    {
        $this->_companyName =  $companyName;
        $this->_companyDirector = $companyDirector;
        $this->_clientName = $clientName;
        $this->_clientType = $clientType;
    }

    protected function getTemplate($name)
    {
        return file_get_contents(realpath("../application/default/views/templates/{$name}.rtf"));
    }

    /**
    * Formats utf-8 encoded text. Internal use.
    * @param string $str Text
    */
    protected function utf8Unicode($str)
    {
        return $this->unicodeToEntitiesPreservingAscii($this->utf8ToUnicode($str));
    }

    private function utf8ToUnicode($str)
    {
        $unicode = array();
        $values = array();
        $lookingFor = 1;

        for ($i = 0; $i < strlen($str); $i++ )
        {
            $thisValue = ord($str[$i]);

            if ($thisValue < 128)
            {
                $unicode[] = $thisValue;
            }
            else
            {
                if ( count( $values ) == 0 )
                {
                    $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
                }

                $values[] = $thisValue;

                if ( count( $values ) == $lookingFor )
                {
                    $number = ( $lookingFor == 3 ) ?
                        ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                        ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );

                    $unicode[] = $number;
                    $values = array();
                    $lookingFor = 1;
                }
            }
        }

        return $unicode;
    }

    private function unicodeToEntities($unicode)
    {
        $entities = '';
        foreach( $unicode as $value )
        {
            $entities .= '\uc0\u'.$value.' ';
        }
        return $entities;
    }

    private function unicodeToEntitiesPreservingAscii($unicode)
    {
        $entities = '';
        foreach( $unicode as $value )
        {
            if ($value != 65279)
            {
                $entities .= ( $value > 127 ) ? '\uc0\u' . $value . ' ' : chr( $value );
            }
        }
        return $entities;
    }

    protected function rtf_replace($paramName, $value)
    {
        if (is_int($value))
        {
            $value = "{$value}";
        }

        $this->_content = str_replace("#{$paramName}", $this->utf8Unicode($value), $this->_content);
    }

    protected function replaceMainParams()
    {
        $this->rtf_replace('company_name', $this->_companyName);
        $this->rtf_replace('gen_name_main', $this->_companyDirector);
        $this->rtf_replace('client_name', $this->_clientName);

        if ($this->_clientType == 0)
        {
            $desc = "Наименование организации";
            $attachment = "письмо клиента";
        }
        else
        {
            $desc = "ФИО абонента";
            $attachment = "заявление и копия паспорта клиента";
        }

        $this->rtf_replace('client_type_desc', $desc);
        $this->rtf_replace('client_type_attachment', $attachment);
    }

    protected function sentResponce($fileName)
    {
        header('Content-Disposition: attachment; filename=' . $fileName . '.rtf');
        header('Content-type: application/msword');
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
        print $this->_content;
        exit;
    }
}
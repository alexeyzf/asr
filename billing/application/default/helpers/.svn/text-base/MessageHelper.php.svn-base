<?php
class MessageHelper
{
    public static function addError($messageCode, $messageText)
    {
        $_SESSION['error'][$messageCode] = $messageText;
    }

    public static function addInfo($messageCode, $messageText)
    {
        $_SESSION['info'][$messageCode] = $messageText;
    }

    public static function getAllErrors()
    {
        $result =  array();

        if ( ! is_array($_SESSION['error']) )
        {
            return $result;
        }

        foreach ($_SESSION['error'] as $messageCode => $messageText)
        {
            $result[$messageCode] = $messageText;
        }

        unset($_SESSION['error']);

        return $result;
    }

    public static function getAllInfos()
    {
        $result =  array();

        if ( ! is_array($_SESSION['info']) )
        {
            return $result;
        }

        foreach ($_SESSION['info'] as $messageCode => $messageText)
        {
            $result[$messageCode] = $messageText;
        }

        unset($_SESSION['info']);

        return $result;
    }
}
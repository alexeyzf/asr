<?php
/* 
Утилиты для работы с тарифным планом Nova.
 */

class NovaHelper
{
    const MIN_DOWN_SPEED = 50;
    const MAX_DOWN_SPEED = 600;
    const NAME = 'STREAM Nova';

    public static function getTarifsList()
    {
        for ($speed = self::MIN_DOWN_SPEED; $speed <= self::MAX_DOWN_SPEED; $speed++)
        {
            $result[] = self::NAME . '-' . $speed;
        }
        return $result;
    }

    public static function getNameForDownSpeed($speed)
    {
        return self::NAME . '-' . $speed;
    }
}

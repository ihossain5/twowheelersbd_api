<?php

namespace App\Classes;

class ColorUtility
{
    public static function hexToColorName($hexColor)
    {
        $colorMap = array_flip(config('app.colors'));

        if (isset($colorMap[$hexColor])) {
            return $colorMap[$hexColor];
        } else {
            return 'Unknown'; 
        }
    }
}
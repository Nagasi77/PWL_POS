<?php

namespace App\Helpers;

class Helper
{
    public static function ribuan($number)
    {
        return number_format($number, 0, ',', '.');
    }
}
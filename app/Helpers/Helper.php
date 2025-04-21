<?php

namespace App\Helpers;

class Helper
{
    // Existing methods

    public static function ribuan($number)
    {
        return number_format($number, 0, ',', '.');
    }
}
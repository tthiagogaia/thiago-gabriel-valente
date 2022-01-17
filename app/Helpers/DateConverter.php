<?php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class DateConverter
{
    public static function make(string $date): Carbon
    {
        return self::attemptParse($date) ?: Carbon::createFromFormat('d/m/Y', $date);
    }

    protected static function attemptParse(string $date): Carbon | bool
    {
        try {
            return Carbon::parse($date);
        } catch (InvalidFormatException $e) {
            return false;
        }
    }
}

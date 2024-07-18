<?php

namespace Gosas\Core\Helpers;

class PriceFormatter
{
    public static function FormatAmount($amount): int
    {
        return round($amount, 2) * 100;
    }
}

<?php

namespace Gosas\Core\Entity;

class AmountInfo
{
    public $amount;
    public $currencyCode;

    public function __construct($amount, $currencyCode)
    {
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
    }
}

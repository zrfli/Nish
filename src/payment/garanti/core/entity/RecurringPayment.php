<?php

/**
 * recurring object model
 */

namespace Gosas\Core\Entity;

class RecurringPayment
{
    public $amount;
    public $paymentNum;

    public function __construct($amount, $paymentNum)
    {
        $this->amount = $amount;
        $this->paymentNum = $paymentNum;
    }
}

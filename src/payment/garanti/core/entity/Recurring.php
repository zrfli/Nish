<?php

/**
 * recurring object model
 */

namespace Gosas\Core\Entity;

class Recurring
{
    public $type;
    public $totalPaymentNum;
    public $frequencyType;
    public $frequencyInterval;
    public $startDate;
    public $paymentList;

    public function __construct($type, $totalPaymentNum, $frequencyType, $frequencyInterval, $startDate)
    {
        $this->type = $type;
        $this->totalPaymentNum = $totalPaymentNum;
        $this->frequencyType = $frequencyType;
        $this->frequencyInterval = $frequencyInterval;
        $this->startDate = $startDate;
    }
}

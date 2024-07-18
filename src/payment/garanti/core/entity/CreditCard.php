<?php

namespace Gosas\Core\Entity;

class CreditCard
{
    public $cardNumber;
    public $cardHolderName;
    public $expireYear;
    public $expireMonth;
    public $cvv;

    public function __construct($cardNumber, $cardHolderName,  $expireYear, $expireMonth, $cvv)
    {
        $this->cardNumber = $cardNumber;
        $this->cardHolderName = $cardHolderName;
        $this->expireYear = $expireYear;
        $this->expireMonth = $expireMonth;
        $this->cvv = $cvv;
    }

    public function GetExpireInfo()
    {
        return  str_pad((int)$this->expireMonth, 2, 0, STR_PAD_LEFT) . $this->expireYear;
    }
}

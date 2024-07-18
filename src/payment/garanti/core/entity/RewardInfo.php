<?php

class RewardInfo
{
    public $type;
    public $usedAmount;
    public $gainedAmount;

    public function __construct($type, $usedAmount, $gainedAmount)
    {
        $this->type = $type;
        $this->usedAmount = $usedAmount;
        $this->gainedAmount = $gainedAmount;
    }
}

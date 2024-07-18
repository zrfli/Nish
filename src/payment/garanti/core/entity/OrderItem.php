<?php

namespace Gosas\Core\Entity;

class OrderItem
{
    public $number;
    public $productId;
    public $productCode;
    public $quantity;
    public $price;
    public $totalAmount;

    public function __construct($number, $productId, $productCode, $quantity, $price)
    {
        $this->number = $number;
        $this->productId = $productId;
        $this->productCode = $productCode;
        $this->quantity  = $quantity;
        $this->price = $price;
        $this->totalAmount = $this->GetTotalAmount($price, $quantity);
    }

    private function GetTotalAmount($amount, $quantity)
    {
        return $amount * $quantity;
    }
}

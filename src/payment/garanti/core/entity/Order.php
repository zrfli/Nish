<?php

namespace Gosas\Core\Entity;

class Order
{
    public $orderId;
    public $groupId;
    public Address $address;
    public OrderItem $item;
    public OrderComment $comment;
    public Recurring $recurring;

    public function __construct()
    {
        $this->orderId = $this->CreateOrderNumber();
    }

    /**
     * @return string new Guid for order number
     */
    private function createOrderNumber()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        $id = sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

        return $id;
    }
}

<?php

namespace Gosas\Core\Entity;

class Terminal
{
    public string $provUserId;
    public string $userId;
    public string $id;
    public string $merchantId;
    public string $hashData;


    public function __construct($provUserId, $userId, $id, $merchantId, $hashData)
    {
        $this->provUserId = $provUserId;
        $this->userId = $userId;
        $this->id = $id;
        $this->merchantId = $merchantId;
        $this->hashData = $hashData;
    }
}

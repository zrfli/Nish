<?php

namespace Gosas\Core\Entity;

class ThreeDPayment
{
    public int $currency;
    public string $type;
    public string $orderId;
    public string $errorUrl;
    public string $successUrl;
    public string $storeKey;
    public string $hashedPassword;
    public string $hashedData;
}

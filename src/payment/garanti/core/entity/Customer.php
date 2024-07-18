<?php

/**
 * customer object model
 */

namespace Gosas\Core\Entity;

class Customer
{
    public $emailAddress;
    public $ipAddress;

    public function __construct($emailAddress, $ipAddress)
    {
        $this->emailAddress = $emailAddress;
        $this->ipAddress = $ipAddress;
    }
}

<?php

namespace Gosas\Core\Entity;

class Address
{
    public $addressType;
    public $firstName;
    public $lastName;
    public $addressText;
    public $district;
    public $city;
    public $phoneNumber;

    public function __construct(
        $firstName,
        $lastName,
        $addressType,
        $addressText,
        $district,
        $city,
        $phoneNumber
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->addressType = $addressType;
        $this->addressText = $addressText;
        $this->district  = $district;
        $this->city  = $city;
        $this->phoneNumber = $phoneNumber;
    }
}

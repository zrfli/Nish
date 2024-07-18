<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/iyzico/iyzipay-php/IyzipayBootstrap.php';

IyzipayBootstrap::init();

class Config
{
    public static function options()
    {
        $options = new \Iyzipay\Options();
     
        $options->setApiKey("sandbox-P5LkN1PVTwPgyCpLX8NnEuM10Ho8VXEu");
        $options->setSecretKey("sandbox-ay2JiWPSUuPe725HRRNUezgOzJX1kgEk");
        $options->setBaseUrl('https://sandbox-api.iyzipay.com');

        return $options;
    }
}
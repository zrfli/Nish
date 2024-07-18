<?php

namespace Gosas\Core\Settings;

require_once('../../../core/enums/RequestMode.php');

use Gosas\Core\Enums\RequestMode;

class PosSettings
{
    public $requestUrl;
    public $requestMode;

    public $version = "512";
    public $provUserId = "PROVAUT";
    public $provUserId3DS = "GARANTI";
    public $provUserPassword = "123qweASD/";
    public $userId = "PROVAUT";
    public $terminalId = "30691297";
    public $merchantId = "7000679";

    public $emailAddress = "eticaret@garanti.com.tr";
    public $ipAddress = "192.168.0.1";

    public $storeKey = "12345678";
    public $threeDPaymentResultUrl = "http://localhost:8080/gap_php/threed-payment-result.php";

    public function __construct($mode)
    {
        $this->requestMode = $mode;
    }

    public function GetRequestUrl()
    {
        if ($this->requestMode === RequestMode::Test) {
            $this->requestUrl = "https://sanalposprovtest.garanti.com.tr/VPServlet";
        } else {
            $this->requestUrl = "https://sanalposprov.garanti.com.tr/VPServlet";
        }

        return $this->requestUrl;
    }
}

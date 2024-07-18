<?php
/**
 *
 * @package Misy
 * @author Misy
 * @link https://misy.dev
 * @copyright Copyright (c) 2024, Misy
 * @license http://opensource.org/licenses/MIT MIT License
*/

if ($_SERVER['REQUEST_URI'] == '/src/payment/config.php') { header("Location: /"); }

$validBanks = [];

class VposCredentials {
    private $CLIENT_ID;
    private $CLIENT_KEY;
    private $CLIENT_USERNAME;
    private $CLIENT_PASSWORD;
    private $STORE_KEY;
    private $API_URL;

    public function __construct($clientId, $clientKey, $clientUsername, $clientPassword, $storeKey, $apiUrl) {
        $this -> CLIENT_ID = $clientId;
        $this -> CLIENT_KEY = $clientKey;
        $this -> CLIENT_USERNAME = $clientUsername;
        $this -> CLIENT_PASSWORD = $clientPassword;
        $this -> STORE_KEY = $storeKey;
        $this -> API_URL = $apiUrl;
    }

    public function getCredentials() {
        return [
            'CLIENT_ID' => $this -> CLIENT_ID,
            'CLIENT_KEY' => $this -> CLIENT_KEY,
            'CLIENT_USERNAME' => $this -> CLIENT_USERNAME,
            'CLIENT_PASSWORD' => $this -> CLIENT_PASSWORD,
            'STORE_KEY' => $this -> STORE_KEY,
            'API_URL' => $this -> API_URL,
        ];
    }
}

function getVposCredentials($key, $dbClass) {
    try {
        $db = new \PDO('mysql:dbname='.$dbClass -> getDbName().';port='.$dbClass -> getPort().';host='.$dbClass -> getHost().';charset=utf8', $dbClass -> getUser(), $dbClass -> getPassword());
        $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $fetch_vpos_credentials = $db -> prepare("SELECT `client_id`, `client_key`, `client_username`, `client_password`, `store_key`, `api_url` FROM `Ms_Vpos_Credentials` WHERE `client_key` = :client_key");
        $fetch_vpos_credentials -> bindValue(':client_key',(string) $key, PDO::PARAM_STR);
        $fetch_vpos_credentials -> execute();

        $bankCredentialsRow  = $fetch_vpos_credentials-> fetch(PDO::FETCH_ASSOC);
        
        if ($bankCredentialsRow) {
            return new VposCredentials($bankCredentialsRow['client_id'] ?? NULL, $bankCredentialsRow['client_key'] ?? NULL, $bankCredentialsRow['client_username'] ?? NULL, $bankCredentialsRow['client_password'] ?? NULL, $bankCredentialsRow['store_key'] ?? NULL, $bankCredentialsRow['api_url'] ?? NULL);
        } else {
            return 400;
        }
    } catch(PDOException $e) {
        return 500;
    }

    $db = null;
}
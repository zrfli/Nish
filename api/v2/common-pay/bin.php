<?php
if($_SERVER['REQUEST_URI'] != '/api/v2/common-pay/bin/'.htmlspecialchars($_GET['bin'])){ header('Location: /'); exit(); }

require_once("../../../vendor/autoload.php");
require_once '../../../src/database/config.php';
require_once '../../../src/logger/logger.php';

$dbClass = new dbInformation();
$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $binData = []; $binCode = null;

header("Content-type: application/json; charset=utf-8");

try {
    $db = new \PDO('mysql:dbname='.$dbClass -> getDbName().';port='.$dbClass -> getPort().';host='.$dbClass -> getHost().';charset=utf8', $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);
	
    if (!$auth -> check()) { $errors['isLogged'] = false; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST') { $errors['method'] = 'method not accepted!'; }
    if (isset($_GET['bin']) AND is_numeric($_GET['bin'])) { $binCode = htmlspecialchars($_GET['bin']); } else { $errors['bin'] = 'bin code is not correct!'; }
    if (!$auth -> hasRole(\Delight\Auth\Role::STUDENT)) { $errors['permission'] = 'permission denied!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $fetch_bin_details = $db -> prepare("SELECT `bankName`,`cardBrand`,`cardBrandCode`,`cardProgram`,`cardType`,`cvvRequired`,`installment`,`countryCode`,`cardLength`,`cardBrandIcon`,`cardProgramIcon` FROM `Ms_Bins` WHERE `bin` = :binCode;");
        $fetch_bin_details -> bindValue(':binCode',(int) $binCode, PDO::PARAM_INT);
        $fetch_bin_details -> execute();

        if ($fetch_bin_details -> rowCount() > 0) {
            $result_fetch_bin_details = $fetch_bin_details -> fetch(PDO::FETCH_ASSOC);
            
            $binData = [
                'bankName' => $result_fetch_bin_details['bankName'] ?? NULL,
                'cardBrand' => $result_fetch_bin_details['cardBrand'] ?? NULL,
                'cardProgram' => $result_fetch_bin_details['cardProgram'] ?? NULL,
                'cardType' => $result_fetch_bin_details['cardType'] ?? NULL,
                'cvvRequired' => (bool) $result_fetch_bin_details['cvvRequired'] ?? NULL,
                'installment' => (bool) $result_fetch_bin_details['installment'] ?? NULL,
                'countryCode' => $result_fetch_bin_details['countryCode'] ?? NULL,
                'cardLength' => $result_fetch_bin_details['cardLength'] ?? NULL,
                'cardBrandIcon' => $result_fetch_bin_details['cardBrandIcon'] ?? NULL,
                'cardBrandCode' => $result_fetch_bin_details['cardBrandCode'] ?? 0,
                'cardProgramIcon' => $result_fetch_bin_details['cardProgramIcon'] ?? NULL
            ];

            $data['status'] = 'success'; 
            $data['statusCode'] = 200; 
            $data['token'] = \Delight\Auth\Auth::createRandomString(24); 
            $data['userId'] = \Delight\Auth\Auth::createUuid();
            $data['bankDetail'] = $binData;
        } else {
            $data['status'] = false;
            $data['statusCode'] = 406;
        }
    }
} catch (\Throwable $th) {
    $logger->logError($th, ['details' -> $th->getMessage(), 'user_id' -> $auth -> getUserId()], 'GET_BIN_DETAILS');
    die();
}

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
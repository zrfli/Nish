<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/payment/nish_pay') { header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = [];

header("Content-type: application/json; charset=utf-8");

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());

        $data['status'] = 'success'; 
        $data['statusCode'] = 200; 

        $data['payAmount'][1] = 10;
        $data['payAmount'][2] = 20;
        $data['payAmount'][3] = 30;
        $data['payAmount'][4] = 50;
        $data['payAmount'][5] = 100;
        $data['payAmount'][6] = 150;
        $data['payAmount'][7] = 200;
        $data['payAmount'][8] = 300;
        $data['payAmount'][9] = 500;

    }
    
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
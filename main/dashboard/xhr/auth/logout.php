<?php
if($_SERVER['REQUEST_URI'] != '/xhr/dashboard/auth/logout'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
//require_once '../../src/logger/logger.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);

$errors = []; $data = [];

header("Content-type: application/json; charset=utf-8");
try {
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
	$auth = new \Delight\Auth\Auth($db);
    
    if (!$auth -> check()){ $errors['isLogged'] = false; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }

	if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $auth -> logOut();

        $data['status'] = 'success'; 
        $data['statusCode'] = 200; 
    }
} catch (PDOException $e) { 
	//$logger->logError($th -> getMessage(), ['details' => $th, 'user_id' => $auth -> getUserId()], 'LOGOUT');
}

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
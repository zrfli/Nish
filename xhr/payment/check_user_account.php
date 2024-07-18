<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/payment/check_user_account') { header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $userAccountNumber = null;

header("Content-type: application/json; charset=utf-8");

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }
    if (isset($_POST['accountNumber'])) { $userAccountNumber = htmlspecialchars($_POST['accountNumber']); } else { $errors['accountNumber'] = 'empty'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());

        $check_user_account = $db -> prepare('SELECT * FROM `Ms_Users` WHERE `username` = :userAccountNumber LIMIT 1');
        $check_user_account -> bindValue(':userAccountNumber', $userAccountNumber, PDO::PARAM_STR);
        $check_user_account -> execute();
        
        if ($check_user_account -> rowCount() === 1) { 
            $check_user_account_fetch = $check_user_account -> fetch(PDO::FETCH_ASSOC);

            $data['status'] = 'success'; 
            $data['statusCode'] = 200;
            
            $data['userInformation']['fullName'] = mb_convert_case($check_user_account_fetch['first_name'] . ' ' . $check_user_account_fetch['last_name'], MB_CASE_TITLE, "UTF-8") ?? 'Error';
            $data['userInformation']['phoneNumber'] = $check_user_account_fetch['phone_number'] ?? 'Error';
            $data['userInformation']['nishCardBalance'] = 450;
        } else { $data['status'] = false; $data['statusCode'] = 404; }
    }
    
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
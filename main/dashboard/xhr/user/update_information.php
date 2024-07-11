<?php
if($_SERVER['REQUEST_URI'] != '/xhr/dashboard/user/update_information'){ header('Location: /'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
//require_once '../../src/logger/logger.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $email = null; $phoneNumber = null; $address = null;

header("Content-type: application/json; charset=utf-8");

try {
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);
	
    if (!$auth -> check()){ $errors['isLogged'] = false; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }
    
    if (empty($_POST['email']) || empty($_POST['phoneNumber']) || empty($_POST['address'])) { $errors['information'] = 'information is empty!'; } else {
        $email = htmlspecialchars(trim(strtolower($_POST['email'])), ENT_QUOTES, 'UTF-8');
        $phoneNumber = htmlspecialchars(trim($_POST['phoneNumber']), ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars(trim($_POST['address']), ENT_QUOTES, 'UTF-8');
    }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $data['token'] = \Delight\Auth\Auth::createRandomString(24); 
        $data['userId'] = \Delight\Auth\Auth::createUuid();

        if($auth -> misyPhoneNumber() == $phoneNumber AND $auth -> misyAddress() == $address AND $auth -> misyEmail() == $email) { 
            $data['status'] = false; 
            $data['statusCode'] = 406; 
            $data['errors'] = 'the same data';
        }

        if($auth -> misyEmail() != $email AND $auth -> misyValidateEmail($email)){
            $update_email = $db->prepare('UPDATE `Ms_Users` SET `email` = :email WHERE `id` = :userId AND NOT EXISTS (SELECT 1 FROM `Ms_Users` WHERE `email` = :email);');
            $update_email->bindValue(':email', $email, PDO::PARAM_STR);
            $update_email->bindValue(':userId', $auth -> getUserId(), PDO::PARAM_INT);
            $update_email->execute();
            
            if ($update_email -> rowCount() > 0) {
                $data['status'] = 'success'; 
                $data['statusCode'] = 200;

                $_SESSION['email'] = $email;
            } else {
                $data['status'] = false;
                $data['statusCode'] = 406; 
                $data['errors'] = 'email exists';
            }       
        }
        
        if($auth -> misyPhoneNumber() != $phoneNumber AND is_numeric($phoneNumber)){
            $update_number = $db->prepare('UPDATE `Ms_Users` SET `phone_number` = :phoneNumber WHERE `id` = :userId AND NOT EXISTS (SELECT 1 FROM `Ms_Users` WHERE `phone_number` = :phoneNumber);');
            $update_number->bindValue(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
            $update_number->bindValue(':userId', $auth -> getUserId(), PDO::PARAM_INT);
            $update_number->execute();
            
            if ($update_number -> rowCount() > 0) {
                $data['status'] = 'success'; 
                $data['statusCode'] = 200;

                $_SESSION['phoneNumber'] = $phoneNumber;
            } else {
                $data['status'] = false; 
                $data['statusCode'] = 406; 
                $data['errors'] = 'Number exists';
            }
        }

        if($auth -> misyAddress() != $address){
            $update_address = $db->prepare('UPDATE `Ms_Users` SET `address` = :address WHERE `id` = :userId;');
            $update_address->bindValue(':address', $address, PDO::PARAM_STR);
            $update_address->bindValue(':userId', $auth -> getUserId(), PDO::PARAM_INT);
            $update_address->execute();
            
            if ($update_address -> rowCount() > 0) {
                $data['status'] = 'success'; 
                $data['statusCode'] = 200;

                $_SESSION['address'] = $address;
            } else {
                $data['status'] = false; 
                $data['statusCode'] = 406; 
            }
        }
    }
    
} catch (\Throwable $th) {
    echo $th;
    //$logger->logError($th, ['details' -> $th->getMessage(), 'user_id' -> $auth -> getUserId()], 'UPDATE_INFORMATION');
    die();
}

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
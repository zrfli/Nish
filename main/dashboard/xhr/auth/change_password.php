<?php
if($_SERVER['REQUEST_URI'] != '/xhr/dashboard/auth/change_password'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
//require_once '../../src/logger/logger.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $password = null;

header("Content-type: application/json; charset=utf-8");

try {
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);
	
    if (!$auth -> check()){ $errors['isLogged'] = false; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }

    if ((empty($_POST['password']) OR empty($_POST['rePassword'])) OR $_POST['password'] != $_POST['rePassword']) { $errors['password'] = 'Password is not correct!'; } else { $password = htmlspecialchars($_POST['password']); }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $data['token'] = \Delight\Auth\Auth::createRandomString(24); 
        $data['userId'] = \Delight\Auth\Auth::createUuid();

        try {
            $auth -> changePasswordWithoutOldPassword($password);
            $data['status'] = 'success'; 
            $data['statusCode'] = 200; 
        } catch (\Delight\Auth\NotLoggedInException $e) {
            $data['status'] = false; 
            $data['statusCode'] = 406; 
            $data['errors'] = 'Not logged in';
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            $data['status'] = false; 
            $data['statusCode'] = 406; 
            $data['errors'] = 'Invalid password(s)';
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            $data['status'] = false; 
            $data['statusCode'] = 406; 
            $data['errors'] = 'Too many requests';
        }
    }
    
} catch (\Throwable $th) {
    //$logger->logError($th, ['details' -> $th->getMessage(), 'user_id' -> $auth -> getUserId()], 'CHANGE_PASSWORD');
    die();
}

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
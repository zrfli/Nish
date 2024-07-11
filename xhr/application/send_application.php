<?php
if($_SERVER['REQUEST_URI'] != '/xhr/application/send_application'){ header('Location: /not-found'); exit(); }

require_once '../../src/config.php';
require_once '../../src/database/config.php';
//require_once '../../src/logger/logger.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $validGrades = ['9', '10', '11', '12', '99']; $fullName = null; $phoneNumber = null; $email = null; $grade = null; $programList = null;

header("Content-type: application/json; charset=utf-8");

try {	
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }

    if (empty($_POST['fullName']) OR empty($_POST['phoneNumber']) OR empty($_POST['email']) OR empty($_POST['grade']) OR empty($_POST['programList'])) { $errors['information'] = 'information is empty!'; } else {
        $fullName = htmlspecialchars(trim($_POST['fullName']));
        $phoneNumber = htmlspecialchars(trim($_POST['phoneNumber']));
        $email = htmlspecialchars(trim($_POST['email']));
        $grade = htmlspecialchars(trim($_POST['grade']));
        $programList = strip_tags(trim($_POST['programList']));
    }

    if (!in_array($grade, $validGrades)) { $errors['grade'] = 'grade is not correct!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());

        //add the try catch exmp
        $send_application = $db -> prepare('INSERT INTO `Ms_Application` (`full_name`,`phone_number`,`email`,`grade`,`city`,`program`,`date`) VALUES (:fullName,:phoneNumber,:email,:grade,:city,:program,:date);');
        $send_application -> bindValue(':fullName', $fullName, PDO::PARAM_STR);
        $send_application -> bindValue(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
        $send_application -> bindValue(':email', $email, PDO::PARAM_STR);
        $send_application -> bindValue(':grade', $grade, PDO::PARAM_STR);
        $send_application -> bindValue(':city', null, PDO::PARAM_STR);
        $send_application -> bindValue(':program', $programList, PDO::PARAM_STR);
        $send_application -> bindValue(':date', time(), PDO::PARAM_INT);

        $send_application -> execute();
        
        if ($send_application -> rowCount() > 0) { $data['status'] = 'success'; $data['statusCode'] = 200; } else { $data['status'] = false; $data['statusCode'] = 406; }
    }
    
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
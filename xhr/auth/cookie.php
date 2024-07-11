<?php
$errors = []; $data = []; $status = false;

if($_SERVER['REQUEST_URI'] != '/xhr/cookie'){ header('Location: /'); exit(); }

header("Content-Type: application/json"); 
$data_ = json_decode(file_get_contents("php://input"));

try {
    //if(isset($data_ -> status)){ $status = htmlspecialchars($data_ -> status); } else { $errors['status'] = null; }
    if($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }
    if(isset($_COOKIE['cookie'])) { $errors['message'] = 'already exists cookie'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        try {
            setcookie('cookie', true, time() + (86400 * 30), '/');

            $data['status'] = 'success'; $data['statusCode'] = 200; $data['cookieStatus'] = $status;
        } catch (\Throwable $th) { $data['status'] = false; $data['statusCode'] = 501; }

    }

} catch (\Throwable $th) { $data['status'] = false; $data['statusCode'] = 501; }

echo json_encode($data);
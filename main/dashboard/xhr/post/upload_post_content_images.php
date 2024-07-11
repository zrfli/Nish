<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/dashboard/post/upload_post_content_images'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/fix_image_orientation.php';
//require_once '../../src/logger/logger.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $supportedFormats = ['webp', 'jpeg', 'png', 'jpg', 'gif']; $handledImages = null;

header("Content-type: application/json; charset=utf-8");

try {	
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);

    if (!$auth -> check()) { $errors['isLogged'] = false; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST') { $errors['method'] = 'method not accepted!'; }
    
    if (empty($_FILES['handledImages'])) { $errors['handledImages'] = 'handled images is not correct!'; } 

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $tempFilePath = $_FILES["handledImages"]["tmp_name"];

        //$fileName = pathinfo($_FILES["handledImages"]["name"], PATHINFO_FILENAME);
        $fileExtension = strtolower(pathinfo($_FILES["handledImages"]["name"], PATHINFO_EXTENSION));

        $uploadDir = 'uploads/post/'.date('Y').'/'.date('m').'/'.date('d').'/content/';

        $handledImages = match ($fileExtension) {
            'gif' => $uploadDir . \Delight\Auth\Auth::createUuid().'.gif',
            default => $uploadDir . \Delight\Auth\Auth::createUuid().'.webp',
        };
        
        $uploadedImagePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $handledImages;
        
        if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$uploadDir)) { mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$uploadDir, 0777, true); }

        if (in_array($fileExtension, $supportedFormats)) { 
            if (move_uploaded_file($tempFilePath, $uploadedImagePath)) {
                if ($fileExtension !== 'gif') {
                    fixImageOrientation($uploadedImagePath);

                    $sourceImage = imagecreatefromstring(file_get_contents($uploadedImagePath));
    
                    if ($sourceImage !== false) {
                        //$resizedImage = imagecreatetruecolor(395, 263);
    
                        //imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, 395, 263, imagesx($sourceImage), imagesy($sourceImage));
                        imagejpeg($sourceImage, $uploadedImagePath, 70);
        
                        imagedestroy($sourceImage);
                        //imagedestroy($resizedImage);
                    }
                }

                $data['location'] = $handledImages; $data['status'] = 'success'; $data['statusCode'] = 200;
            } else { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'file upload error'; }
        } else { $data['status'] = false; $data['statusCode'] = 400; $data['message'] = 'file type not supported'; }
    }
    
} catch(\throw $e) { echo $e; $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'unknown error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
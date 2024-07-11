<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/dashboard/post/create_post'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/slugify.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/fix_image_orientation.php';
//require_once '../../src/logger/logger.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $supportedFormats = ['webp', 'jpeg', 'png', 'jpg', 'gif'];  $contentValidLanguage = ['tr', 'en']; $contentValidType = ['news', 'events', 'research', 'announcements', 'achievements']; $contentSlug = null; $contentTitle = null; $contentCoverImage = null; $contentType = null; $contentLanguage = null; $contentHtmlMarkup = null;

header("Content-type: application/json; charset=utf-8");

try {	
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);

    if (!$auth -> check()){ $errors['isLogged'] = false; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }
    
    if (empty($_POST['contentTitle']) OR empty($_POST['contentType']) OR empty($_POST['contentHtmlMarkup'])  OR empty($_POST['contentLanguage'])) { 
        $errors['information'] = 'information is empty!';
    } else {
        $contentTitle = htmlspecialchars(trim($_POST['contentTitle']));
        $contentType = htmlspecialchars(trim($_POST['contentType']));
        $contentLanguage = htmlspecialchars(trim($_POST['contentLanguage']));
        $contentHtmlMarkup = trim($_POST['contentHtmlMarkup']);
        $contentSlug = slugify($contentTitle);
    }

    if (!in_array($contentType, $contentValidType)) { $errors['type'] = 'content type is not correct!'; }
    if (!in_array($contentLanguage, $contentValidLanguage)) { $errors['language'] = 'content language is not correct!'; }
    if (!isset($_FILES['contentCoverImage']) AND ($contentType == 'news' OR $contentType == 'research' OR $contentType == 'achievements')) { $errors['coverImage'] = 'cover image is not correct!'; }
    if (empty($contentSlug)) { $errors['slug'] = 'slug is not correct!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        if (isset($_FILES['contentCoverImage'])) {
            $tempFilePath = $_FILES["contentCoverImage"]["tmp_name"];
    
            //$fileName = pathinfo($_FILES["contentCoverImage"]["name"], PATHINFO_FILENAME);
            $fileExtension = strtolower(pathinfo($_FILES["contentCoverImage"]["name"], PATHINFO_EXTENSION));

            $uploadDir = 'uploads/post/'.date('Y').'/'.date('m').'/'.date('d').'/';

            $contentCoverImage = match ($fileExtension) {
                'gif' => $uploadDir . \Delight\Auth\Auth::createUuid().'.gif',
                default => $uploadDir . \Delight\Auth\Auth::createUuid().'.webp',
            };
            
            $uploadedImagePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $contentCoverImage;
            
            if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$uploadDir)) { mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$uploadDir, 0777, true); }
    
            if (in_array($fileExtension, $supportedFormats)) { 
                if (move_uploaded_file($tempFilePath, $uploadedImagePath)) {
                    if ($fileExtension !== 'gif') {
                        fixImageOrientation($uploadedImagePath);
    
                        $sourceImage = imagecreatefromstring(file_get_contents($uploadedImagePath));
        
                        if ($sourceImage !== false) {
                            $resizedImage = imagecreatetruecolor(395, 263);
        
                            imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, 395, 263, imagesx($sourceImage), imagesy($sourceImage));
                            imagejpeg($resizedImage, $uploadedImagePath);
            
                            imagedestroy($sourceImage);
                            imagedestroy($resizedImage);
                        }
                    }
                } else { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'file upload error'; }
            } else { $data['status'] = false; $data['statusCode'] = 400; $data['message'] = 'file type not supported'; }
        } 

        $insert_post = $db -> prepare('INSERT INTO `Ms_Posts` (`user_id`,`slug`,`post_title`,`post_text`,`post_type`,`cover_image`,`language`,`time`) VALUES (:userId,:contentSlug,:contentTitle,:contentHtmlMarkup,:contentType,:contentCoverImage,:contentLanguage,:contentTime);');
        $insert_post -> bindValue(':userId', $auth -> getUserId(), PDO::PARAM_INT);
        $insert_post -> bindValue(':contentSlug', $contentSlug, PDO::PARAM_STR);
        $insert_post -> bindValue(':contentTitle', $contentTitle, PDO::PARAM_STR);
        $insert_post -> bindValue(':contentHtmlMarkup', $contentHtmlMarkup, PDO::PARAM_STR);
        $insert_post -> bindValue(':contentType', $contentType, PDO::PARAM_STR);
        $insert_post -> bindValue(':contentCoverImage', $contentCoverImage, PDO::PARAM_STR);
        $insert_post -> bindValue(':contentLanguage', $contentLanguage, PDO::PARAM_STR);
        $insert_post -> bindValue(':contentTime', time(), PDO::PARAM_INT);

        $insert_post -> execute();
        
        if ($insert_post -> rowCount() > 0) { $data['status'] = 'success'; $data['statusCode'] = 200; } else { $data['status'] = false; $data['statusCode'] = 406; }
    }
    
} catch(PDOException $e) { echo $e; $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'unknown error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
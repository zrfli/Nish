<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/carousel/base_carousel'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass -> misyGetDb('mysql'));	

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

        $data['baseCarouselData'][1]['url'] = 'https://misy.online/announcements/2024-yili-mezuniyet-toreni-hakkinda-onemli-duyuru-466834';
        $data['baseCarouselData'][1]['image'] = 'uploads/carousel/2024/05/31/5.webp?v=35';

        $data['baseCarouselData'][2]['url'] = 'https://misy.online/announcements/2024-yili-mezuniyet-toreni-hakkinda-onemli-duyuru-466834';
        $data['baseCarouselData'][2]['image'] = 'uploads/carousel/2024/05/31/3.webp?v=35';
  
        $data['baseCarouselData'][3]['url'] = 'car3';
        $data['baseCarouselData'][3]['image'] = 'uploads/carousel/2024/05/31/1.webp?v=3';
    }
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
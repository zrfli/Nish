<?php
if($_SERVER['REQUEST_URI'] != '/xhr/brand/brand_logos'){ header('Location: /not-found'); exit(); }

require_once '../../src/database/config.php';
//require_once '../../src/logger/logger.php';

if(isset($_COOKIE['lang'])){ require_once('../../src/language/'.strip_tags($_COOKIE['lang']).'.php'); } else { require_once('../../src/language/turkish.php'); }

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

        $data['brandLogosData'][1]['image'] = 'uploads/brand/2024/05/31/1.webp';
        $data['brandLogosData'][1]['active'] = 1;

        $data['brandLogosData'][2]['image'] = 'uploads/brand/2024/05/31/2.webp';
        $data['brandLogosData'][2]['active'] = 1;
        
        $data['brandLogosData'][3]['image'] = 'uploads/brand/2024/05/31/3.webp';
        $data['brandLogosData'][3]['active'] = 1;
        
        $data['brandLogosData'][4]['image'] = 'uploads/brand/2024/05/31/4.webp';
        $data['brandLogosData'][4]['active'] = 1;
        
        $data['brandLogosData'][5]['image'] = 'uploads/brand/2024/05/31/5.webp';
        $data['brandLogosData'][5]['active'] = 1;
        
        $data['brandLogosData'][6]['image'] = 'uploads/brand/2024/05/31/6.webp';
        $data['brandLogosData'][6]['active'] = 1;
        
        $data['brandLogosData'][7]['image'] = 'uploads/brand/2024/05/31/7.webp';
        $data['brandLogosData'][7]['active'] = 1;
        
        $data['brandLogosData'][8]['image'] = 'uploads/brand/2024/05/31/8.webp';
        $data['brandLogosData'][8]['active'] = 1;
        
        $data['brandLogosData'][9]['image'] = 'uploads/brand/2024/05/31/10.webp';
        $data['brandLogosData'][9]['active'] = 1;
        
        $data['brandLogosData'][10]['image'] = 'uploads/brand/2024/05/31/12.webp';
        $data['brandLogosData'][10]['active'] = 1;
        
        $data['brandLogosData'][11]['image'] = 'uploads/brand/2024/05/31/14.webp';
        $data['brandLogosData'][11]['active'] = 1;
        
        $data['brandLogosData'][12]['image'] = 'uploads/brand/2024/05/31/15.webp';
        $data['brandLogosData'][12]['active'] = 1;
        
        $data['brandLogosData'][13]['image'] = 'uploads/brand/2024/05/31/18.webp';
        $data['brandLogosData'][13]['active'] = 1;
        
        $data['brandLogosData'][14]['image'] = 'uploads/brand/2024/05/31/19.webp';
        $data['brandLogosData'][14]['active'] = 1;
        
        $data['brandLogosData'][15]['image'] = 'uploads/brand/2024/05/31/23.webp';
        $data['brandLogosData'][15]['active'] = 1;
        
        $data['brandLogosData'][16]['image'] = 'uploads/brand/2024/05/31/27.webp';
        $data['brandLogosData'][16]['active'] = 1;

        $data['brandLogosData'][17]['image'] = 'uploads/brand/2024/05/31/29.webp';
        $data['brandLogosData'][17]['active'] = 1;
        
        $data['brandLogosData'][18]['image'] = 'uploads/brand/2024/05/31/35.webp';
        $data['brandLogosData'][18]['active'] = 1;
        
        $data['brandLogosData'][19]['image'] = 'uploads/brand/2024/05/31/39.webp';
        $data['brandLogosData'][19]['active'] = 1;
        
        $data['brandLogosData'][20]['image'] = 'uploads/brand/2024/05/31/42.webp';
        $data['brandLogosData'][20]['active'] = 1;
    }
    
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
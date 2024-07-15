<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/layouts/footer'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';

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

        $data['layoutsFooterData'][1]['contact'] = 'Maslak Mahalesi, Taşyoncası Sokak, No: 1V ve No:1Y Sarıyer-İstanbul <a href="tel:+902122101010" class="text-white underline">+90 (212) 210 10 10</a>';
        $data['layoutsFooterData'][1]['x'] = 'https://x.com/nisantasiedu';
        $data['layoutsFooterData'][1]['instagram'] = 'https://www.instagram.com/nisantasiedu';
        $data['layoutsFooterData'][1]['facebook'] = 'https://www.facebook.com/nisantasiedu';
        $data['layoutsFooterData'][1]['linkedin'] = 'https://www.linkedin.com/school/nisantasiuniversity';
        $data['layoutsFooterData'][1]['whatsapp'] = 'https://api.whatsapp.com/send?phone=905312986235&text=Merhaba';

        $data['layoutsFooterData'][2]['category'] = 'Kurumsal';        
        $data['layoutsFooterData'][2]['element'][1] = array('name' => 'Tarihçe', 'url' => 'tarhice');
        $data['layoutsFooterData'][2]['element'][2] = array('name' => 'Misyon ve Vizyon', 'url' => 'misyon-vizyon');
        $data['layoutsFooterData'][2]['element'][3] = array('name' => 'Kişisel Veriler (kVKK)', 'url' => 'kvkk');
        $data['layoutsFooterData'][2]['active'] = 1;
        
        $data['layoutsFooterData'][3]['category'] = 'Kampüslerimiz';        
        $data['layoutsFooterData'][3]['element'][1] = array('name' => 'NeoTech Campus', 'url' => 'neotech-campus');
        $data['layoutsFooterData'][3]['element'][2] = array('name' => 'Silivri Kampüsü', 'url' => 'silivri-campus');
        $data['layoutsFooterData'][3]['active'] = 1;

        $data['layoutsFooterData'][4]['category'] = 'Tanıtım';        
        $data['layoutsFooterData'][4]['element'][1] = array('name' => 'Fotoğraflar', 'url' => 'fotograflar');
        $data['layoutsFooterData'][4]['active'] = 1;
        
        $data['layoutsFooterData'][5]['category'] = 'Bilgilendirme';        
        $data['layoutsFooterData'][2]['element'][1] = array('name' => 'Yatay Geçiş', 'url' => 'tarhice');
        $data['layoutsFooterData'][5]['element'][2] = array('name' => 'Dikey Geçiş', 'url' => 'misyon-vizyon');
        $data['layoutsFooterData'][5]['element'][3] = array('name' => 'Özel Yetenek', 'url' => 'kvkk');
        $data['layoutsFooterData'][5]['element'][4] = array('name' => 'Bologna / Ders İçerikleri', 'url' => 'kvkk');
        $data['layoutsFooterData'][5]['active'] = 1;

        $data['layoutsFooterData'][6]['category'] = 'Erişim';        
        $data['layoutsFooterData'][6]['element'][1] = array('name' => 'İletişim', 'url' => 'tarhice');
        $data['layoutsFooterData'][6]['element'][2] = array('name' => 'İhaleler', 'url' => 'misyon-vizyon');
        $data['layoutsFooterData'][6]['element'][3] = array('name' => 'OBİS', 'url' => 'kvkk');
        $data['layoutsFooterData'][6]['element'][4] = array('name' => 'Online Ödeme', 'url' => 'kvkk');
        $data['layoutsFooterData'][6]['element'][5] = array('name' => 'Sanal Kampüs', 'url' => 'kvkk');
        $data['layoutsFooterData'][6]['element'][6] = array('name' => 'EBYS', 'url' => 'kvkk');
        $data['layoutsFooterData'][6]['active'] = 1;
    }
    
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
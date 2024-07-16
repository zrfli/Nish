<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/payment/nish_pay') { header('Location: /not-found'); exit(); }

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

        $data['quickAccessData'][1]['name'] = 'Öğrenci Dekanlığı';
        $data['quickAccessData'][1]['url'] = 'https://nisantasi.edu.tr/ogrenci-dekanliğ';
        $data['quickAccessData'][1]['active'] = 1;

        $data['quickAccessData'][2]['name'] = 'Kütüphane';
        $data['quickAccessData'][2]['url'] = 'https://nisantasi.edu.tr/ogrenci-Kütüphane';
        $data['quickAccessData'][2]['active'] = 1;
        
        $data['quickAccessData'][3]['name'] = 'Akademik Takvim';
        $data['quickAccessData'][3]['url'] = 'https://nisantasi.edu.tr/ogrenci-Akademik Takvim';
        $data['quickAccessData'][3]['active'] = 1;
        
        $data['quickAccessData'][4]['name'] = 'Bilimsel Faaliyetler';
        $data['quickAccessData'][4]['url'] = 'https://nisantasi.edu.tr/ogrenci-Bilimsel Faaliyetler';
        $data['quickAccessData'][4]['active'] = 1;
        
        $data['quickAccessData'][5]['name'] = 'E-Bülten';
        $data['quickAccessData'][5]['url'] = 'https://nisantasi.edu.tr/ogrenci-E-Bülten';
        $data['quickAccessData'][5]['active'] = 1;
        
        $data['quickAccessData'][6]['name'] = 'SEM';
        $data['quickAccessData'][6]['url'] = 'https://nisantasi.edu.tr/ogrenci-SEM';
        $data['quickAccessData'][6]['active'] = 1;
        
        $data['quickAccessData'][7]['name'] = 'OBİS';
        $data['quickAccessData'][7]['url'] = 'https://nisantasi.edu.tr/ogrenci-OBİS';
        $data['quickAccessData'][7]['active'] = 1;
        
        $data['quickAccessData'][8]['name'] = 'Sanal Kampüs';
        $data['quickAccessData'][8]['url'] = 'https://nisantasi.edu.tr/ogrenci-Sanal Kampüs';
        $data['quickAccessData'][8]['active'] = 1;
        
        $data['quickAccessData'][9]['name'] = 'Bologna / Ders İçerikleri';
        $data['quickAccessData'][9]['url'] = 'https://nisantasi.edu.tr/ogrenci-Bologna / Ders İçerikleri';
        $data['quickAccessData'][9]['active'] = 1;
        
        $data['quickAccessData'][10]['name'] = 'Kariyer Merkezi';
        $data['quickAccessData'][10]['url'] = 'https://nisantasi.edu.tr/Kariyer Merkezi';
        $data['quickAccessData'][10]['active'] = 1;
        
        $data['quickAccessData'][11]['name'] = 'Formlar';
        $data['quickAccessData'][11]['url'] = 'https://nisantasi.edu.tr/ogrenci-Formlar';
        $data['quickAccessData'][11]['active'] = 1;
        
        $data['quickAccessData'][12]['name'] = 'Lisansüstü';
        $data['quickAccessData'][12]['url'] = 'https://nisantasi.edu.tr/ogrenci-Lisansüstü';
        $data['quickAccessData'][12]['active'] = 1;
        
        $data['quickAccessData'][13]['name'] = 'Yatay Geçiş';
        $data['quickAccessData'][13]['url'] = 'https://nisantasi.edu.tr/ogrenci-Yatay Geçiş';
        $data['quickAccessData'][13]['active'] = 1;
        
        $data['quickAccessData'][14]['name'] = 'Özel Yetenek';
        $data['quickAccessData'][14]['url'] = 'https://nisantasi.edu.tr/ogrenci-Özel Yetenek';
        $data['quickAccessData'][14]['active'] = 1;
        
        $data['quickAccessData'][15]['name'] = 'DGS';
        $data['quickAccessData'][15]['url'] = 'https://nisantasi.edu.tr/ogrenci-DGS';
        $data['quickAccessData'][15]['active'] = 1;
        
        $data['quickAccessData'][16]['name'] = 'Uluslararası Ofis';
        $data['quickAccessData'][16]['url'] = 'https://nisantasi.edu.tr/ogrenci-Uluslararası Ofis';
        $data['quickAccessData'][16]['active'] = 1;
    }
    
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
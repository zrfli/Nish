<?php
if($_SERVER['REQUEST_URI'] != '/xhr/layouts/header'){ header('Location: /not-found'); exit(); }

require_once '../../src/language/language.php';
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

        $data['layoutsHeaderData'][0]['name'] = 'Kurumsal';      
        $data['layoutsHeaderData'][0]['sub_category'][0][0] = 'Hakkında';
        $data['layoutsHeaderData'][0]['sub_category'][0][1] = array('name' => '- Tarihçe', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][0][2] = array('name' => '- Misyon ve Vizyon', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][0][3] = array('name' => '- Kalite', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][0][4] = array('name' => '- Organizasyon Şeması', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][0][5] = array('name' => '- Kişisel Veriler (page/tarihce)', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][0][6] = array('name' => '- Senato Kararları', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][1][0] = 'Yönetim';
        $data['layoutsHeaderData'][0]['sub_category'][1][1] = array('name' => '- Nişantaşı Eğitim Vakfı Kurucusu Sayın Levent Uysal’ın Mesajı', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][1][2] = array('name' => '- Rektörün Mesajı', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][1][3] = array('name' => '- Rektörlük', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][1][4] = array('name' => '- Senato Üyeleri', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][1][5] = array('name' => '- Yönetim Kurulu Üyeleri', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][1][6] = array('name' => '- Genel Sekreterlik', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][1][7] = array('name' => '- İdari Birimler', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][1][8] = array('name' => '- Mevzuat', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][2][0] = 'Kurumsal İletişim';
        $data['layoutsHeaderData'][0]['sub_category'][2][1] = array('name' => '- Fotoğraf/Video Galerisi', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][2][2] = array('name' => '- Kurumsal Kimlik', 'url' => 'page/tarihce');

        $data['layoutsHeaderData'][0]['sub_category'][3][0] = 'Rektörlüğe Bağlı Birimler';
        $data['layoutsHeaderData'][0]['sub_category'][3][1] = array('name' => '- Nişantaşı Kültür Sanat Koordinatörlüğü', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][3][2] = array('name' => '- Sürdürülebilir Toplum ve Yenileşim Koordinatörlüğü', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][3][3] = array('name' => '- Kalite ve Yönetişim Koordinatörlüğü', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][3][4] = array('name' => '- Bilimsel Faaliyetler Koordinatörlüğü', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][3][5] = array('name' => '- Öğrenme ve Öğretme Merkezi (INU-CELT)', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][3][6] = array('name' => '- Genel Sekreterlik', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][3][7] = array('name' => '- İdari Birimler', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][3][8] = array('name' => '- Mevzuat', 'url' => 'page/tarihce');

        $data['layoutsHeaderData'][0]['sub_category'][4][0] = 'Komisyonlar / Kurullar';
        $data['layoutsHeaderData'][0]['sub_category'][4][1] = array('name' => '- Yayın Komisyonu', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][4][2] = array('name' => '- Mevzuat Komisyonu', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][4][3] = array('name' => '- Türkçe Yeterlilik Sınav Komisyonu', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][4][4] = array('name' => '- Açık Erişim Komisyonu', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][4][5] = array('name' => '- Eğitim Öğretim Komisyonu', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][4][6] = array('name' => '- Stratejik Plan Komisyonu', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][4][7] = array('name' => '- Engelli Öğrenci Birimi Komisyonu', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][4][8] = array('name' => '- Bilimsel Yayın ve Akademik Etkinlikleri Destekleme Teşvik Komisyonu', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][0]['sub_category'][4][9] = array('name' => '- Etik Kurul', 'url' => 'page/tarihce');


        $data['layoutsHeaderData'][1]['name'] = 'Akademik';      
        $data['layoutsHeaderData'][1]['sub_category'][0][0] = 'Hakkında';
        $data['layoutsHeaderData'][1]['sub_category'][0][1] = array('name' => '- Misyon ve Vizyon', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][1]['sub_category'][0][2] = array('name' => '- Kişisel Veriler (page/tarihce)', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][1]['sub_category'][1][0] = 'Yönetim';
        $data['layoutsHeaderData'][1]['sub_category'][1][1] = array('name' => '- Misyon ve Vizyon', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][1]['sub_category'][1][2] = array('name' => '- Kişisel Veriler (page/tarihce)', 'url' => 'page/tarihce');

        $data['layoutsHeaderData'][2]['name'] = 'NeoTech Kampüs';      
        $data['layoutsHeaderData'][2]['sub_category'][0][0] = 'Hakkında';
        $data['layoutsHeaderData'][2]['sub_category'][0][1] = array('name' => '- Misyon ve Vizyon', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][2]['sub_category'][0][2] = array('name' => '- Kişisel Veriler (page/tarihce)', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][2]['sub_category'][1][0] = 'Yönetim';
        $data['layoutsHeaderData'][2]['sub_category'][1][1] = array('name' => '- Misyon ve Vizyon', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][2]['sub_category'][1][2] = array('name' => '- Kişisel Veriler (page/tarihce)', 'url' => 'page/tarihce');

        $data['layoutsHeaderData'][3]['name'] = 'Öğrenci';      
        $data['layoutsHeaderData'][3]['sub_category'][0][0] = 'Hakkında';
        $data['layoutsHeaderData'][3]['sub_category'][0][1] = array('name' => '- Misyon ve Vizyon', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][3]['sub_category'][0][2] = array('name' => '- Kişisel Veriler (page/tarihce)', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][3]['sub_category'][1][0] = 'Yönetim';
        $data['layoutsHeaderData'][3]['sub_category'][1][1] = array('name' => '- Misyon ve Vizyon', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][3]['sub_category'][1][2] = array('name' => '- Kişisel Veriler (page/tarihce)', 'url' => 'page/tarihce');

        $data['layoutsHeaderData'][4]['name'] = 'International';      
        $data['layoutsHeaderData'][4]['url'] = 'afsafssaf';

        $data['layoutsHeaderData'][5]['name'] = 'Aday Öğrenci';      
        $data['layoutsHeaderData'][5]['sub_category'][0][0] = 'Hakkında';
        $data['layoutsHeaderData'][5]['sub_category'][0][1] = array('name' => '- Misyon ve Vizyon', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][5]['sub_category'][0][2] = array('name' => '- Kişisel Veriler (page/tarihce)', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][5]['sub_category'][1][0] = 'Yönetim';
        $data['layoutsHeaderData'][5]['sub_category'][1][1] = array('name' => '- Misyon ve Vizyon', 'url' => 'page/tarihce');
        $data['layoutsHeaderData'][5]['sub_category'][1][2] = array('name' => '- Kişisel Veriler (page/tarihce)', 'url' => 'page/tarihce');
    }
    
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
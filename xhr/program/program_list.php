<?php
if($_SERVER['REQUEST_URI'] != '/xhr/program/program_list') { header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/modules/config.php';

use Phpfastcache\Helper\Psr16Adapter;

if(isset($_COOKIE['lang'])){ require_once('../../src/language/'.strip_tags($_COOKIE['lang']).'.php'); } else { require_once('../../src/language/turkish.php'); }

$dbClass = new misyDbInformation();
$Psr16Adapter = new Psr16Adapter('Files');
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $programs = [];

header("Content-type: application/json; charset=utf-8");

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $data['status'] = 'success'; 
        $data['statusCode'] = 200; 

        if (!$Psr16Adapter -> has('applicationProgramList')) {
            $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
        
            $fetch_programs = $db -> prepare("SELECT MD.`id`,MD.`department_name`,MU.`unit_name` FROM `Ms_Department` MD JOIN `Ms_Unit` MU ON MD.`unit_id` = MU.`id` ORDER BY MD.`department_name` ASC;");
            $fetch_programs -> execute();
            
            if ($fetch_programs -> rowCount() > 0) { 
                $programs = $fetch_programs -> fetchAll(PDO::FETCH_ASSOC); 

                $Psr16Adapter -> set('applicationProgramList', $programs, 600);
            }
        } else { $programs = $Psr16Adapter -> get('applicationProgramList'); }

        $data['program_list'] = $programs;
    }    
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
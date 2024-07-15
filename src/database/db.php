<?php
#Author: Misy
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) { session_start(); ob_start(); }

//use Gregwar\Image\Image;
//use chillerlan\QRCode\QRCode; 

if ($_SERVER['REQUEST_URI'] == '/src/database/db.php') { header('Location: /'); exit(); } 

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';
//require_once $_SERVER['DOCUMENT_ROOT'].'/src/logger/logger.php';

if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $langs)) {
    require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/' . strip_tags($_COOKIE['lang']) . '.php';
} else { require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/turkish.php'; }

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);

try {
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
    $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $auth = new \Delight\Auth\Auth($db);

    //if ($auth -> check()) { 
        //if ($auth->misyNationality() != null && in_array($auth->misyNationality(), $country)) {
        //    $_SESSION['nationality'] = $misy['country'][$auth->misyNationality()];
        //}

        //if ($auth->misyStatus() !== 1) {
        //    header('Location: logout');
        //}

        
        //if ($auth->misyIdenityVerify() == 0 || $auth->misyInfoVerify() == 0) {
        //    if ($_SERVER['REQUEST_URI'] != '/start-up') {
        //        header("Location: start-up");
        //    }
        //} else if ($auth->misyContractsVerify() != 1 && $_SERVER['REQUEST_URI'] != '/account-update') {
        //    header('Location: account-update');
        //}

        //if ($auth -> hasAnyRole(\Delight\Auth\Role::ADMIN, \Delight\Auth\Role::DEVELOPER)) { header('Location: /dashboard/'); }
    //}
} catch (\Throwable $th) {
    var_dump($th);
    //$logger->logError($th -> getMessage(), ['details' => $th], 'SYSTEM');
}

//$db = null;
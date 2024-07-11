<?php
if ($_SERVER['REQUEST_URI'] != '/dashboard'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/db.php'; 

if (!$auth -> isLoggedIn()){ header('Location: /login'); exit(); }

include_once 'inc/head/content.phtml';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php';
include_once 'inc/header/content.phtml'; 
include_once 'inc/sidebar/content.phtml'; 
include_once 'inc/index_base/content.phtml';
include_once 'inc/js/content.phtml';
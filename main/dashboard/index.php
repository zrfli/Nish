<?php
if ($_SERVER['REQUEST_URI'] != '/dashboard') { header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/db.php'; 

if (!$auth -> isLoggedIn()) { header('Location: /login'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/head/content.phtml';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/header/content.phtml'; 
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/sidebar/content.phtml'; 
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/index_base/content.phtml';
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/js/content.phtml';
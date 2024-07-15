<?php
if ($_SERVER['REQUEST_URI'] == '/src/language/languageController.php') { header("Location: /"); exit(); }

$langs = ['turkish', 'english'];

if (!isset($_COOKIE['lang']) OR !in_array($_COOKIE['lang'], $langs)) { setcookie('lang', 'turkish', time() + 60 * 60 * 24 * 365, '/'); } 

require_once match ($_COOKIE['lang']) {
    'turkish' => $_SERVER['DOCUMENT_ROOT'].'/src/language/turkish.php',
    'english' => $_SERVER['DOCUMENT_ROOT'].'/src/language/english.php',
    default => $_SERVER['DOCUMENT_ROOT'].'/src/language/turkish.php'
};
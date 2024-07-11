<?php
$langs = array('turkish','english','arabic');

if(!isset($_COOKIE['lang']) OR !in_array($_COOKIE['lang'], $langs)){ setcookie('lang', 'turkish', time() + 60 * 60 * 24 * 365, '/'); }
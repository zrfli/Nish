<?php
if ($_SERVER['REQUEST_URI'] == '/src/functions/slugify.php') { header("Location: /"); exit(); }

function slugify($text) {
    $text = str_replace(
        ['ı', 'İ', 'ş', 'Ş', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç', ' ', '&'],
        ['i', 'i', 's', 's', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c', '-', 'and'],
        $text
    );
    
    $text = preg_replace('/[^a-zA-Z0-9\-]+/', '-', $text);
    $text = trim(strtolower($text), '-');
    $text .= '-' . mt_rand(100000, 999999);

    return $text;
}
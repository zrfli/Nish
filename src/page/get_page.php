<?php
if ($_SERVER['REQUEST_URI'] == '/src/post/get_page.php') { header("Location: /"); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';

function cleanInput($data) { return htmlspecialchars(stripslashes(trim($data))); }

function getPage($pageData) {
    $dbClass = new misyDbInformation();

    if (empty($pageData['slug'])) { return false; }

    $slug = cleanInput($pageData['slug']);

    try {
        $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());

        $fetch_post = $db -> prepare('SELECT `page_title`, `page_text`, `page_file`, `time` FROM `Ms_Pages` WHERE `slug` = :slug');
        $fetch_post -> bindValue(':slug',(string) $slug, PDO::PARAM_STR);
        $fetch_post -> execute();

        $post  = $fetch_post -> fetch(PDO::FETCH_ASSOC);
        
        if ($post > 0) { 
            return [
                'pageTitle' => $post['page_title'] ?? NULL,
                'pageText' => $post['page_text'] ?? NULL,
                'pagetFile' => $post['page_file'] ?? NULL,
                'time' => $post['time'] ?? NULL,
                'status' => 200
            ];
        } else { return ['status' => 404]; }
    } catch(PDOException $e) { return ['status' => 500]; } finally { $db = null; }
}
<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/dashboard/post/load_more_content'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
//require_once '../../src/logger/logger.php';
if (isset($_COOKIE['lang'])){ require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/'.strip_tags($_COOKIE['lang'].'.php'); } else { require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/turkish.php'; }

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $key = 0; $paramsList = ['pages', 'posts'];

header("Content-type: application/json; charset=utf-8");

try {	
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }

    if (isset($_POST['params']) && in_array($_POST['params'], $paramsList)) { $params = htmlspecialchars($_POST['params']); } else { $errors['params'] = 'params is not correct'; } 
    if (isset($_POST['key']) && $_POST['key'] >= 6) { $key = htmlspecialchars($_POST['key']); } else { $errors['key'] = 'key is not correct'; } 

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
        $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $fetch_content_query = match ($params) {
            'posts' => 'SELECT `id`, `slug`, `post_title`, `post_type`, `language`, `time` FROM `Ms_Posts` ORDER BY `time` DESC LIMIT :contentLimit OFFSET :startKey;',
            'pages' => 'SELECT `id`, `slug`, `page_title`, `page_type`, `language`, `time` FROM `Ms_Pages` ORDER BY `time` DESC LIMIT :contentLimit OFFSET :startKey;'
        };
        
        $fetch_content = $db -> prepare($fetch_content_query);
        $fetch_content -> bindValue(':contentLimit', 9, PDO::PARAM_INT);
        $fetch_content -> bindValue(':startKey', $key, PDO::PARAM_INT);
        $fetch_content -> execute();

        $base_posts = $fetch_content -> fetchAll(PDO::FETCH_ASSOC);
        
        if (is_array($base_posts) && count($base_posts) > 0) { 
            $data['status'] = 'success'; 
            $data['statusCode'] = 200; 
            
            $i = 0;

            foreach ($base_posts as $post) {
                $data['posts'][$i]['id'] = $post['id'] ?? '';
                $data['posts'][$i]['language'] = $post['language'] ?? '';
                $data['posts'][$i]['time'] = date('d/m/Y H:i', $post['time']);

                $data['posts'][$i]['slug'] =  match ($params) {
                    'posts' => $post['post_type'].'/'.$post['slug'] ?? '',
                    'pages' => 'page/'.$post['slug'] ?? '',
                };

                $data['posts'][$i]['title'] =  match ($params) {
                    'posts' => trim($post['post_title']) ?? '',
                    'pages' => trim($post['page_title']) ?? '',
                };

                $data['posts'][$i]['type'] =  match ($params) {
                    'posts' => $post['post_type'] ?? '',
                    'pages' => $post['page_type'] ?? '',
                };

                $i++;
            }
        } else { $data['status'] = false; $data['statusCode'] = 404; $data['message'] = 'content not found!'; }
    }
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
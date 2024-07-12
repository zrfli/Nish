<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/dashboard/post/published_posts'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
//require_once '../../src/logger/logger.php';
if (isset($_COOKIE['lang'])){ require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/'.strip_tags($_COOKIE['lang'].'.php'); } else { require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/turkish.php'; }

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $month = null;

header("Content-type: application/json; charset=utf-8");

try {	
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
        $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $fetch_base_posts = $db -> prepare("SELECT `id`, `slug`, `post_title`, `post_type`, `language`, `time` FROM `Ms_Posts` ORDER BY `time` DESC LIMIT :postLimit;");
        $fetch_base_posts -> bindValue(':postLimit', 9, PDO::PARAM_INT);
        $fetch_base_posts -> execute();

        $base_posts = $fetch_base_posts -> fetchAll(PDO::FETCH_ASSOC);
        
        if (is_array($base_posts) && count($base_posts) > 0) { 
            $data['status'] = 'success'; 
            $data['statusCode'] = 200; 
            
            $i = 0;

            foreach ($base_posts as $post) {
                $data['publishedPostsData'][$i]['id'] = $post['id'] ?? '';
                $data['publishedPostsData'][$i]['title'] = trim($post['post_title']) ?? '';
                $data['publishedPostsData'][$i]['time'] = date('d/m/Y H:i', $post['time']);
                $data['publishedPostsData'][$i]['slug'] = $post['post_type'].'/'.$post['slug'] ?? '';
                $data['publishedPostsData'][$i]['language'] = $post['language'] ?? '';
                $data['publishedPostsData'][$i]['type'] = $post['post_type'] ?? '';

                $i++;
            }
        } else { $data['status'] = false; $data['statusCode'] = 404; $data['message'] = 'post not found!'; }
    }
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
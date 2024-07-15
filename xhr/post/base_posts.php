<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/post/base_posts') { header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';

//require_once '../../src/logger/logger.php';

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

        $fetch_base_posts = $db -> prepare("WITH `RankedPosts` AS ( SELECT *, ROW_NUMBER() OVER( PARTITION BY `post_type` ORDER BY `time` DESC) AS rn FROM `Ms_Posts` ) SELECT `id`, `slug`, `post_title`, LEFT(`post_text`, :maxTextCharacter) AS `post_text`, `post_file`, `post_type`, `cover_image`, `language`, `time` FROM `RankedPosts` WHERE rn <= :postLimit ORDER BY `post_type`, `time` DESC;");
        $fetch_base_posts -> bindValue(':postLimit', 6, PDO::PARAM_INT);
        $fetch_base_posts -> bindValue(':maxTextCharacter', 300, PDO::PARAM_INT);
        $fetch_base_posts -> execute();

        $base_posts = $fetch_base_posts -> fetchAll(PDO::FETCH_ASSOC);
        
        if (is_array($base_posts) && count($base_posts) > 0) { 
            $data['status'] = 'success'; 
            $data['statusCode'] = 200; 
            
            $i = 0;

            foreach ($base_posts as $post) {
                $data['posts'][$i]['title'] = trim($post['post_title']) ?? '';
                $data['posts'][$i]['description'] = $post['post_text'] ?? '';
                
                if ($post['cover_image']) { $data['posts'][$i]['image'] = $post['cover_image']; }
        
                if ($post['post_type'] == 'events' || $post['post_type'] == 'announcements') {
                    if ($post['time']) { $month = strftime("%B", strtotime(date('F', $post['time']))); }

                    $data['posts'][$i]['date']['month'] = $month ?? '';
                    $data['posts'][$i]['date']['day'] = date('d', $post['time']);
                } else { $data['posts'][$i]['date'] = date('d/m/Y', $post['time']); }
        
                $data['posts'][$i]['url'] = $post['post_type'].'/'.$post['slug'] ?? '';
                $data['posts'][$i]['type'] = $post['post_type'] ?? '';

                $i++;
            }
        } else { $data['status'] = false; $data['statusCode'] = 404; $data['message'] = 'post not found!'; }
    }
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
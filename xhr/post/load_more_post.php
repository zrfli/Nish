<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/post/load_more_post'){ header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';
//require_once '../../src/logger/logger.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $month = null; $params = null; $key = 0; $paramsList = ['events', 'news', 'research', 'announcements', 'achievements'];

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

        $fetch_more_posts = $db -> prepare("SELECT `id`, `slug`, `post_title`, LEFT(`post_text`, :maxTextCharacter) AS `post_text`, `post_file`, `post_type`, `cover_image`, `language`, `time` FROM `Ms_Posts` WHERE `post_type` = :postType ORDER BY `time` DESC LIMIT :postLimit OFFSET :startKey;");
        $fetch_more_posts -> bindValue(':startKey', $key, PDO::PARAM_INT);
        $fetch_more_posts -> bindValue(':postLimit', 6, PDO::PARAM_INT);
        $fetch_more_posts -> bindValue(':postType', $params, PDO::PARAM_STR);
        $fetch_more_posts -> bindValue(':maxTextCharacter', 300, PDO::PARAM_INT);
        $fetch_more_posts -> execute();

        $more_posts = $fetch_more_posts -> fetchAll(PDO::FETCH_ASSOC);
        
        if (is_array($more_posts) && count($more_posts) > 0) { 
            $data['status'] = 'success'; 
            $data['statusCode'] = 200; 
            
            $i = 0;

            foreach ($more_posts as $post) {
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
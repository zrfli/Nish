<?php
if ($_SERVER['REQUEST_URI'] == '/src/post/get_post.php') { header("Location: /"); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';

function cleanInput($data) { return htmlspecialchars(stripslashes(trim($data))); }

function getPost($postData) {
    $dbClass = new misyDbInformation();

    if (empty($postData['params']) AND empty($postData['slug'])) { return false; }

    $postType = cleanInput($postData['params']); $slug = cleanInput($postData['slug']);

    try {
        $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
        $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $fetch_post = $db -> prepare('SELECT `post_title`, `post_text`, `post_file`, `post_type`, `time` FROM `Ms_Posts` WHERE `post_type` = :postType AND `slug` = :slug');
        $fetch_post -> bindValue(':postType',(string) $postType, PDO::PARAM_STR);
        $fetch_post -> bindValue(':slug',(string) $slug, PDO::PARAM_STR);
        $fetch_post -> execute();

        $post  = $fetch_post -> fetch(PDO::FETCH_ASSOC);
        
        if ($post > 0) { 
            return [
                'postTitle' => $post['post_title'] ?? NULL,
                'postText' => $post['post_text'] ?? NULL,
                'postFile' => $post['post_file'] ?? NULL,
                'postType' => $post['post_type'] ?? NULL,
                'time' => $post['time'] ?? NULL,
                'status' => 200
            ];
        } else { return ['status' => 404]; }
    } catch(PDOException $e) { return ['status' => 500]; } finally { $db = null; }
}
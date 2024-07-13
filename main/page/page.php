<?php 
$params = isset($_GET['params']) ? htmlspecialchars($_GET['params']) : null; 
$slug = isset($_GET['slug']) ? htmlspecialchars($_GET['slug']) : null; 

$element = null; $teamplate = null; 

if(empty($params) && empty($slug)) { header('Location: /not-found'); exit(); } 

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use Phpfastcache\Helper\Psr16Adapter;

$Psr16Adapter = new Psr16Adapter('Files');

require_once $_SERVER['DOCUMENT_ROOT'].'/src/config.php'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/format_date_time.php'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/manipulate_the_element.php';

if (!$Psr16Adapter -> has($slug)) {
    require_once $_SERVER['DOCUMENT_ROOT'].'/src/post/get_post.php'; 
    
    $element = getPost(['params' => $params, 'slug' => $slug]);

    if (!$element['status'] || $element['status'] !== 200) { header('Location: /not-found'); exit(); } else { $Psr16Adapter -> set($slug, $element, 120); }
} else { $element = $Psr16Adapter -> get($slug); }

if (isset($element['time'])) { $element['time'] = formatDateTime($element['time'], 'tr'); }

$teamplate = $_SERVER['DOCUMENT_ROOT'].'/inc/post/'.$params.'.phtml';

if (!file_exists($teamplate)) { header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/head/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/system/errorHandler.php'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/header/content.phtml';
require_once $teamplate;
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/footer/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/js/content.phtml';
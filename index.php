<?php
/**
 *
 * @package Misy
 * @author Misy
 * @link https://misy.dev
 * @copyright Copyright (c) 2024, Misy
 * @license http://opensource.org/licenses/MIT MIT License
*/

if ($_SERVER['REQUEST_URI'] != '/') { header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/modules/config.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/head/content.phtml';

require_once $_SERVER['DOCUMENT_ROOT'].'/src/system/errorHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php';

//if (checkModuleStatus('indexBase') === false) {
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/header/content.phtml'; 
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/index_base/content.phtml';
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/footer/content.phtml';
//} else {  }

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/js/content.phtml';
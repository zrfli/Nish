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

require_once 'src/modules/config.php';

require_once 'inc/head/content.phtml';

require_once 'src/system/errorHandler.php';
require_once 'src/functions/includeAsset.php';

//if (checkModuleStatus('indexBase') === false) {
    require_once 'inc/layouts/header/content.phtml'; 
    require_once 'inc/index_base/content.phtml';
    require_once 'inc/layouts/footer/content.phtml';
//} else {  }

require_once 'inc/js/content.phtml';
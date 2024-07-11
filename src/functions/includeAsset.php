<?php
if ($_SERVER['REQUEST_URI'] == '/src/functions/include_asset.php') { header("Location: /"); exit(); }

$assetsList = ['logo'];

function IncludeAsset($key = null) {
    global $assetsList;

    try {
        if (!in_array($key, $assetsList)) { 
            return 'Error: Asset key is not correct!'; 
        } else {
            if ($key == 'logo') {
                if (file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/static/img/logo.svg')) {
                    return file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/static/img/logo.svg');   
                } else { return 'Error: Assets not found.'; }
            }
        }
    } catch (\Throwable $th) { return 'Unknown error!'; }
}

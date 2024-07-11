<?php if($_SERVER['REQUEST_URI'] != '/flappy'){ header('Location: /not-found'); exit(); } require_once $_SERVER['DOCUMENT_ROOT'].'/inc/head/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/header/content.phtml'; ?>
<main class="mt-20 min-h-96">
    <div id="crispy-bird" class="w-full"></div>
</main>
<script src="assets/game/flappy/js/libs.min.js"></script>
<script src="assets/game/flappy/js/game.min.js"></script>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/footer/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/js/content.phtml'; ?>
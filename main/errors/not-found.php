<?php if($_SERVER['REQUEST_URI'] != '/not-found'){ header('Location: /not-found'); exit(); } require_once $_SERVER['DOCUMENT_ROOT'].'/inc/head/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/header/content.phtml'; ?> 
<main>
    <section class="flex relative z-20 min-h-64 justify-center px-4 mx-auto max-w-screen-xl">
        <div class="flex items-center h-screen space-x-4 dark:text-white">
            <h1 class="text-3xl font-medium">404</h1>
            <hr class="border-r border-gray-300 dark:border-neutral-700 h-10" />
            <h2>This page could not be found.</h2>
        </div>
    </section>
</main>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/footer/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/js/content.phtml'; ?>
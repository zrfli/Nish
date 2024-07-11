<?php if($_SERVER['REQUEST_URI'] != '/internal-server-error'){ header('Location: /not-found'); exit(); } require_once '../../inc/head/content.phtml'; require_once '../../inc/layouts/header/content.phtml'; ?> 
<main>
    <section class="flex relative z-20 min-h-64 justify-center px-4 mx-auto max-w-screen-xl">
        <div class="flex items-center h-screen space-x-4 dark:text-white">
            <h1 class="text-3xl font-medium">500</h1>
            <hr class="border-r border-gray-300 dark:border-gray-700 h-10" />
            <h2>Internal server error.</h2>
        </div>
    </section>
</main>
<?php require_once '../../inc/layouts/footer/content.phtml'; require_once '../../inc/js/content.phtml'; ?>
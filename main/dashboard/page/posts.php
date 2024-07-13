<?php if($_SERVER['REQUEST_URI'] != '/dashboard/posts') { header('Location: /not-found'); exit(); } require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/db.php'; if (!$auth -> isLoggedIn()) { header('Location: /login'); exit(); } require_once '../inc/head/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php'; require_once '../inc/header/content.phtml'; require_once '../inc/sidebar/content.phtml'; ?>
<div class="sm:ml-64 mt-28">
    <div class="flex items-center justify-between ml-4">
        <div>
            <h3 class="text-xl font-semibold dark:text-white">Gönderiler</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Paylaşılan tüm Gönderiler.</p>
        </div>
        <div>
            <button class="text-white bg-black hover:bg-black/80 font-medium rounded-lg border border-gray-700 text-xs px-5 py-2.5 text-center dark:hover:bg-neutral-800/80 dark:bg-neutral-800 dark:border-neutral-700 mr-4" type="button" onclick="createContentPanel();">İçerik Oluştur</button>
        </div>
    </div>
    <!-- Pages Section -->
    <div id="publishedContent" class="p-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="flex flex-col rounded-xl border bg-white dark:bg-black dark:border-neutral-700 shadow-sm">
            <div class="flex justify-between items-center rounded-t-xl border-b bg-gray-100 border-gray-200 dark:border-neutral-700 dark:bg-neutral-800 px-4 py-3 md:px-5 md:py-4">
                <div class="w-36 h-2 bg-gray-300 dark:bg-neutral-900 rounded-full animate-pulse"></div>
                <div class="w-14 h-2 bg-gray-300 dark:bg-neutral-900 rounded-full animate-pulse"></div>
            </div>
            <div class="p-4">
                <div class="w-36 h-2 bg-gray-300 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                <div class="mt-1 w-28 h-2 bg-gray-300 dark:bg-neutral-800 rounded-full animate-pulse"></div>
            </div>
        </div>
        <div class="flex flex-col rounded-xl border bg-white dark:bg-black dark:border-neutral-700 shadow-sm">
            <div class="flex justify-between items-center rounded-t-xl border-b bg-gray-100 border-gray-200 dark:border-neutral-700 dark:bg-neutral-800 px-4 py-3 md:px-5 md:py-4">
                <div class="w-36 h-2 bg-gray-300 dark:bg-neutral-900 rounded-full animate-pulse"></div>
                <div class="w-14 h-2 bg-gray-300 dark:bg-neutral-900 rounded-full animate-pulse"></div>
            </div>
            <div class="p-4">
                <div class="w-36 h-2 bg-gray-300 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                <div class="mt-1 w-28 h-2 bg-gray-300 dark:bg-neutral-800 rounded-full animate-pulse"></div>
            </div>
        </div>
        <div class="flex flex-col rounded-xl border bg-white dark:bg-black dark:border-neutral-700 shadow-sm">
            <div class="flex justify-between items-center rounded-t-xl border-b bg-gray-100 border-gray-200 dark:border-neutral-700 dark:bg-neutral-800 px-4 py-3 md:px-5 md:py-4">
                <div class="w-36 h-2 bg-gray-300 dark:bg-neutral-900 rounded-full animate-pulse"></div>
                <div class="w-14 h-2 bg-gray-300 dark:bg-neutral-900 rounded-full animate-pulse"></div>
            </div>
            <div class="p-4">
                <div class="w-36 h-2 bg-gray-300 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                <div class="mt-1 w-28 h-2 bg-gray-300 dark:bg-neutral-800 rounded-full animate-pulse"></div>
            </div>
        </div>
    </div>
    <div id="loadMorePostContent" class="flex justify-center mt-5 mb-5">
        <button class="border border-gray-400 dark:border-neutral-700 text-white bg-black hover:bg-black/80 font-medium rounded-lg text-xs w-full max-w-sm py-2.5 text-center dark:hover:bg-neutral-800/80 dark:bg-neutral-800" onclick="loadMorePost();">Daha fazla yükle</button>
    </div>
    <!-- End Pages Section -->
</div>
<?php require_once '../inc/js/content.phtml'; ?>
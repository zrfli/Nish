<?php if($_SERVER['REQUEST_URI'] != '/announcements'){ header('Location: /not-found'); exit(); } require_once $_SERVER['DOCUMENT_ROOT'].'/inc/head/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/header/content.phtml'; ?> 
<main class="mt-32 mb-16 min-h-96">
    <div aria-label="announcements">
        <div class="px-4 mx-auto max-w-screen-xl">
            <div class="flex items-center justify-start mb-6">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl dark:text-white">Duyurular</h1>
            </div>
            <div id="announcementsContent" class="grid gap-8 grid sm:grid-cols-2 lg:grid-cols-3">
                <article class="flex">
                    <div class="mr-4 h-16 w-16 flex-shrink-0 overflow-hidden bg-gray-200 dark:bg-neutral-800 text-white animate-pulse"></div>
                    <div class="space-y-2 w-full">
                        <div class="w-full h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-16 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                    </div>
                </article>
                <article class="flex">
                    <div class="mr-4 h-16 w-16 flex-shrink-0 overflow-hidden bg-gray-200 dark:bg-neutral-800 text-white animate-pulse"></div>
                    <div class="space-y-2 w-full">
                        <div class="w-full h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-16 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                    </div>
                </article>
                <article class="flex">
                    <div class="mr-4 h-16 w-16 flex-shrink-0 overflow-hidden bg-gray-200 dark:bg-neutral-800 text-white animate-pulse"></div>
                    <div class="space-y-2 w-full">
                        <div class="w-full h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-16 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                    </div>
                </article>
                <article class="flex">
                    <div class="mr-4 h-16 w-16 flex-shrink-0 overflow-hidden bg-gray-200 dark:bg-neutral-800 text-white animate-pulse"></div>
                    <div class="space-y-2 w-full">
                        <div class="w-full h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-16 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                    </div>
                </article>
                <article class="flex">
                    <div class="mr-4 h-16 w-16 flex-shrink-0 overflow-hidden bg-gray-200 dark:bg-neutral-800 text-white animate-pulse"></div>
                    <div class="space-y-2 w-full">
                        <div class="w-full h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-16 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                    </div>
                </article>
                <article class="flex">
                    <div class="mr-4 h-16 w-16 flex-shrink-0 overflow-hidden bg-gray-200 dark:bg-neutral-800 text-white animate-pulse"></div>
                    <div class="space-y-2 w-full">
                        <div class="w-full h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-36 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                        <div class="w-16 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                    </div>
                </article>
            </div>
            <div id="loadMorePostContent" class="flex justify-center mt-10">
                <button class="border border-gray-400 dark:border-neutral-700 text-white bg-black hover:bg-black/80 font-medium rounded-lg text-xs w-full max-w-sm py-2.5 text-center dark:hover:bg-neutral-800/80 dark:bg-neutral-800" onclick="loadMorePost();">Daha fazla yükle</button>
            </div>
        </div>
    </div>
</main>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/footer/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/js/content.phtml'; ?>
<?php if($_SERVER['REQUEST_URI'] != '/research'){ header('Location: /not-found'); exit(); } require_once $_SERVER['DOCUMENT_ROOT'].'/inc/head/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/header/content.phtml'; ?>
<main class="mt-32 mb-16 min-h-96">
    <div aria-label="research">
    <div class="px-4 mx-auto max-w-screen-xl">
            <div class="flex items-center justify-start mb-6">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl dark:text-white">Araştırmalar</h1>
            </div>
            <div id="researchContent" class="grid gap-8 grid sm:grid-cols-2 lg:grid-cols-3">
                <article class="relative">
                    <div class="flex justify-end">
                        <div class="mb-5 w-full h-56 bg-gray-200 dark:bg-neutral-800 max-w-lg animate-pulse"></div>
                    </div>
                    <div class="mb-2 w-full h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                    <div class="w-16 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                </article>
                <article class="relative">
                    <div class="flex justify-end">
                        <div class="mb-5 w-full h-56 bg-gray-200 dark:bg-neutral-800 max-w-lg animate-pulse"></div>
                    </div>
                    <div class="mb-2 w-full h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                    <div class="w-16 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                </article>
                <article class="relative">
                    <div class="flex justify-end">
                        <div class="mb-5 w-full h-56 bg-gray-200 dark:bg-neutral-800 max-w-lg animate-pulse"></div>
                    </div>
                    <div class="mb-2 w-full h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                    <div class="w-16 h-2 bg-gray-200 dark:bg-neutral-800 rounded-full animate-pulse"></div>
                </article>
            </div>
            <div id="loadMorePostContent" class="flex justify-center mt-10">
                <button class="border border-gray-400 dark:border-neutral-700 text-white bg-black hover:bg-black/80 font-medium rounded-lg text-xs w-full max-w-sm py-2.5 text-center dark:hover:bg-neutral-800/80 dark:bg-neutral-800" onclick="loadMorePost();">Daha fazla yükle</button>
            </div>
        </div>
    </div>
</main>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/footer/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/js/content.phtml'; ?>
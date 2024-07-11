<?php if($_SERVER['REQUEST_URI'] != '/dashboard/posts') { header('Location: /not-found'); exit(); } require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/db.php'; if (!$auth -> isLoggedIn()) { header('Location: /login'); exit(); } require_once '../inc/head/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php'; require_once '../inc/header/content.phtml'; require_once '../inc/sidebar/content.phtml'; ?>
<div class="sm:ml-64 mt-28">
    <div class="flex items-center justify-between ml-4">
        <div>
            <h3 class="text-xl font-semibold dark:text-white">Gönderiler</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Paylaşılan tüm Gönderiler.</p>
        </div>
        <div>
            <button class="text-white bg-black hover:bg-black/80 font-medium rounded-lg border border-gray-700 text-xs px-5 py-2.5 text-center dark:hover:bg-neutral-800/80 dark:bg-neutral-800 dark:border-neutral-700 mr-4" type="button" onclick="createPost();">Gönderi Oluştur</button>
        </div>
    </div>
    <!-- Pages Section -->
    <div id="pagesContent">
        <div class="p-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="flex flex-col rounded-xl bg-white dark:bg-black dark:border-neutral-700 shadow-sm border">
                    <div class="flex justify-between items-center rounded-t-xl border-b bg-gray-100 border-gray-200 dark:border-neutral-700 dark:bg-neutral-800 px-3 py-3">
                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-300">#125</span>
                    <span class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium border border-purple-500 bg-purple-100 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 text-purple-800 rounded">Dynamic</span>
                </div>
                <div class="p-3">
                    <div class="flex justify-center text-center flex-col items-center">
                        <span class="font-semibold text-sm text-gray-800 dark:text-white">Nişantaşı Eğitim Vakfı Kurucusu Sayın Levent Uysal’ın Mesajı</span>
                        <p class="mt-1 text-gray-800 text-xs dark:text-gray-400">nisantasi-egitim-vakfi-kurucunun-mesaji-341232</p>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span lessonCode="'.strtoupper($x['lesson_code']).'" name="'.$x['lesson_name'].'" academician="'.$auth -> misyTag() . ' ' . $auth -> misyFullName().'" class="mt-1 inline-flex cursor-pointer items-center gap-x-1 rounded-lg border border-transparent text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-600 disabled:pointer-events-none disabled:opacity-50" onclick="getAttendanceList(this);">Sayfa Detayları ></span>
                        <span lessonCode="'.strtoupper($x['lesson_code']).'" name="'.$x['lesson_name'].'" academician="'.$auth -> misyTag() . ' ' . $auth -> misyFullName().'" class="mt-1 inline-flex cursor-pointer items-center gap-x-1 rounded-lg border border-transparent text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-600 disabled:pointer-events-none disabled:opacity-50" onclick="getAttendanceList(this);">Sayfayı Düzenle ></span>
                        <span lessonCode="'.strtoupper($x['lesson_code']).'" name="'.$x['lesson_name'].'" academician="'.$auth -> misyTag() . ' ' . $auth -> misyFullName().'" class="mt-1 inline-flex cursor-pointer items-center gap-x-1 rounded-lg border border-transparent text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-600 disabled:pointer-events-none disabled:opacity-50" onclick="getLessonDetails(this);">Sayfayı Gör ></span>
                    </div>
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
    </div>
    <!-- End Pages Section -->
</div>
<?php require_once '../inc/js/content.phtml'; ?>
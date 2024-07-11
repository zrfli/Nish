<?php if($_SERVER['REQUEST_URI'] != '/login'){ header('Location: /not-found'); exit(); } require_once '../../src/database/db.php'; if($auth -> isLoggedIn()){ header('Location: /dashboard'); exit(); } require_once '../../inc/head/content.phtml'; require_once '../../inc/layouts/header/content.phtml'; ?> 
<main>
    <div class="flex relative z-20 min-h-64 justify-center px-4 mx-auto max-w-screen-xl">
        <div class="flex items-center h-screen space-x-4 dark:text-white">
            <form class="w-full max-w-md space-y-4 md:space-y-6 xl:max-w-xl">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl dark:text-white">Log in to Nişantaşı</h1>
                <div id="error" class="mt-2" style="display: none;"></div>
                <div>
                    <label for="username" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Username</label>
                    <input type="text" name="username" id="username" class="shadow-sm bg-gray-50 focus:ring-0 focus:border-black border border-gray-300 dark:border-neutral-700 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" placeholder="hi@nisantasi.com" autocomplete="on" maxlength="64" required>              
                </div>
                <div>
                    <label for="password" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Password</label>
                    <input type="password" name="password" id="password" class="shadow-sm bg-gray-50 focus:ring-0 focus:border-black border border-gray-300 dark:border-neutral-700 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" placeholder="********" autocomplete="on" maxlength="64" required>                
                </div>
                <div class="flex items-center justify-end">
                    <span data-modal-target="password-recovery-modal" data-modal-toggle="password-recovery-modal" class="text-gray-900 dark:text-gray-300 text-xs font-medium hover:underline cursor-pointer">Şifrenizi mi unuttunuz?</span>
                </div>
                <button id="authBtn" class="flex justify-center items-center text-white bg-black hover:bg-black/80 dark:hover:bg-neutral-800/80 border-gray-700 dark:bg-neutral-800 border dark:border-neutral-700 font-medium rounded-lg text-xs w-full px-5 py-2.5" type="submit" onclick="Auth();">Giriş yap</button>
                <div class="flex justify-center pt-5 text-center">
                    <span class="text-xs font-medium text-gray-900 dark:text-gray-300">© 2024 İstanbul Nişantaşı Üniversitesi Bilgi İşlem Daire Başkanlığı - Tüm Hakları Saklıdır.</span>
                </div>
            </form>
        </div>
    </div>
    <div id="password-recovery-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Şifre Yenileme</h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="password-recovery-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <div class="space-y-4">
                        <div id="passwordRecoveryContent">
                            <label for="password_recovery_user_number" class="block mb-2 text-sm font-medium text-gray-900">Öğrenci / Sicil Numarası</label>
                            <input type="text" id="password_recovery_user_number" class="focus:ring-0 focus:border-black block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-xs font-medium text-gray-900" placeholder="••••••••••••••••" maxlength="16" required />
                            <label for="password_recovery_idenity_number" class="block mb-2 text-sm font-medium text-gray-900">T.C. Kimlik No</label>
                            <input type="text" id="password_recovery_idenity_number" class="focus:ring-0 focus:border-black block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-xs font-medium text-gray-900" placeholder="••••••••••••••••" maxlength="11" required />
                            <label for="password_recovery_birthday" class="block mb-2 text-sm font-medium text-gray-900">Doğum Tarihi</label>
                            <input type="date" id="password_recovery_birthday" class="focus:ring-0 focus:border-black block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-xs font-medium text-gray-900" required />
                        </div>
                        <div id="passwordRecoveryMessage" class="text-red-400 text-sm" style="display: none;"></div>
                        <button id="passwordRecoveryBtn" itype="button" class="flex w-full rounded-lg bg-black px-5 py-2.5 items-center justify-center text-sm font-medium text-white hover:bg-black/80" onclick="passwordRecovery();">Gönder</button>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</main>
<?php require_once '../../inc/layouts/footer/content.phtml'; require_once '../../inc/js/content.phtml'; ?>
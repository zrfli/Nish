<?php if($_SERVER['REQUEST_URI'] != '/dashboard/settings') { header('Location: /not-found'); exit(); } require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/db.php'; if (!$auth -> isLoggedIn()) { header('Location: /login'); exit(); } require_once '../inc/head/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php'; require_once '../inc/header/content.phtml'; require_once '../inc/sidebar/content.phtml'; ?>
<div class="sm:ml-64 mt-28">
    <div class="mb-4 col-span-full ml-4 xl:mb-2">
        <h1 class="font-semibold text-gray-900 text-xl dark:text-white">Ayarlar</h1>
    </div>
    <div class="grid grid-cols-1 pt-2 px-4 xl:grid-cols-3 xl:gap-4">
        <!-- Right Content -->
        <div class="col-span-full xl:col-auto">
            <div class="p-4 mb-4 bg-white dark:bg-black border border-gray-200 dark:border-neutral-700 rounded-lg shadow-sm">
                <div class="flex space-x-4">
                    <img class="rounded w-28 h-28 dark:border-neutral-700 border" src="<?= $auth -> misyAvatar() ?? ''; ?>" alt="" loading="lazy" decoding="async" />
                    <div>
                        <h3 class="mb-1 text-base font-semibold dark:text-white">Fotoğraf</h3>
                        <div id="pictureStatus" class="font-medium">
                            <div class="mb-2 w-48 h-2 bg-gray-300 rounded-full animate-pulse"></div>
                            <div class="w-24 h-2 bg-gray-300 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 mb-4 bg-white border border-gray-200 dark:border-neutral-700 dark:bg-black rounded-lg shadow-sm">
                <h3 class="mb-2 text-base font-semibold dark:text-white">İki Faktörlü Kimlik Doğrulama</h3>
                <button id="2faAuthenticationBtn" class="text-white <?php if($auth -> misy2faStatus() == 1){ echo 'bg-red-500'; } else { echo 'bg-black hover:bg-black/80'; } ?> dark:hover:bg-neutral-800/80 dark:bg-neutral-800 font-medium rounded-lg text-xs w-full px-5 py-2.5 text-center" type="button" onclick="twofaAuthentication();"><?= $auth -> misy2faStatus() ? 'Devre dışı bırak' : 'Kurulum yap'; ?></button>
            </div>
            <div class="p-4 mb-4 bg-white border border-gray-200 dark:bg-black dark:border-neutral-700 rounded-lg shadow-sm">
                <h3 class="mb-4 text-base font-semibold dark:text-white">Sicil Bilgileri</h3>
                <div class="mb-2">
                    <label for="unit" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Birim</label>
                    <input type="text" name="unit" id="unit" class="shadow-sm bg-gray-100 border border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" value="<?= $auth -> misyTag() ?? ''; ?>" disabled>
                </div>
                <div class="mb-2">
                    <label for="username" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Sicil Numarası</label>
                    <input type="text" name="username" id="username" class="shadow-sm bg-gray-100 border border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.55" value="<?= $auth -> misyUsername() ?? ''; ?>" autocomplete="off" disabled>
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div class="p-4 mb-4 bg-white border border-gray-200 dark:border-neutral-700 dark:bg-black rounded-lg shadow-sm">
                <h3 class="mb-4 text-base font-semibold dark:text-white">Genel Bilgiler</h3>
                <div class="grid grid-cols-6 gap-4">
                    <div class="col-span-6 sm:col-span-3">
                        <label for="fullName" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Ad Soyad</label>
                        <input type="text" name="fullName" id="fullName" class="shadow-sm bg-gray-100 border border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" value="<?= $auth -> misyFullName() ?? ''; ?>" disabled>
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="idenityNumber" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">T.C. Kimlik No</label>
                        <input type="text" name="idenityNumber" id="idenityNumber" class="shadow-sm bg-gray-100 border border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" value="<?= $auth -> misyIdenityNumber() ?? ''; ?>" disabled>
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="birthDay" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Doğum Tarihi</label>
                        <input type="text" name="birthDay" id="birthDay" class="shadow-sm bg-gray-100 border border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" value="<?= $auth -> misyBirthDay() ?? ''; ?>" disabled>
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="gender" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Cinsiyet</label>
                        <input type="text" name="gender" id="gender" class="shadow-sm bg-gray-100 border border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" value="<?= $auth -> misyGender() ?? ''; ?>" disabled>
                    </div>
                    <div class="col-span-6">
                        <label for="address" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Address</label>
                        <input type="text" name="address" id="address" class="shadow-sm focus:ring-0 focus:border-black border border-gray-300 dark:border-neutral-700 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" value="<?= $auth -> misyAddress() ?? ''; ?>" autocomplete="on" maxlength="100">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="email" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Email</label>
                        <input type="email" name="email" id="email" class="shadow-sm focus:ring-0 focus:border-black border border-gray-300 dark:border-neutral-700 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" value="<?= $auth -> misyEmail() ?? ''; ?>" autocomplete="on" maxlength="64">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="phoneNumber" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Telefon Numarası</label>
                        <input type="number" name="phoneNumber" id="phoneNumber" class="shadow-sm border focus:ring-0 focus:border-black border-gray-300 dark:border-neutral-700 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" value="<?= $auth -> misyPhoneNumber() ?? ''; ?>" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length == 11) return false;">
                    </div>
                    <div class="col-span-6 sm:col-full">
                        <button class="text-white bg-black hover:bg-black/80 font-medium rounded-lg text-xs px-5 py-2.5 text-center dark:hover:bg-neutral-800/80 dark:bg-neutral-800" type="button" onclick="updateInformation();">Kaydet</button>
                    </div>
                </div>
            </div>
            <div class="p-4 mb-4 bg-white border border-gray-200 dark:border-neutral-700 dark:bg-black rounded-lg shadow-sm 2xl:col-span-2">
                <h3 class="mb-4 text-base font-semibold dark:text-white">Parola Güncelleme</h3>
                <div class="grid grid-cols-6 gap-4">
                    <div class="col-span-6 sm:col-span-3">
                        <label for="password" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Parola</label>
                        <input type="password" name="password" id="password" class="shadow-sm focus:ring-0 focus:border-black border border-gray-300 dark:border-neutral-700 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" placeholder="••••••••" required="">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="rePassword" class="block mb-1 text-xs font-medium text-gray-900 dark:text-gray-300">Parola Tekrar</label>
                        <input type="password" name="rePassword" id="rePassword" class="shadow-sm focus:ring-0 focus:border-black border border-gray-300 dark:border-neutral-700 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" placeholder="••••••••" required="">
                    </div>
                    <div class="col-span-6 sm:col-full">
                        <button class="text-white bg-black hover:bg-black/80 font-medium rounded-lg text-xs px-5 py-2.5 text-center dark:hover:bg-neutral-800/80 dark:bg-neutral-800" type="button" onclick="changePassword();">Kaydet</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../inc/js/content.phtml'; ?>
<?php if($_SERVER['REQUEST_URI'] != '/aday') { header('Location: /not-found'); exit(); } require_once $_SERVER['DOCUMENT_ROOT'].'/inc/head/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/src/functions/includeAsset.php'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/header/content.phtml'; ?>
<main class="mt-32 mb-16 min-h-96">
    <div class="mx-auto max-w-screen-xl p-4">
        <div class="mb-8 grid items-center gap-8 lg:mb-16 lg:grid-cols-9 lg:gap-9">
            <div class="col-span-1 lg:col-span-6 text-center sm:mb-6 lg:mb-0 lg:text-left">
                <div class="mb-6 text-sm text-black dark:text-white">
                    <span class="text-sm font-medium">Aday bilgi formu</span>
                </div>
                <h1 class="mb-6 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl xl:text-6xl dark:text-white">Formu gönder + %5 burs kazan</h1>
                <div class="mx-auto max-w-xl lg:ml-0" id="applicationForm">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="relative z-0 w-full group">
                            <label for="fullName" class="block mb-1 text-xs font-medium text-black dark:text-white">Ad Soyad</label>
                            <input type="text" name="fullName" id="fullName" class="shadow-sm bg-gray-50 focus:ring-0 focus:border-black border border-gray-300 dark:border-gray-600 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" placeholder="Nişantaşı Üniversitesi" autocomplete="on" maxlength="100" required>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="phoneNumber" class="block mb-1 text-xs font-medium text-black dark:text-white">Telefon Numarası</label>
                            <input type="number" name="phoneNumber" id="phoneNumber" class="shadow-sm bg-gray-50 border focus:ring-0 focus:border-black border-gray-300 dark:border-gray-600 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" placeholder="0511 111 11 11" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length == 11) return false;" required>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="email" class="block mb-1 text-xs font-medium text-black dark:text-white">E-Posta</label>
                            <input type="email" name="email" id="email" class="shadow-sm bg-gray-50 focus:ring-0 focus:border-black border border-gray-300 dark:border-gray-600 dark:bg-black dark:text-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" placeholder="info@nisantasi.edu.tr" autocomplete="on" maxlength="64" required>
                        </div>
                        <div class="relative z-0 w-full group">
                            <label for="grade" class="block mb-1 text-xs font-medium text-black dark:text-white">Sınıf</label>
                            <select id="grade" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-xs text-gray-900 focus:outline-none focus:ring-0 focus:border-black border-gray-300 dark:border-gray-600 dark:bg-black dark:text-gray-300 font-medium" required>
                                <option>--</option>
                                <option value="9">9. Sınıf</option>
                                <option value="10">10. Sınıf</option>
                                <option value="11">11. Sınıf</option>
                                <option value="12">12. Sınıf</option>
                                <option value="99">Mezun</option>
                            </select>
                        </div>
                    </div>
                    <div class="relative w-full mt-4 group">
                        <label for="programList" class="block mb-1 text-xs font-medium text-black dark:text-white">Hedef Bölüm</label>
                        <div id="programSection">
                            <select id="programList" multiple data-hs-select='{
                                "placeholder": "--",
                                "toggleTag": "<button type=\"button\"></button>",
                                "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative flex text-nowrap w-full cursor-pointer bg-gray-50 border border-gray-300 rounded-lg text-start text-xs focus:border-black focus:ring-0 dark:bg-black dark:border-gray-700 dark:text-gray-300",
                                "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-gray-50 border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-200 [&::-webkit-scrollbar-thumb]:bg-gray-400 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-black dark:border-neutral-700",
                                "optionClasses": "border-b py-2 px-4 w-full text-xs text-gray-800 cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:bg-black dark:hover:bg-neutral-800 dark:text-white dark:focus:bg-neutral-800",
                                "mode": "tags",
                                "isAddTagOnEnter" : false,
                                "wrapperClasses": "relative ps-0.5 pe-9 bg-gray-50 flex items-center flex-wrap text-nowrap w-full border border-gray-300 rounded-lg text-start text-xs font-medium focus:border-black focus:ring-0 dark:bg-black dark:border-gray-700 dark:text-gray-300",
                                "tagsItemTemplate": "<div class=\"flex flex-nowrap items-center relative z-10 bg-white border border-gray-300 rounded-md p-1 m-1 dark:bg-black dark:border-gray-700\"><div class=\"me-1\" data-icon></div><div class=\"whitespace-nowrap dark:text-gray-300\" data-title></div><div class=\"inline-flex flex-shrink-0 justify-center items-center size-5 ms-2 rounded-full text-gray-800 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-0 text-xs dark:bg-neutral-700/50 dark:hover:bg-neutral-700 dark:text-neutral-400 cursor-pointer\" data-remove><svg class=\"flex-shrink-0 size-3\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M18 6 6 18\"/><path d=\"m6 6 12 12\"/></svg></div></div>",
                                "tagsInputClasses": "py-3 px-2 rounded-lg order-1 text-xs outline-none dark:bg-black dark:text-gray-300 bg-gray-50",
                                "optionTemplate": "<div class=\"flex items-center\"><div class=\"size-8 me-2\" data-icon></div><div><div class=\"text-xs font-semibold text-gray-800 dark:text-neutral-200\" data-title></div><div class=\"text-xs text-gray-500 dark:text-neutral-500\" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"flex-shrink-0 size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
                                "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"flex-shrink-0 size-3.5 text-gray-500 dark:text-neutral-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                }' class="hidden">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mx-auto flex flex-col items-center">
                <img src="assets/static/img/0190973b-bdc3-7920-8cc6-841062aa338f.webp" class="max-w-xs h-auto" alt="" loading="lazy" decoding="async" />
                <img src="assets/static/img/f1f6c982-af7f-4c46-928c-e33ef89e6b96.webp" class="max-w-xs h-auto mt-4" alt="" loading="lazy" decoding="async" />
            </div>
        </div>

        <section class="mb-16">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl dark:text-white mb-2">Akademik Birimlerimiz</h1>
                <span class="text-gray-600 dark:text-gray-400 text-sm">Fakülteler, Yüksekokullar, Enstitü, Meslek Yüksekokulları ve Hazırlık Okulumuzdan size kısaca bahsedelim.</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <a href="#" class="p-8 text-left h-96 bg-no-repeat bg-cover bg-center bg-gray-400 bg-blend-multiply hover:bg-blend-normal tracking-tight leading-tight" style="background-image: url('assets/static/img/page/edc96852-1f27-4dbb-abb9-c8c1ca998f6f.webp');">
                    <h2 class="mb-2 max-w-xl text-4xl font-extrabold text-white">Tıp Fakültesi</h2>
                    <span class="text-gray-300">Tıp Fakültesi</span>
                    <p class="text-white text-sm mt-4 underline font-medium underline">Devamı</p>
                </a>
                <a href="#" class="p-8 text-left h-96 bg-no-repeat bg-cover bg-center bg-gray-400 bg-blend-multiply hover:bg-blend-normal tracking-tight leading-tight" style="background-image: url('assets/static/img/page/71790205-ef87-4e58-b5f3-40ccc2949186.webp');">
                    <h2 class="mb-2 max-w-xl text-4xl font-extrabold text-white">Diş Hekimliği Fakültesi</h2>
                    <span class="text-gray-300">Diş Hekimliği Fakültesi</span>
                    <p class="text-white text-sm mt-4 underline font-medium underline">Devamı</p>
                </a>
                <a href="#" class="p-8 text-left h-96 bg-no-repeat bg-cover bg-center bg-gray-400 bg-blend-multiply hover:bg-blend-normal tracking-tight leading-tight" style="background-image: url('assets/static/img/page/45756b87-55de-4901-a36c-d0c646c0ae6f.webp');">
                    <h2 class="mb-2 max-w-xl text-4xl font-extrabold text-white">Fakülteler</h2>
                    <span class="text-gray-300">İktisadi, İdari ve Sosyal Bilimler, Mühendislik Mimarlık, Sağlık Bilimleri, Sanat ve Tasarım Fakültesi</span>
                    <p class="text-white text-sm mt-4 underline font-medium underline">Devamı</p>
                </a>
                <a href="#" class="p-8 text-left h-96 bg-no-repeat bg-cover bg-center bg-gray-400 bg-blend-multiply hover:bg-blend-normal tracking-tight leading-tight" style="background-image: url('assets/static/img/page/01d8eaa5-7ede-4c04-8cc9-79d264b96928.webp');">
                    <h2 class="mb-2 max-w-xl text-4xl font-extrabold text-white">Yüksekokullar</h2>
                    <span class="text-gray-300">Beden Eğitimi ve Spor, Sivil Havacılık ve Uygulamalı Bilimler Yüksekokulu</span>
                    <p class="text-white text-sm mt-4 underline font-medium underline">Devamı</p>
                </a>
                <a href="#" class="p-8 text-left h-96 bg-no-repeat bg-cover bg-center bg-gray-400 bg-blend-multiply hover:bg-blend-normal tracking-tight leading-tight" style="background-image: url('assets/static/img/page/89500bce-0052-4742-a9e2-9af8e4486c17.webp');">
                    <h2 class="mb-2 max-w-xl text-4xl font-extrabold text-white">Meslek Yüksekokulu</h2>
                    <span class="text-gray-300">Etkinlik ve verimlilik odaklı yapılan bu planlamalar, mezunlarımızın okurken iş hayatını tanımlarına yardımcı olmaktadır.</span>
                    <p class="text-white text-sm mt-4 underline font-medium underline">Devamı</p>
                </a>
                <a href="#" class="p-8 text-left h-96 bg-no-repeat bg-cover bg-center bg-gray-400 bg-blend-multiply hover:bg-blend-normal tracking-tight leading-tight" style="background-image: url('assets/static/img/page/3933ac93-7d93-44c7-baf0-2c187f1ad14b.webp');">
                    <h2 class="mb-2 max-w-xl text-4xl font-extrabold text-white">Enstitü</h2>
                    <span class="text-gray-300">Lisansüstü Eğitim Enstitüsü</span>
                    <p class="text-white text-sm mt-4 underline font-medium underline">Devamı</p>
                </a>
            </div>
        </section>

        <section class="grid gap-8 md:grid-cols-3">
            <div class="flex justify-center">
                <svg class="fill-black dark:fill-white mr-3 h-6 w-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0 0 2v8a2 2 0 0 0 2 2h2.586l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 15.414l2.293 2.293a1 1 0 0 0 1.414-1.414L12.414 15H15a2 2 0 0 0 2-2V5a1 1 0 1 0 0-2zm11 4a1 1 0 1 0-2 0v4a1 1 0 1 0 2 0zm-3 1a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0zM8 9a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0z" clip-rule="evenodd"/></svg>          
                <div>
                    <h3 class="mb-1 text-lg font-semibold text-black dark:text-white line-clamp-2">Neden İstanbul Nişantaşı Üniversitesi?</h3>
                    <p class="font-light text-sm line-clamp-2 dark:text-gray-400">(2024 Proje Yılı) Erasmus İngilizce Yeterlilik Sınavı sonuçları açıklandı Sonucunuzu görmek için tıklayınız. Sınava itirazınızın olması durumunda Perşembe günü (30.05.2024) saat 17:00’a kadar Yabancı Diller Okulu ile iletişime geçebilirsiniz.</p>       
                    <a href="aday/test1" class="inline-flex items-center text-xs font-medium underline underline-offset-4 text-black dark:text-gray-300 hover:no-underline">Devamını oku</a>   
                </div>
            </div>
            <div class="flex justify-center">
                <svg class="fill-black dark:fill-white mr-3 h-6 w-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0 0 2v8a2 2 0 0 0 2 2h2.586l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 15.414l2.293 2.293a1 1 0 0 0 1.414-1.414L12.414 15H15a2 2 0 0 0 2-2V5a1 1 0 1 0 0-2zm11 4a1 1 0 1 0-2 0v4a1 1 0 1 0 2 0zm-3 1a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0zM8 9a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0z" clip-rule="evenodd"/></svg>          
                <div>
                    <h3 class="mb-1 text-lg font-semibold text-black dark:text-white line-clamp-2">Mavi Diploma Nedir ?</h3>
                    <p class="font-light text-sm line-clamp-2 dark:text-gray-400">(2024 Proje Yılı) Erasmus İngilizce Yeterlilik Sınavı sonuçları açıklandı Sonucunuzu görmek için tıklayınız. Sınava itirazınızın olması durumunda Perşembe günü (30.05.2024) saat 17:00’a kadar Yabancı Diller Okulu ile iletişime geçebilirsiniz.</p>       
                    <a href="aday/test1" class="inline-flex items-center text-xs font-medium underline underline-offset-4 text-black dark:text-gray-300 hover:no-underline">Devamını oku</a>   
                </div>
            </div>
            <div class="flex justify-center">
                <svg class="fill-black dark:fill-white mr-3 h-6 w-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0 0 2v8a2 2 0 0 0 2 2h2.586l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 15.414l2.293 2.293a1 1 0 0 0 1.414-1.414L12.414 15H15a2 2 0 0 0 2-2V5a1 1 0 1 0 0-2zm11 4a1 1 0 1 0-2 0v4a1 1 0 1 0 2 0zm-3 1a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0zM8 9a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0z" clip-rule="evenodd"/></svg>          
                <div>
                    <h3 class="mb-1 text-lg font-semibold text-black dark:text-white line-clamp-2">Özel Yetenek</h3>
                    <p class="font-light text-sm line-clamp-2 dark:text-gray-400">(2024 Proje Yılı) Erasmus İngilizce Yeterlilik Sınavı sonuçları açıklandı Sonucunuzu görmek için tıklayınız. Sınava itirazınızın olması durumunda Perşembe günü (30.05.2024) saat 17:00’a kadar Yabancı Diller Okulu ile iletişime geçebilirsiniz.</p>       
                    <a href="aday/test1" class="inline-flex items-center text-xs font-medium underline underline-offset-4 text-black dark:text-gray-300 hover:no-underline">Devamını oku</a>   
                </div>
            </div>
            <div class="flex justify-center">
                <svg class="fill-black dark:fill-white mr-3 h-6 w-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0 0 2v8a2 2 0 0 0 2 2h2.586l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 15.414l2.293 2.293a1 1 0 0 0 1.414-1.414L12.414 15H15a2 2 0 0 0 2-2V5a1 1 0 1 0 0-2zm11 4a1 1 0 1 0-2 0v4a1 1 0 1 0 2 0zm-3 1a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0zM8 9a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0z" clip-rule="evenodd"/></svg>          
                <div>
                    <h3 class="mb-1 text-lg font-semibold text-black dark:text-white line-clamp-2">Nish Kariyer Envanteri</h3>
                    <p class="font-light text-sm line-clamp-2 dark:text-gray-400">(2024 Proje Yılı) Erasmus İngilizce Yeterlilik Sınavı sonuçları açıklandı Sonucunuzu görmek için tıklayınız. Sınava itirazınızın olması durumunda Perşembe günü (30.05.2024) saat 17:00’a kadar Yabancı Diller Okulu ile iletişime geçebilirsiniz.</p>       
                    <a href="aday/test1" class="inline-flex items-center text-xs font-medium underline underline-offset-4 text-black dark:text-gray-300 hover:no-underline">Devamını oku</a>   
                </div>
            </div>
            <div class="flex justify-center">
                <svg class="fill-black dark:fill-white mr-3 h-6 w-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0 0 2v8a2 2 0 0 0 2 2h2.586l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 15.414l2.293 2.293a1 1 0 0 0 1.414-1.414L12.414 15H15a2 2 0 0 0 2-2V5a1 1 0 1 0 0-2zm11 4a1 1 0 1 0-2 0v4a1 1 0 1 0 2 0zm-3 1a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0zM8 9a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0z" clip-rule="evenodd"/></svg>          
                <div>
                    <h3 class="mb-1 text-lg font-semibold text-black dark:text-white line-clamp-2">Ek Kontenjan ve Puan Tablosu</h3>
                    <p class="font-light text-sm line-clamp-2 dark:text-gray-400">(2024 Proje Yılı) Erasmus İngilizce Yeterlilik Sınavı sonuçları açıklandı Sonucunuzu görmek için tıklayınız. Sınava itirazınızın olması durumunda Perşembe günü (30.05.2024) saat 17:00’a kadar Yabancı Diller Okulu ile iletişime geçebilirsiniz.</p>       
                    <a href="aday/test1" class="inline-flex items-center text-xs font-medium underline underline-offset-4 text-black dark:text-gray-300 hover:no-underline">Devamını oku</a>   
                </div>
            </div>
            <div class="flex justify-center">
                <svg class="fill-black dark:fill-white mr-3 h-6 w-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0 0 2v8a2 2 0 0 0 2 2h2.586l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 15.414l2.293 2.293a1 1 0 0 0 1.414-1.414L12.414 15H15a2 2 0 0 0 2-2V5a1 1 0 1 0 0-2zm11 4a1 1 0 1 0-2 0v4a1 1 0 1 0 2 0zm-3 1a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0zM8 9a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0z" clip-rule="evenodd"/></svg>          
                <div>
                    <h3 class="mb-1 text-lg font-semibold text-black dark:text-white line-clamp-2">360° Sanal tur</h3>
                    <p class="font-light text-sm line-clamp-2 dark:text-gray-400">(2024 Proje Yılı) Erasmus İngilizce Yeterlilik Sınavı sonuçları açıklandı Sonucunuzu görmek için tıklayınız. Sınava itirazınızın olması durumunda Perşembe günü (30.05.2024) saat 17:00’a kadar Yabancı Diller Okulu ile iletişime geçebilirsiniz.</p>       
                    <a href="aday/test1" class="inline-flex items-center text-xs font-medium underline underline-offset-4 text-black dark:text-gray-300 hover:no-underline">Devamını oku</a>   
                </div>
            </div>
            <div class="flex justify-center">
                <svg class="fill-black dark:fill-white mr-3 h-6 w-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0 0 2v8a2 2 0 0 0 2 2h2.586l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 15.414l2.293 2.293a1 1 0 0 0 1.414-1.414L12.414 15H15a2 2 0 0 0 2-2V5a1 1 0 1 0 0-2zm11 4a1 1 0 1 0-2 0v4a1 1 0 1 0 2 0zm-3 1a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0zM8 9a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0z" clip-rule="evenodd"/></svg>          
                <div>
                    <h3 class="mb-1 text-lg font-semibold text-black dark:text-white line-clamp-2">Dikey Geçiş</h3>
                    <p class="font-light text-sm line-clamp-2 dark:text-gray-400">(2024 Proje Yılı) Erasmus İngilizce Yeterlilik Sınavı sonuçları açıklandı Sonucunuzu görmek için tıklayınız. Sınava itirazınızın olması durumunda Perşembe günü (30.05.2024) saat 17:00’a kadar Yabancı Diller Okulu ile iletişime geçebilirsiniz.</p>       
                    <a href="aday/test1" class="inline-flex items-center text-xs font-medium underline underline-offset-4 text-black dark:text-gray-300 hover:no-underline">Devamını oku</a>   
                </div>
            </div>
            <div class="flex justify-center">
                <svg class="fill-black dark:fill-white mr-3 h-6 w-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0 0 2v8a2 2 0 0 0 2 2h2.586l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 15.414l2.293 2.293a1 1 0 0 0 1.414-1.414L12.414 15H15a2 2 0 0 0 2-2V5a1 1 0 1 0 0-2zm11 4a1 1 0 1 0-2 0v4a1 1 0 1 0 2 0zm-3 1a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0zM8 9a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0z" clip-rule="evenodd"/></svg>          
                <div>
                    <h3 class="mb-1 text-lg font-semibold text-black dark:text-white line-clamp-2">Online Deneme Sınavı</h3>
                    <p class="font-light text-sm line-clamp-2 dark:text-gray-400">(2024 Proje Yılı) Erasmus İngilizce Yeterlilik Sınavı sonuçları açıklandı Sonucunuzu görmek için tıklayınız. Sınava itirazınızın olması durumunda Perşembe günü (30.05.2024) saat 17:00’a kadar Yabancı Diller Okulu ile iletişime geçebilirsiniz.</p>       
                    <a href="aday/test1" class="inline-flex items-center text-xs font-medium underline underline-offset-4 text-black dark:text-gray-300 hover:no-underline">Devamını oku</a>   
                </div>
            </div>
            <div class="flex justify-center">
                <svg class="fill-black dark:fill-white mr-3 h-6 w-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0 0 2v8a2 2 0 0 0 2 2h2.586l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 15.414l2.293 2.293a1 1 0 0 0 1.414-1.414L12.414 15H15a2 2 0 0 0 2-2V5a1 1 0 1 0 0-2zm11 4a1 1 0 1 0-2 0v4a1 1 0 1 0 2 0zm-3 1a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0zM8 9a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0z" clip-rule="evenodd"/></svg>          
                <div>
                    <h3 class="mb-1 text-lg font-semibold text-black dark:text-white line-clamp-2">Apply Nişantaşı</h3>
                    <p class="font-light text-sm line-clamp-2 dark:text-gray-400">(2024 Proje Yılı) Erasmus İngilizce Yeterlilik Sınavı sonuçları açıklandı Sonucunuzu görmek için tıklayınız. Sınava itirazınızın olması durumunda Perşembe günü (30.05.2024) saat 17:00’a kadar Yabancı Diller Okulu ile iletişime geçebilirsiniz.</p>       
                    <a href="aday/test1" class="inline-flex items-center text-xs font-medium underline underline-offset-4 text-black dark:text-gray-300 hover:no-underline">Devamını oku</a>   
                </div>
            </div>
        </section>
    </div>
</main>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/inc/layouts/footer/content.phtml'; require_once $_SERVER['DOCUMENT_ROOT'].'/inc/js/content.phtml'; ?>
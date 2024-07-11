<?php if($_SERVER['REQUEST_URI'] != '/maintenance'){ header('Location: /maintenance'); exit(); } require_once '../../inc/head/content.phtml'; ?> 
<main>
    <section class="flex relative z-20 justify-center px-4 mx-auto max-w-4xl">
        <div class="flex flex-col items-center justify-center h-screen space-y-4 dark:text-white">
            <div><?php require_once $_SERVER['DOCUMENT_ROOT'].'/assets/img/logo.svg'; ?></div>
            <h1 class="text-3xl font-medium">Erişim sağlanamıyor!</h1>
            <p>Nişantaşı Üniversitesi web sitemizde gerçekleştirdiğimiz planlı bakım çalışmaları nedeniyle şu anda sitemize erişim sağlayamıyorsunuz. En kısa sürede hizmetlerimize kaldığımız yerden devam edeceğiz. Anlayışınız için teşekkür ederiz..</p>
    </section>
</main>
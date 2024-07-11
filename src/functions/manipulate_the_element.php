<?php
if ($_SERVER['REQUEST_URI'] == '/src/functions/manipulate_the_element.php') { header("Location: /"); exit(); }

function manipulateTheElement($text) {
    if (empty(trim($text))) { return '<p class="text-sm text-red-400 text-center">İşlem sırasında bir hata oluştu. Tekrar deneyiniz!</p>'; }

    $text = preg_replace('/<h1(.*?)>/', '<h1$1 class="font-bold text-2xl">', $text);
    $text = preg_replace('/<h2(.*?)>/', '<h2$1 class="font-semibold text-lg">', $text);
    $text = preg_replace('/<h3(.*?)>/', '<h3$1 class="font-semibold text-base">', $text);
    $text = preg_replace('/<h4(.*?)>/', '<h4$1 class="font-semibold text-sm">', $text);
    $text = preg_replace('/<h5(.*?)>/', '<h5$1 class="font-semibold text-xs">', $text);
    $text = preg_replace('/<h6(.*?)>/', '<h6$1 class="font-semibold text-xs">', $text);

    $text = preg_replace('/<ul(.*?)>/', '<ul$1 class="list-disc list-inside">', $text);
    
    $text = preg_replace('/<table(.*?)>/', '<table$1 class="border border-gray-300 dark:border-neutral-700">', $text);
    $text = preg_replace('/<thead(.*?)>/', '<thead$1 class="bg-gray-50 border-b border-gray-300 dark:border-neutral-700 dark:bg-neutral-800">', $text);
    $text = preg_replace('/<th(.*?)>/', '<th$1 class="font-bold text-sm px-4 py-3 text-start">', $text);
    $text = preg_replace('/<td(.*?)>/', '<td$1 class="p-2 px-4 text-sm whitespace-nowrap">', $text);

    $text = preg_replace_callback(
        '/<tbody[^>]*>(.*?)<\/tbody>/is',
        function ($matches) {
            return preg_replace(
                '/<tr(.*?)>/is',
                '<tr$1 class="bg-white dark:bg-black border-b border-gray-300 dark:border-neutral-700">',
                $matches[0]
            );
        },
        $text
    );

    $text = preg_replace_callback('/<a(.*?)>(.*?)<\/a>/', function ($matches) {
        return '<a' . $matches[1] . ' class="inline-flex text-blue-600 dark:text-blue-300" target="_blank" rel="noopener noreferrer">' . $matches[2] . '<svg width="7" viewBox="0 0 6 6" height="7" aria-hidden="true"><path fill="currentColor" d="m1.252 5.547-.63-.63 3.16-3.161H1.383L1.39.891h3.887v3.89h-.87l.005-2.396z"></path></svg></a>';
    }, $text);

    $text = preg_replace_callback('/<img(.*?)src=["\']([^"\']*)["\'](.*?)\/?>/', function($matches) {
        return '<img' . $matches[1] . ' loading="lazy" decoding="async" alt="" src="' . $matches[2] . '"' . $matches[3] . '>';
    }, $text);

    return $text ?? '<p class="text-sm text-red-400 text-center">İşlem sırasında bir hata oluştu. Tekrar deneyiniz!</p>';
}
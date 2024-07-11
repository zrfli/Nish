<?php
function moduleResponse($error){
    return '<div class="mt-2 flex justify-center p-4 dark:bg-black rounded-lg" role="alert">
                <div class="flex">
                    <div class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-500 dark:bg-blue-900 dark:text-blue-300">
                        <svg class="h-4 w-4 animate-spin" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 1v5h-5M2 19v-5h5m10-4a8 8 0 0 1-14.947 3.97M1 10a8 8 0 0 1 14.947-3.97"/></svg>
                    </div>
                    <div class="ms-2 text-sm font-normal">
                        <span class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Modül Kapalı</span>
                        <div class="mb-2 text-sm font-normal dark:text-gray-400">'.$error.'</div>
                    </div>
                </div>
            </div>';
}

function checkModuleStatus($moduleName) {
    $modulesJsonPath = $_SERVER['DOCUMENT_ROOT'].'/src/modules/modules_config.json';

    if (empty($moduleName)) { return moduleResponse('Error: json key is not correct!'); } 

    if (!file_exists($modulesJsonPath)) { return moduleResponse('Error: File not found.'); }
    
    $jsonString = file_get_contents($modulesJsonPath);
    
    if ($jsonString === false) { return moduleResponse('Error: Unable to read file.'); }
    
    $modules = json_decode($jsonString, true);
    
    if ($modules === null) { return moduleResponse('Error: An error occurred while decoding JSON.'); }
    
    if (array_key_exists($moduleName, $modules)) {
        if ($modules[$moduleName] === true) { return true; } else { return moduleResponse('Modül şu anda kapalı durumda olduğundan, geçici olarak erişim sağlanamıyor.'); }
    } else { return moduleResponse('Error: json key is not correct!'); }
}
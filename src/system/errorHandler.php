<?php
if ($_SERVER['REQUEST_URI'] == '/src/system/errorHandler.php') { header("Location: /"); exit(); }

error_reporting(E_ALL);
ini_set('display_errors', false);

function errorTemplate($errType, $errFile = null, $errLine = null, $errText) {
    if (empty($errType) || empty($errText)) { return false; }

    return ('<main>
                <section class="flex justify-center text-center">
                    <div class="flex flex-col items-center justify-center h-screen space-y-4 dark:text-white">
                        <h1 class="text-3xl font-bold">App Error!</h1>
                        <p class="text-sm"><strong>'.$errType.':</strong> <span class="text-red-400 underline">'.$errFile.':'.$errLine.'</span> ('.$errText.')</p>
                    </div>
                </section>
            </main>
            </body>');
}

function errorHandler($errno, $errstr, $errfile, $errline) {
    if (!error_reporting() || empty($errno)) { return false; }

    $errstr = htmlspecialchars($errstr);

    $out = match ($errno) {
        E_USER_ERROR => errorTemplate('Fatal error on line', $errfile, $errline,$errstr), 
        E_USER_WARNING => errorTemplate('Warning', $errfile, $errline,$errstr), 
        E_USER_NOTICE => errorTemplate('Notice', $errfile, $errline,$errstr),
        default => errorTemplate('Unknown error type', $errfile, $errline,$errstr),
    };

    die($out);  
}

set_error_handler("errorHandler");
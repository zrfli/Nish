<?php
if($_SERVER['REQUEST_URI'] != '/xhr/dashboard/2fa/2fa_authentication'){ header('Location: /'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
//require_once '../../src/logger/logger.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/modules/config.php';

use chillerlan\QRCode\QRCode;
$ga = new PHPGangsta_GoogleAuthenticator();

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = [];

header("Content-type: application/json; charset=utf-8");

try {
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);
	
    if (!$auth -> check()){ $errors['isLogged'] = false; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $data['status'] = 'success'; 
        $data['statusCode'] = 200; 
        $data['token'] = \Delight\Auth\Auth::createRandomString(24); 
        $data['userId'] = \Delight\Auth\Auth::createUuid();

        if (checkModuleStatus('twofa_authentication') === true) {
            $verifyStepJS = '<script>
                                    function focusNextInput(el, prevId, nextId) {
                                        if (el.value.length === 0) {
                                            if (prevId) {
                                                document.getElementById(prevId).focus();
                                            }
                                        } else {
                                            if (nextId) {
                                                document.getElementById(nextId).focus();
                                            }
                                        }
                                    }
                                    
                                    document.querySelectorAll("[data-focus-input-init]").forEach(function(element) {
                                        element.addEventListener("keyup", function() {
                                            const prevId = this.getAttribute("data-focus-input-prev");
                                            const nextId = this.getAttribute("data-focus-input-next");
                                            focusNextInput(this, prevId, nextId);
                                        });
                                    });
                                </script>';
                            
            if ($auth -> misy2faStatus() === 0) {
                $secretKey = $ga -> createSecret();
                $qrElement = $ga -> getQRCodeGoogleUrl('Misy', $secretKey);

                $data['2faSecretKey'] = (string) $secretKey;

                $data['data'] = '<div id="2fasetupStep">
                                    <p class="text-xs text-gray-900 font-medium">İki Faktörlü Kimlik Doğrulama, tek dokunuşla oturum açmaya olanak tanır ve verilerinizi yetkisiz erişime karşı korur.</p>
                                    <p class="text-xs mt-2 text-gray-900 font-medium">Henüz mobil cihazınızda yoksa Google Authenticator uygulamasını mobil cihazınıza yükleyin.</p>
                                    <div class="flex items-center justify-center mt-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <img class="border h-32 w-32" src="'.(new QRCode) -> render("https://apps.apple.com/tr/app/google-authenticator/id388497605").'" alt="" decoding="async" loading="lazy" />
                                                <a href="https://apps.apple.com/tr/app/google-authenticator/id388497605" target="_blank" class="mt-2 flex justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="33.427" viewBox="0 0 100 33.427"><path d="M92.037 0H7.968q-.459 0-.915.002-.382.003-.768.011A11 11 0 0 0 4.61.159a5.6 5.6 0 0 0-1.588.524 5.4 5.4 0 0 0-1.354.986 5.4 5.4 0 0 0-.983 1.353 5.5 5.5 0 0 0-.522 1.59 11 11 0 0 0-.15 1.674c-.008.257-.008.514-.013.77v19.318c.004.259.005.51.013.77a11 11 0 0 0 .15 1.673 5.5 5.5 0 0 0 .521 1.592 5.2 5.2 0 0 0 .986 1.347 5.4 5.4 0 0 0 1.352.985 5.6 5.6 0 0 0 1.589.526 11 11 0 0 0 1.675.148c.259.006.512.009.768.009.306.002.608.002.915.002h84.069l.906-.002c.254 0 .516-.003.77-.008a11 11 0 0 0 1.671-.149 5.7 5.7 0 0 0 1.594-.526 5.4 5.4 0 0 0 1.351-.985 5.4 5.4 0 0 0 .988-1.349 5.5 5.5 0 0 0 .517-1.592 11 11 0 0 0 .155-1.673c.003-.259.003-.51.003-.77q.009-.454.007-.914V7.969q.002-.459-.007-.913.001-.384-.003-.769a11 11 0 0 0-.155-1.674 5.5 5.5 0 0 0-.518-1.59 5.4 5.4 0 0 0-2.338-2.34 5.7 5.7 0 0 0-1.594-.524 11 11 0 0 0-1.671-.147c-.255-.004-.516-.009-.77-.011-.301-.002-.606-.002-.906-.002" style="fill:#a6a6a6"/><path d="M7.057 32.696c-.255 0-.503-.003-.755-.008a11 11 0 0 1-1.563-.137 5 5 0 0 1-1.384-.458 4.5 4.5 0 0 1-1.167-.849 4.5 4.5 0 0 1-.852-1.167 4.8 4.8 0 0 1-.455-1.385 11 11 0 0 1-.139-1.567 62 62 0 0 1-.013-.763V7.058s.008-.578.013-.748a11 11 0 0 1 .139-1.563 5 5 0 0 1 .455-1.389 4.5 4.5 0 0 1 .848-1.168 4.7 4.7 0 0 1 1.172-.855A5 5 0 0 1 4.735.88 11 11 0 0 1 6.303.741l.754-.01h85.881l.763.011a11 11 0 0 1 1.553.135 5 5 0 0 1 1.396.458 4.7 4.7 0 0 1 2.018 2.022 5 5 0 0 1 .447 1.378 11 11 0 0 1 .145 1.577c.003.236.003.491.003.744.007.313.007.612.007.913v17.49q.002.453-.007.898c0 .272 0 .521-.003.777a11 11 0 0 1-.142 1.549 4.8 4.8 0 0 1-.451 1.396 4.6 4.6 0 0 1-.849 1.158 4.5 4.5 0 0 1-1.17.854 5 5 0 0 1-1.394.46 11 11 0 0 1-1.562.136 31 31 0 0 1-.75.009l-.906.002Z"/><g data-name="&amp;amp;amp;lt;Group&amp;amp;amp;gt;"><g data-name="&amp;amp;amp;lt;Group&amp;amp;amp;gt;"><path data-name="&amp;amp;amp;lt;Path&amp;amp;amp;gt;" d="M20.699 16.964a4.14 4.14 0 0 1 1.969-3.469 4.24 4.24 0 0 0-3.334-1.803c-1.404-.147-2.764.84-3.48.84-.729 0-1.83-.826-3.015-.801a4.45 4.45 0 0 0-3.738 2.28c-1.616 2.798-.41 6.91 1.137 9.172.775 1.107 1.68 2.344 2.865 2.301 1.159-.048 1.592-.74 2.992-.74 1.386 0 1.792.74 3 .712 1.244-.021 2.027-1.113 2.774-2.231a9.2 9.2 0 0 0 1.27-2.584 4 4 0 0 1-2.44-3.677m-2.283-6.761a4.07 4.07 0 0 0 .932-2.916 4.14 4.14 0 0 0-2.681 1.387 3.88 3.88 0 0 0-.956 2.808 3.43 3.43 0 0 0 2.705-1.279" style="fill:#fff"/></g><path d="M35.351 22.68h-3.955l-.95 2.805H28.77l3.747-10.377h1.741l3.746 10.377H36.3Zm-3.546-1.295h3.135l-1.547-4.55h-.043Zm14.291.318c0 2.351-1.259 3.861-3.158 3.861a2.57 2.57 0 0 1-2.38-1.323h-.036v3.747h-1.554V17.919h1.504v1.259h.028a2.68 2.68 0 0 1 2.409-1.337c1.92 0 3.186 1.518 3.186 3.862m-1.596 0c0-1.532-.792-2.539-2-2.539-1.187 0-1.985 1.028-1.985 2.539 0 1.524.798 2.545 1.985 2.545 1.208 0 2-1 2-2.545m9.924 0c0 2.351-1.259 3.861-3.158 3.861a2.57 2.57 0 0 1-2.38-1.323h-.036v3.747h-1.554V17.919h1.503v1.259h.028a2.68 2.68 0 0 1 2.409-1.337c1.92 0 3.186 1.518 3.186 3.862m-1.596 0c0-1.532-.792-2.539-2-2.539-1.187 0-1.985 1.028-1.985 2.539 0 1.524.798 2.545 1.985 2.545 1.208 0 1.999-1 1.999-2.545m7.1.891c.115 1.03 1.115 1.705 2.482 1.705 1.309 0 2.25-.675 2.25-1.604 0-.806-.568-1.287-1.914-1.618l-1.345-.324c-1.905-.46-2.79-1.351-2.79-2.798 0-1.79 1.56-3.02 3.776-3.02 2.193 0 3.696 1.23 3.746 3.02h-1.568c-.094-1.035-.949-1.66-2.201-1.66s-2.107.633-2.107 1.553c0 .734.547 1.166 1.884 1.496l1.143.281c2.129.504 3.013 1.359 3.013 2.877 0 1.941-1.546 3.157-4.005 3.157-2.301 0-3.856-1.187-3.956-3.064Zm9.726-6.466v1.79h1.439v1.23h-1.437v4.171c0 .648.288.95.921.95a5 5 0 0 0 .511-.036v1.223a4.2 4.2 0 0 1-.862.072c-1.532 0-2.129-.576-2.129-2.043v-4.336h-1.1v-1.23h1.1v-1.791Zm2.272 5.574c0-2.381 1.402-3.877 3.588-3.877 2.194 0 3.589 1.496 3.589 3.877 0 2.387-1.388 3.876-3.589 3.876s-3.588-1.489-3.588-3.876m5.595 0c0-1.633-.748-2.597-2.006-2.597s-2.006.971-2.006 2.597c0 1.64.747 2.596 2.006 2.596s2.006-.956 2.006-2.596m2.863-3.784h1.482v1.288h.036a1.804 1.804 0 0 1 1.819-1.366 2.5 2.5 0 0 1 .532.058v1.452a2.2 2.2 0 0 0-.698-.094 1.565 1.565 0 0 0-1.619 1.741v4.488h-1.553Zm11.029 5.344c-.209 1.373-1.546 2.316-3.257 2.316-2.201 0-3.567-1.474-3.567-3.84 0-2.373 1.374-3.913 3.501-3.913 2.094 0 3.41 1.437 3.41 3.732v.532h-5.343v.094a1.97 1.97 0 0 0 2.036 2.143 1.71 1.71 0 0 0 1.747-1.064Zm-5.25-2.258h3.782a1.82 1.82 0 0 0-1.855-1.92 1.914 1.914 0 0 0-1.927 1.92" style="fill:#fff"/></g><path d="M31.61 7.296a2.205 2.205 0 0 1 2.347 2.478c0 1.593-.861 2.509-2.347 2.509h-1.801V7.295Zm-1.026 4.281h.94a1.568 1.568 0 0 0 1.644-1.793 1.57 1.57 0 0 0-1.644-1.783h-.94Zm4.247-1.178a1.782 1.782 0 1 1 3.55 0 1.783 1.783 0 1 1-3.549 0m2.786 0c0-.816-.367-1.293-1.009-1.293-.646 0-1.009.477-1.009 1.293 0 .822.363 1.295 1.009 1.295.643 0 1.009-.476 1.009-1.295m5.48 1.884h-.77l-.777-2.772h-.058l-.775 2.772h-.763l-1.038-3.764h.754l.674 2.871h.056l.774-2.871h.712l.774 2.871h.058l.671-2.871h.743Zm1.906-3.763h.715v.598h.055a1.13 1.13 0 0 1 1.123-.67 1.223 1.223 0 0 1 1.303 1.4v2.436h-.743v-2.25c0-.605-.262-.906-.812-.906a.863.863 0 0 0-.898.954v2.202h-.742Zm4.379-1.469h.742v5.231h-.742Zm1.775 3.349a1.782 1.782 0 1 1 3.549 0 1.783 1.783 0 1 1-3.549 0m2.785 0c0-.816-.367-1.293-1.009-1.293-.646 0-1.009.477-1.009 1.293 0 .822.363 1.295 1.009 1.295.643 0 1.009-.476 1.009-1.295m1.545.819c0-.677.505-1.068 1.401-1.123l1.02-.058v-.325c0-.397-.263-.622-.77-.622-.415 0-.702.152-.785.418h-.719c.075-.646.684-1.061 1.538-1.061.943 0 1.475.47 1.475 1.265v2.571h-.715v-.529h-.058a1.27 1.27 0 0 1-1.131.591 1.137 1.137 0 0 1-1.254-1.126m2.419-.321v-.315l-.919.058c-.518.035-.752.211-.752.543 0 .338.293.535.697.535a.884.884 0 0 0 .974-.821m1.715-.498c0-1.189.612-1.942 1.563-1.942a1.24 1.24 0 0 1 1.153.66h.056V7.051h.742v5.231h-.711v-.593h-.058a1.3 1.3 0 0 1-1.182.656c-.957 0-1.562-.753-1.562-1.945m.767 0c0 .798.376 1.279 1.005 1.279.627 0 1.013-.487 1.013-1.275 0-.784-.391-1.279-1.013-1.279-.625 0-1.005.485-1.005 1.275m5.82 0a1.782 1.782 0 1 1 3.549 0 1.783 1.783 0 1 1-3.549 0m2.785 0c0-.816-.366-1.293-1.009-1.293-.645 0-1.009.477-1.009 1.293 0 .822.364 1.295 1.009 1.295.643 0 1.009-.476 1.009-1.295m1.761-1.879h.715v.598h.055a1.13 1.13 0 0 1 1.123-.67 1.223 1.223 0 0 1 1.303 1.4v2.436h-.743v-2.25c0-.605-.262-.906-.812-.906a.863.863 0 0 0-.898.954v2.202h-.743Zm7.392-.937v.954h.816v.626h-.816v1.935c0 .394.162.567.532.567a3 3 0 0 0 .283-.018v.618a3 3 0 0 1-.404.038c-.826 0-1.154-.291-1.154-1.016V9.162h-.598v-.626h.598v-.952Zm1.83-.532h.735v2.073h.058a1.16 1.16 0 0 1 1.148-.674 1.24 1.24 0 0 1 1.295 1.403v2.429h-.743v-2.247c0-.602-.28-.906-.805-.906a.88.88 0 0 0-.948.954v2.198h-.742Zm7.568 4.216a1.53 1.53 0 0 1-1.63 1.089 1.71 1.71 0 0 1-1.739-1.943 1.736 1.736 0 0 1 1.735-1.966c1.047 0 1.679.715 1.679 1.897v.259h-2.657v.042a.994.994 0 0 0 1.003 1.078.9.9 0 0 0 .894-.456Zm-2.612-1.213h1.901a.91.91 0 0 0-.927-.975.96.96 0 0 0-.974.975" style="fill:#fff" data-name="&amp;amp;amp;lt;Group&amp;amp;amp;gt;"/></svg>
                                                </a>
                                            </div>
                                            <div>
                                                <img class="border h-32 w-32" src="'.(new QRCode) -> render("https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2").'" alt="" decoding="async" loading="lazy" />
                                                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="mt-2 flex justify-center">
                                                    <svg width="112" height="33.185" viewBox="0 0 112 33.185" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><path d="M107.85 33.185H4.15a4.16 4.16 0 0 1-4.148-4.148V4.148A4.16 4.16 0 0 1 4.15 0h103.7a4.16 4.16 0 0 1 4.148 4.148v24.889a4.16 4.16 0 0 1-4.148 4.148" fill="#100f0d"/><path d="M107.85.001H4.15A4.16 4.16 0 0 0 .002 4.149v24.888a4.16 4.16 0 0 0 4.148 4.148h103.7a4.16 4.16 0 0 0 4.148-4.148V4.148A4.16 4.16 0 0 0 107.85 0m0 .663a3.49 3.49 0 0 1 3.484 3.484v24.889a3.49 3.49 0 0 1-3.484 3.484H4.15a3.49 3.49 0 0 1-3.484-3.484V4.147A3.49 3.49 0 0 1 4.15.663z" fill="#a2a2a1"/><path d="M88.716 24.889h1.548V14.517h-1.548zm13.943-6.635-1.775 4.496h-.053l-1.842-4.496h-1.668l2.763 6.284-1.575 3.496h1.615l4.256-9.781zm-8.78 5.457c-.506 0-1.213-.253-1.213-.88 0-.8.88-1.108 1.641-1.108.68 0 1.001.147 1.414.347a1.88 1.88 0 0 1-1.842 1.641m.187-5.684c-1.121 0-2.281.494-2.761 1.589l1.374.573c.294-.573.84-.76 1.414-.76.801 0 1.615.479 1.628 1.334v.106c-.28-.16-.881-.4-1.615-.4-1.482 0-2.989.814-2.989 2.335 0 1.388 1.215 2.282 2.575 2.282 1.04 0 1.615-.467 1.974-1.014h.054v.801h1.495v-3.977c0-1.842-1.376-2.868-3.15-2.868m-9.567 1.49h-2.201v-3.555h2.201c1.157 0 1.814.958 1.814 1.777 0 .804-.657 1.778-1.814 1.778m-.04-4.999h-3.709v10.372h1.547v-3.929h2.162c1.716 0 3.402-1.242 3.402-3.221s-1.686-3.221-3.402-3.221zm-20.224 9.194c-1.069 0-1.964-.896-1.964-2.125 0-1.243.895-2.152 1.964-2.152 1.056 0 1.885.908 1.885 2.152 0 1.23-.829 2.125-1.885 2.125m1.778-4.878h-.054c-.347-.414-1.016-.788-1.857-.788-1.765 0-3.382 1.551-3.382 3.542 0 1.978 1.617 3.516 3.382 3.516.841 0 1.51-.375 1.857-.803h.054v.508c0 1.35-.722 2.072-1.885 2.072-.948 0-1.537-.681-1.777-1.256l-1.35.561c.388.935 1.417 2.085 3.128 2.085 1.818 0 3.355-1.07 3.355-3.676v-6.334h-1.471zm2.54 6.054h1.549V14.516h-1.549zm3.835-3.421c-.04-1.363 1.057-2.058 1.845-2.058.615 0 1.136.307 1.31.748zm4.812-1.176c-.294-.789-1.19-2.246-3.02-2.246-1.818 0-3.329 1.431-3.329 3.529 0 1.978 1.498 3.528 3.502 3.528 1.618 0 2.554-.989 2.941-1.564l-1.203-.802c-.401.589-.949.976-1.738.976-.788 0-1.35-.361-1.71-1.07l4.718-1.951zm-37.588-1.164v1.498h3.582c-.107.842-.388 1.456-.815 1.884-.521.521-1.337 1.096-2.766 1.096-2.206 0-3.929-1.778-3.929-3.983s1.724-3.983 3.929-3.983c1.19 0 2.058.468 2.7 1.07l1.056-1.056c-.896-.856-2.085-1.51-3.756-1.51-3.021 0-5.561 2.459-5.561 5.48 0 3.02 2.54 5.479 5.561 5.479 1.63 0 2.86-.534 3.822-1.537.989-.989 1.297-2.379 1.297-3.502a5 5 0 0 0-.081-.935zm9.192 4.585c-1.07 0-1.992-.882-1.992-2.138 0-1.27.922-2.139 1.991-2.139s1.992.869 1.992 2.139c0 1.256-.922 2.138-1.991 2.138m0-5.667c-1.952 0-3.542 1.483-3.542 3.528 0 2.032 1.59 3.529 3.542 3.529s3.542-1.496 3.542-3.528c0-2.045-1.59-3.529-3.542-3.529m7.726 5.667c-1.069 0-1.991-.882-1.991-2.138 0-1.27.922-2.139 1.991-2.139s1.991.869 1.991 2.139c0 1.256-.922 2.138-1.991 2.138m0-5.667c-1.951 0-3.542 1.483-3.542 3.528 0 2.032 1.59 3.529 3.542 3.529s3.542-1.496 3.542-3.528c0-2.045-1.59-3.529-3.542-3.529" fill="#fff"/><path d="m17.187 16.115-8.832 9.374.001.006a2.385 2.385 0 0 0 3.514 1.439l.029-.016 9.942-5.737z" fill="#eb3131"/><path d="m26.123 14.519-.009-.006-4.292-2.488-4.835 4.302 4.853 4.852 4.269-2.464a2.387 2.387 0 0 0 .014-4.197" fill="#f6b60b"/><path d="M8.354 7.697q-.08.294-.081.614v16.565q0 .319.081.613l9.137-9.134z" fill="#5778c5"/><path d="m17.252 16.593 4.571-4.57-9.931-5.758a2.39 2.39 0 0 0-3.539 1.429v.002z" fill="#3bad49"/><path d="M39.32 8.115H36.9v.599h1.814c-.05.488-.244.872-.572 1.151-.328.278-.747.418-1.241.418q-.815-.001-1.381-.565-.555-.573-.557-1.43.002-.854.558-1.43.566-.564 1.381-.564c.279 0 .544.048.788.153q.364.157.592.439l.46-.46a2.05 2.05 0 0 0-.801-.551 2.7 2.7 0 0 0-1.039-.195q-1.088 0-1.841.753-.753.755-.753 1.855c0 .732.251 1.353.753 1.855q.753.753 1.841.753c.761 0 1.368-.244 1.834-.739.411-.412.621-.969.621-1.667q-.002-.177-.035-.376zm.938-2.323v4.993h2.915v-.614h-2.274V8.587h2.051v-.599h-2.051V6.405h2.274v-.612zm7.003.614v-.613h-3.432v.614h1.395v4.379h.642V6.407zm3.106-.614h-.641v4.993h.641zm4.235.614v-.613H51.17v.614h1.395v4.379h.642V6.407zm6.475.035c-.495-.51-1.101-.761-1.827-.761s-1.332.251-1.827.753q-.74.744-.739 1.855c.001 1.111.243 1.36.739 1.855.495.502 1.101.753 1.827.753.719 0 1.332-.251 1.827-.753q.74-.744.739-1.855c0-.732-.243-1.352-.739-1.847zm-3.194.417q.553-.564 1.367-.564t1.36.564c.37.37.551.852.551 1.43 0 .58-.181 1.06-.551 1.43q-.545.564-1.36.565-.815-.001-1.367-.565c-.362-.377-.543-.85-.543-1.43 0-.578.181-1.052.543-1.43zm5.457.817-.027-.963h.027l2.539 4.073h.67V5.792h-.641v2.921l.027.963h-.027l-2.427-3.885h-.782v4.993h.642z" fill="#fff" stroke="#fff" stroke-miterlimit="10" stroke-width=".166"/></svg>                                       
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="2faBuildStep" style="display: none;">
                                    <p class="text-xs text-gray-900 font-medium">Cihazınızda Google Authenticator uygulamasını açın ve yeni bir hesap eklemek için "+" simgesine dokunun. "QR kodunu tara yı seçin.</p>
                                    <div class="flex items-center mt-4 justify-center space-x-5">
                                        <div>
                                            <img class="border h-32 w-32" src="'.(new QRCode) -> render($qrElement).'" alt="" decoding="async" loading="lazy" />
                                        </div>
                                        <div>
                                            <label for="secretKey" class="block mb-1 text-xs font-medium text-gray-900">2FA Gizli Anahtarı</label>
                                            <input type="text" name="secretKey" id="secretKey" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 font-medium text-xs rounded-lg block w-full p-2.5" value="'.$secretKey.'" disabled="">
                                        </div>
                                    </div>
                                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-gray-600 mt-4" role="alert">
                                        <p class="text-xs text-yellow-600 text-center">Gizli anahtarınızı daima güvenli bir yerde saklayın. Hem Authenticator uygulamanıza hem de gizli anahtarınıza erişimi kaybetmek, hesabınızın kilitlenmesine neden olabilir.</p>
                                    </div>
                                </div>
                                <div id="2faVerifyStep" style="display: none;">
                                    <p class="text-xs text-gray-900 font-medium">Authenticator uygulamasında 6 haneli bir kod görünecektir. 2FA yı doğrulamak ve etkinleştirmek için bu kodu girin.</p>
                                    <div class="mx-auto max-w-sm" id="2faAuthCodeContent">
                                        <div class="mb-2 flex items-center justify-center space-x-2 mt-2">
                                            <div>
                                                <label for="code-1" class="sr-only">First code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-next="code-2" id="code-1" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-2" class="sr-only">Second code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-1" data-focus-input-next="code-3" id="code-2" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-3" class="sr-only">Third code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-2" data-focus-input-next="code-4" id="code-3" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-4" class="sr-only">Fourth code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-3" data-focus-input-next="code-5" id="code-4" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-5" class="sr-only">Fifth code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-4" data-focus-input-next="code-6" id="code-5" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-6" class="sr-only">Sixth code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-5" id="code-6" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                        </div>
                                        '.$verifyStepJS.'
                                    </div>
                                </div>
                                <div class="flex justify-end mt-4 gap-3">
                                    <button id="2faAuthenticationBackBtn" type="button" class="text-white flex bg-black hover:bg-black/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onclick="twofaAuthentication(1);" style="display:none;">Geri dön</button>
                                    <button id="2faAuthenticationStepBtn" type="button" class="text-white flex bg-black hover:bg-black/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onclick="twofaAuthentication(0);">Devam et</button>
                                </div>';
            } else if ($auth -> misy2faStatus() === 1) {
                $data['data'] = '<div id="2faVerifyStep">
                                    <p class="text-xs text-gray-900 font-medium">Authenticator uygulamasında 6 haneli bir kod görünecektir. 2FA yi devre dışı bırakmak için bu kodu girin veya 2FA gizli anahtarınızı girin."</p>
                                    <div class="mx-auto max-w-sm" id="2faAuthCodeContent">
                                        <div class="flex items-center justify-center space-x-2 mt-4">
                                            <div>
                                                <label for="code-1" class="sr-only">First code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-next="code-2" id="code-1" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-2" class="sr-only">Second code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-1" data-focus-input-next="code-3" id="code-2" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-3" class="sr-only">Third code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-2" data-focus-input-next="code-4" id="code-3" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-4" class="sr-only">Fourth code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-3" data-focus-input-next="code-5" id="code-4" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-5" class="sr-only">Fifth code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-4" data-focus-input-next="code-6" id="code-5" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                            <div>
                                                <label for="code-6" class="sr-only">Sixth code</label>
                                                <input type="tel" maxlength="1" data-focus-input-init data-focus-input-prev="code-5" id="code-6" class="focus:ring-0 focus:border-black block h-9 w-9 rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-extrabold text-gray-900" required />
                                            </div>
                                        </div>
                                        '.$verifyStepJS.'
                                    </div>
                                </div>
                                <div class="flex justify-end mt-4 gap-3">
                                    <button id="2faAuthenticationStepBtn" type="button" class="text-white flex bg-black hover:bg-black/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onclick="verify2faAuthentication(0);">İptal et</button>
                                </div>';
            }
        } else { $data['data'] = checkModuleStatus('twofa_authentication'); }
    }
} catch (\Throwable $th) {
    //$logger->logError($e, ['details' -> $e->getMessage(), 'user_id' -> $auth -> getUserId()], 'SETUP_2FA_AUTHENTICATION');
    die();
}

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
<?php
if($_SERVER['REQUEST_URI'] != '/xhr/auth/login'){ header('Location: /'); exit(); }

require_once("../../vendor/autoload.php");
include_once('../../src/language/language.php');
require_once '../../src/database/config.php';
//require_once '../../src/logger/logger.php';

if(isset($_COOKIE['lang'])){ require_once('../../src/language/'.strip_tags($_COOKIE['lang']).'.php'); } else { require_once('../../src/language/turkish.php'); }

date_default_timezone_set('Europe/Istanbul');

$ga = new PHPGangsta_GoogleAuthenticator();

$errors = []; $data = [];

$username = null; $password = null; $authCode = null; $authStatus = false;

header("Content-type: application/json; charset=utf-8");

$data_ = json_decode(file_get_contents("php://input"));

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);
	
try {
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
    
    $auth = new \Delight\Auth\Auth($db);
	
    if (isset($data_ -> authUsername)){ $username = htmlspecialchars(trim($data_ -> authUsername)); } else { $errors['authUsername'] = null; }
    if (isset($data_ -> authPassword)){ $password = htmlspecialchars(trim($data_ -> authPassword)); } else { $errors['authPassword'] = null; }
    if (isset($data_ -> authCode)){ $authCode = htmlspecialchars(trim($data_ -> authCode)); }
    if ($auth -> check()){ $errors['isLogged'] = true; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        try {
            if (isset($authCode)) {
                if ($ga -> verifyCode($_SESSION['AUTH-2FacSecretKey'], $authCode, 2)) { 
                    $authStatus = true; 
                    unset($_SESSION['AUTH-2FacSecretKey']);
                } else { $authStatus = false; }
            }

            $auth -> loginWithUsername($username, $password, $authStatus, null);

            $data['status'] = 'success'; $data['statusCode'] = 200; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['userId'] = \Delight\Auth\Auth::createUuid($auth -> getUserId()); $data['message'] = 'success';
            
            //$logger->logInfo('user logged', ['user_id' => $auth -> getUserId()], 'LOGIN');

            } catch (\Delight\Auth\UnknownUsernameException  $e) {
                $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['invalid_mail'];
            } catch (\Delight\Auth\AmbiguousUsernameException  $e) {
                $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['progress'];
            }catch (\Delight\Auth\InvalidPasswordException $e) {
                $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['wrong_password'];
            }catch (\Delight\Auth\TooManyRequestsException $e) {
                $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['too_many_request'];
            } catch (\Delight\Auth\twoFactorAuthentication $e) {
                $data['status'] = 'false'; $data['statusCode'] = 401; $data['token'] = \Delight\Auth\Auth::createRandomString(24); 
                
                if (isset($authCode) AND $authStatus === false) { 
                    $data['statusCode'] = 400; 
                } else { 
                    $data['2fa'] = '<p class="text-xs text-gray-900 font-medium">Authenticator uygulamasında 6 haneli bir kod görünecektir. 2FA yı doğrulamak için bu kodu girin.</p>
                                    <p class="text-xs mt-2 text-gray-900 font-medium">2FA koduna ulaşmakta sorun yaşayanlar, destek almak için öğrenci işlerine başvurmalıdırlar.</p>
                                    <div class="mx-auto max-w-sm mt-4" id="2faAuthCodeContent">
                                        <div class="flex items-center justify-center space-x-2">
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
                                        <script>
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
                                        </script>
                                    </div>
                                    <div class="flex justify-end mt-4">
                                        <button id="2faAuthenticationStepBtn" type="button" class="text-white flex bg-black hover:bg-black/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onclick="Auth(1);">Doğrula</button>
                                    </div>';
                }
            } catch (\Delight\Auth\UserBlocked $e) {
                $data['status'] = 'false'; $data['statusCode'] = 403; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['user_blocked'];
            } catch (\Delight\Auth\UserTypeNotAcceptable $e) {
                $data['status'] = 'false'; $data['statusCode'] = 403; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['user_type_not_acceptable'];
        } catch(\Throwable $th) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'unknown error!'; }
    }

} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data);
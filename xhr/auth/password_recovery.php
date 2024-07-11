<?php
if($_SERVER['REQUEST_URI'] != '/xhr/auth/password_recovery'){ header('Location: /'); exit(); }

require_once '../../vendor/autoload.php';
include_once '../../src/language/language.php';
require_once '../../src/database/config.php';
require_once '../../src/logger/logger.php';

if(isset($_COOKIE['lang'])){ require_once('../../src/language/'.strip_tags($_COOKIE['lang']).'.php'); } else { require_once('../../src/language/turkish.php'); }

$dbClass = new dbInformation();
$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $userNumber = null; $birthday = null; $idenityNumber = null;

header("Content-type: application/json; charset=utf-8");

try {
    $db = new \PDO('mysql:dbname='.$dbClass -> getDbName().';port='.$dbClass -> getPort().';host='.$dbClass -> getHost().';charset=utf8', $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);
	
    if ($auth -> check()){ $errors['isLogged'] = true; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }
    
    if (empty($_POST['idenityNumber']) OR empty($_POST['userNumber']) OR empty($_POST['birthday'])) { $errors['fields'] = 'information is empty or incorrect!'; } else {
        $idenityNumber = htmlspecialchars(trim($_POST['idenityNumber']), ENT_QUOTES, 'UTF-8');
        $birthday = date("d/m/Y", strtotime(htmlspecialchars(trim($_POST['birthday']), ENT_QUOTES, 'UTF-8')));
        $userNumber = htmlspecialchars(trim($_POST['userNumber']), ENT_QUOTES, 'UTF-8');
    }
    
    if (!empty($errors)) { $data['status'] = false; $data['statusCode'] = 406;  $data['errors'] = $errors; } else {
        $user = $db -> prepare("SELECT `email` FROM `Ms_Users` WHERE `idenity_number` = :idenityNumber AND `birthday` = :birthday AND `username` = :userId");
        $user -> bindValue(':userId',(string) $userNumber, PDO::PARAM_STR);
        $user -> bindValue(':idenityNumber',(string) $idenityNumber, PDO::PARAM_STR);
        $user -> bindValue(':birthday',(string) $birthday, PDO::PARAM_STR);
        $user -> execute();

        if($user -> rowCount() > 0) {
            $userData = $user -> fetch();

            try {
                $auth -> forgotPassword($userData['email'], function ($hash, $token) {
                    $data['status'] = 'success'; 
                    $data['statusCode'] = 200; 
                    $data['token'] = \Delight\Auth\Auth::createRandomString(24); 
                    $data['userId'] = \Delight\Auth\Auth::createUuid();
                    $data['token'] = \urlencode($token);
                    $data['hash'] = \urlencode($hash);
                    $data['passwordRecoveryContent'] = '<label for="resetPasswordFirstInput" class="block mb-2 text-sm font-medium text-gray-900">Şifre</label>
                                                        <input type="password" id="resetPasswordFirstInput" class="focus:ring-0 focus:border-black block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-xs font-medium text-gray-900" placeholder="••••••••••••••••" maxlength="16" required />
                                                        <label for="resetPasswordSecondInput" class="block mb-2 text-sm font-medium text-gray-900">Şifre Tekrar</label>
                                                        <input type="password" id="resetPasswordSecondInput" class="focus:ring-0 focus:border-black block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-xs font-medium text-gray-900" placeholder="••••••••••••••••" maxlength="16" required />';

                    die(json_encode($data, JSON_UNESCAPED_UNICODE));
                });
            } catch (\Delight\Auth\InvalidEmailException $e) {
                $data['status'] = false; $data['statusCode'] = 404; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['passwordRecoveryMessage'] = 'user not found';
            } catch (\Delight\Auth\EmailNotVerifiedException $e) {
                $data['status'] = false; $data['statusCode'] = 400; $$data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['passwordRecoveryMessage'] = 'Email not verified';
            } catch (\Delight\Auth\ResetDisabledException $e) {
                $data['status'] = false; $data['statusCode'] = 400; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['passwordRecoveryMessage'] = 'Password reset is disabled';
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                $data['status'] = false; $data['statusCode'] = 429; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['passwordRecoveryMessage'] = 'Too many requests';
            }
        } else { $data['status'] = false; $data['statusCode'] = 404; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['passwordRecoveryMessage'] = 'user not found'; }
    }
} catch (\Throwable $th) {
    $logger->logError($th, ['details' -> $th->getMessage(), 'user_id' -> $auth -> getUserId()], 'PASSWORD_RECOVERY');
    die();
}

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
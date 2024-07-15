<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/reset_password'){ header('Location: /'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';

$dbClass = new dbInformation();
$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $hash = null; $token = null; $password = null; $rePassword = null;

header("Content-type: application/json; charset=utf-8");

try {
    $db = new \PDO('mysql:dbname='.$dbClass -> getDbName().';port='.$dbClass -> getPort().';host='.$dbClass -> getHost().';charset=utf8', $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);
	
    if ($auth -> check()){ $errors['isLogged'] = true; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }
    
    if (empty($_POST['hash']) OR empty($_POST['token']) OR empty($_POST['password']) OR empty($_POST['rePassword'])) { $errors['fields'] = true; $data['resetPasswordMessage'] = 'information is empty or incorrect!'; } else {
        $hash = htmlspecialchars(trim($_POST['hash']), ENT_QUOTES, 'UTF-8');
        $token = htmlspecialchars(trim($_POST['token']), ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars(trim($_POST['password']), ENT_QUOTES, 'UTF-8');
        $rePassword = htmlspecialchars(trim($_POST['rePassword']), ENT_QUOTES, 'UTF-8');
    }
    
    if (isset($password) AND isset($rePassword) AND $password != $rePassword) { $errors['password'] = 'Password is not correct!'; $data['resetPasswordMessage'] = 'Password is not correct!'; }

    if (!empty($errors)) { $data['status'] = false; $data['statusCode'] = 406;  $data['errors'] = $errors; } else {
        try {
            $auth -> resetPassword($hash, $token, $password);
            $data['status'] = 'success'; 
            $data['statusCode'] = 200; 
            $data['token'] = \Delight\Auth\Auth::createRandomString(24); 
            $data['userId'] = \Delight\Auth\Auth::createUuid();
            $data['token'] = \urlencode($token);
            $data['hash'] = \urlencode($hash);
            $data['resetPasswordContent'] = '<div class="flex items-center justify-center">
                                                <div>
                                                    <svg width="78" height="96" viewBox="0 0 78 96" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M62.857 25.297c6.541 1.605 10.407 7.909 9.634 15.164-.952 8.325-2.379 8.623 3.211 18.197 3.687 6.78 2.2 13.618-3.33 16.591-6.363 3.509-7.612 2.379-10.05 12.37-.833 3.032-2.438 5.173-4.46 6.303-.654.357-4.282 1.606-4.995 1.784-2.2.476-1.725-1.01-4.342-2.498-6.957-3.984-7.136-5.708-15.758-2.794-6.126 1.843-11.775-2.32-13.856-9.694-2.32-8.444-1.25-9.455-9.574-15.818-5.71-4.579-7.018-11.894-3.51-17.602 4.045-6.542 5.531-6.066 3.807-16.948-.714-5.59-1.963-8.742 1.724-10.407.298-.179 4.52-1.844 4.817-1.963a11.5 11.5 0 0 1 3.568-.416c7.434.297 7.553 2.379 13.737-4.817.595-.713.06-.654-.654-1.01-.119-.06 5.233-2.023 6.066-2.32 2.378-.833 4.638.833 6.125 1.843a17.2 17.2 0 0 1 3.746 3.747c5.233 6.898 4.698 8.444 14.094 10.288" fill="#34873C"/><path fill-rule="evenodd" clip-rule="evenodd" d="M59.232 26.487c6.541 1.546 10.407 7.91 9.574 15.164-.892 8.325-2.319 8.623 3.271 18.197 3.687 6.78 2.2 13.559-3.33 16.591-6.363 3.45-7.612 2.32-10.11 12.37-1.903 6.898-7.611 9.039-13.737 5.53-7.017-3.984-7.136-5.709-15.758-2.735-6.125 1.784-11.775-2.32-13.856-9.694-2.379-8.444-1.309-9.455-9.574-15.818-5.71-4.638-7.018-11.953-3.51-17.662 4.045-6.541 5.531-6.065 3.807-16.948-1.011-7.552 3.033-12.547 9.515-12.31 7.374.298 8.147 1.903 14.331-5.292 4.4-4.817 10.764-3.747 15.283 2.26 5.233 6.957 4.698 8.444 14.094 10.347" fill="#9FE0B0"/><path d="M50.372 7.218c-.119.536-.654.892-1.19.833-.535-.119-.891-.654-.832-1.19L49.54.797c.059-.535.594-.892 1.13-.773.594.06.95.595.832 1.19zm12.665 6.066c-.297.416-.951.475-1.367.178a1.05 1.05 0 0 1-.238-1.427l3.151-4.044c.357-.416.952-.476 1.428-.178.416.357.476 1.01.178 1.427zm8.027 11.656c-.535.177-1.13-.12-1.308-.596-.179-.535.118-1.13.594-1.308l4.936-1.724a1.044 1.044 0 0 1 1.308.654c.179.476-.119 1.07-.594 1.249zM24.978 51.343c-.714-1.011-3.866-3.211-3.271-3.984 0 0 1.011-.417 2.14-.773 2.439-.833 2.499-.595 4.104 1.546 2.557 3.449 6.72 9.812 7.612 11.06l7.493-20.813s2.497-.951 4.103-1.487c.476-.178 1.011-.119 1.249-.06 2.2.477 3.27 3.985 1.486 6.542l-12.25 22.479-5.946 2.14z" fill="#E6E6E6"/><path d="M20.638 51.164c-1.843-2.855 1.903-5.471 3.628-2.795l7.017 10.585 10.169-19.208c2.14-3.45 6.184-.238 3.984 3.39L31.7 67.993z" fill="#2B2A29"/></svg>                                   
                                                </div>
                                            </div>
                                            <p class="font-medium text-lg text-black text-center mt-4">Şifre Değiştirildi!</p>
                                            <p class="text-xs text-black text-center mt-2">Şifre başarıyla değiştirildi. Giriş yapabilirsiniz.</p>';

            die(json_encode($data, JSON_UNESCAPED_UNICODE));
        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            $data['status'] = false; $data['statusCode'] = 404; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['resetPasswordMessage'] = 'Invalid token';
        } catch (\Delight\Auth\TokenExpiredException $e) {
            $data['status'] = false; $data['statusCode'] = 404; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['resetPasswordMessage'] = 'Token expired';
        } catch (\Delight\Auth\ResetDisabledException $e) {
            $data['status'] = false; $data['statusCode'] = 404; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['resetPasswordMessage'] = 'Password reset is disabled';
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            $data['status'] = false; $data['statusCode'] = 404; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['resetPasswordMessage'] = 'Invalid password';
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            $data['status'] = false; $data['statusCode'] = 404; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['resetPasswordMessage'] = 'Too many requests';
        }
    }
} catch (\Throwable $th) {
    $logger->logError($th, ['details' -> $th->getMessage(), 'user_id' -> $auth -> getUserId()], 'RESET_PASSWORD');
    die();
}

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
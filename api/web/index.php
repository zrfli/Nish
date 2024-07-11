<?php
#Author : Misy

#GENERAL
error_reporting(0);

#JSON
header("Content-Type: application/json"); 

#API KEY
const API_KEY = '2wKJOLsaaJhl1Y3ARLQcsVO6sGU65PT1DrmxIL0vOoeHnGCIiEUkPmFTPcu0vg7K';

#Database options
const DB_HOST = 'localhost';
const DB_USER = 'misy';
const DB_DBNAME = 'misy';
const DB_PASSWORD = 'misy';

#REQUEST TYPE
$requestType = array(
	'fetch',
	'delete',
	'insert',
	'update',
	'login',
	'register',
	'like_post',
	'unlike_post',
	'delete_post',
	'create_post',
	'edit_post',
	'comment_post',
	'delete_comment',
	'reply_comment',
	'delete_reply_comment'
);

$type = null;
$id = null;
$username = null;
$password = null;
$data = null;

#PDO
\ini_set('assert.active', 1);
@\ini_set('zend.assertions', 1);
\ini_set('assert.exception', 1);

$charset = 'utf8';
$collate = 'utf8_unicode_ci';

$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_PERSISTENT => false,
	PDO::ATTR_EMULATE_PREPARES => false,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	//PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset COLLATE $collate"
];

if($_GET['token'] == API_KEY AND $_SERVER['REQUEST_METHOD'] == 'GET' AND in_array($_GET['type'], $requestType)){
	if(intval($_GET['id']) > 0 AND isset($_GET['id'])){ $id = intval($_GET['id']); }
    $type = strip_tags($_GET['type']);

    if(isset($_GET['username'])){ $username = strip_tags($_GET['username']); }
    if(isset($_GET['password'])){ $password = strip_tags($_GET['password']); }

    require_once("../../vendor/autoload.php");
    require_once '../../src/database/db.php';
    include_once('../../src/language/language.php'); 

    $dbClass = new dbInformation();

    if(isset($_COOKIE['lang'])){ require_once('../../src/language/'.strip_tags($_COOKIE['lang']).'.php'); }

    $db = new \PDO('mysql:dbname='.$dbClass -> getDbName().';port='.$dbClass -> getPort().';host='.$dbClass -> getHost().';charset=utf8', $dbClass -> getUser(), $dbClass -> getPassword());
    $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $auth = new \Delight\Auth\Auth($db);

    switch($type){
        case 'fetch':
            if(is_int($id)){
                try {
                    $user = $db -> prepare("SELECT * FROM `Ms_Users` WHERE `id` = :userId");
                    $user -> bindValue(':userId',(int) intval($id), PDO::PARAM_INT);
                    $user -> execute();
                    $userOut = $user -> fetchAll();

                    if($user -> rowCount() > 0){
                        echo json_encode($userOut[0]);
                    } else {
                        $data['status'] = false; $data['statusCode'] = 404; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = 'user not found';
                    }
                } catch (PDOException $e) { die('service is not accept to parameter'); }
            } else { $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = 'not implemented'; }
            break;
        case 'login':
            if(!$auth -> check()){
                try {
                    $auth -> login($username, $password, (int) (60 * 60 * 24 * 365.25));
        
                    $data['status'] = 'success'; $data['statusCode'] = 200; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['userId'] = \Delight\Auth\Auth::createUuid($auth -> getUserId()); $data['message'] = 'success';
                } catch (\Delight\Auth\InvalidEmailException $e) {
                    $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['invalid_mail'];
                } catch (\Delight\Auth\InvalidPasswordException $e) {
                    $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['wrong_password'];
                } catch (\Delight\Auth\UserAlreadyExistsException $e) {
                    $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['user_exists'];
                } catch (\Delight\Auth\TooManyRequestsException $e) {
                    $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['too_many_request'];
                } catch (\Delight\Auth\UserBlocked $e) {
                    $data['status'] = 'false'; $data['statusCode'] = 403; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['user_blocked'];
                }
            } else { $data['status'] = false; $data['statusCode'] = 406; $data['isLogged'] = true; }
            echo json_encode($data);
            break;
        case 'register':
            if(!$auth -> check()){
                try {
                    $userId = $auth -> register($username, $password, 1, 'us', NULL, function () {});
                    $data['status'] = 'success'; $data['statusCode'] = 200; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['userId'] = $userId; $data['message'] = 'success';
                } catch (\Delight\Auth\InvalidEmailException $e) {
                    $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['invalid_mail'];
                } catch (\Delight\Auth\InvalidPasswordException $e) {
                    $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['wrong_password'];
                } catch (\Delight\Auth\UserAlreadyExistsException $e) {
                    $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['user_exists'];
                } catch (\Delight\Auth\TooManyRequestsException $e) {
                    $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = $misy['error']['too_many_request'];
                }
            }
            echo json_encode($data);
            break;
        case 'like_post':
            if($auth -> check()){
                if(isset($_GET['post_id']) AND $_GET['post_id'] > 0 AND isset($_GET['user_id']) AND $_GET['user_id'] > 0){
                    $like = $db -> prepare("INSERT INTO `wo_reactions` (`user_id`, `post_id`, `reaction`) VALUES (:userId, :postId, :reactionType)");
                    $like -> bindValue(':userId',(int) intval($_GET['user_id']), PDO::PARAM_INT);
                    $like -> bindValue(':postId',(int) intval($_GET['post_id']), PDO::PARAM_INT);
                    $like -> bindValue(':reactionType',(int) 0, PDO::PARAM_INT);
                    $like -> execute();
            
                    if($like -> rowCount() > 0){ 
                        $data['status'] = 'success'; $data['statusCode'] = 200; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['userId'] = \Delight\Auth\Auth::createUuid($auth -> getUserId()); $data['message'] = 'success';
                    } else {                 
                        $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = 'not implemented';
                    }
                } else { $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = 'not implemented'; }
            } else { $data['status'] = false; $data['statusCode'] = 406; $data['isLogged'] = false; }
            echo json_encode($data);
            break;
		default:
            $data['status'] = false; $data['statusCode'] = 501; $data['token'] = \Delight\Auth\Auth::createRandomString(24); $data['message'] = 'not implemented';
            break;
    }
    
} else { die('method not allowed'); }
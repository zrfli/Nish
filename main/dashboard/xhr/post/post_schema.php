<?php
if($_SERVER['REQUEST_URI'] != '/xhr/dashboard/post/post_schema'){ header('Location: /not-found'); exit(); }

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = [];

header("Content-type: application/json; charset=utf-8");

try {
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);
	
    if (!$auth -> check()){ $errors['isLogged'] = false; }
    //if (!$auth -> hasRole(\Delight\Auth\Role::STUDENT)) { $errors['permission'] = 'permission denied!'; }
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

        $data['postSchema'][1]['name'] = 'Havale / Eft';
        $data['postSchema'][1]['active'] = 1;
        $data['postSchema'][1]['type'] = 'bank_transfer';

        $data['postSchema'][2]['name'] = 'Gönderi / Paylaşım Oluştur';
        $data['postSchema'][2]['active'] = 1;
        $data['postSchema'][2]['type'] = 'credit_card';

        $data['postSchema'][3]['name'] = '<svg class="w-28 h-5" width="210" height="29" viewBox="0 0 210 29" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient x1="90.255%" y1="50%" x2="0%" y2="50%" id="a"><stop stop-color="#1E64FF" offset="0%"/><stop stop-color="#1E64FF" stop-opacity="0" offset="100%"/></linearGradient></defs><g fill="none" fill-rule="evenodd"><path d="M2.603 0A2.6 2.6 0 0 0 0 2.6v15.594c0 1.436 1.166 2.6 2.603 2.6h19.654c.97 0 1.86-.54 2.309-1.4l4.066-7.797c.192-.37.29-.774.294-1.178v-.044a2.6 2.6 0 0 0-.294-1.179L24.566 1.4A2.6 2.6 0 0 0 22.257 0z" transform="translate(0 4.371)" fill="url(#a)"/><path d="M149.143 8.286a1.986 1.986 0 0 1 2.677-.843 1.98 1.98 0 0 1 .844 2.676l-9.054 17.444a1.99 1.99 0 0 1-1.68 1.064 1.93 1.93 0 0 1-1-.221 1.98 1.98 0 0 1-.846-2.675l2.56-4.938-5.545-10.674a1.987 1.987 0 0 1 3.524-1.832l4.26 8.2Zm36.443-1.331c2.148 0 4.167.835 5.687 2.352.772.775.772 2.03 0 2.805a1.993 1.993 0 0 1-2.81 0 4.04 4.04 0 0 0-2.877-1.19 4.03 4.03 0 0 0-2.874 1.19 4.02 4.02 0 0 0-1.191 2.87 4.03 4.03 0 0 0 1.19 2.872 4.05 4.05 0 0 0 2.875 1.191 4.06 4.06 0 0 0 2.877-1.191 1.984 1.984 0 1 1 2.81 2.802 8 8 0 0 1-5.687 2.353 8 8 0 0 1-5.682-2.354 7.96 7.96 0 0 1-2.356-5.673c0-2.145.837-4.158 2.356-5.675a8 8 0 0 1 5.682-2.352m16.056 0c4.433 0 8.039 3.6 8.039 8.027s-3.606 8.027-8.04 8.027c-4.43 0-8.036-3.6-8.036-8.027s3.607-8.027 8.037-8.027m-69.107.264c1.097 0 1.986.888 1.986 1.98v11.598a1.983 1.983 0 0 1-1.986 1.982 1.983 1.983 0 0 1-1.986-1.982V9.2c0-1.093.89-1.98 1.986-1.98m33.293 0h.201c.474-.016.953.135 1.345.465.839.7.948 1.953.245 2.79l-7.01 8.34h5.486c1.097 0 1.987.89 1.987 1.983 0 1.096-.89 1.982-1.987 1.982h-9.744a1.98 1.98 0 0 1-1.52-3.256l7.008-8.337h-4.582a1.984 1.984 0 1 1 0-3.967h1.769Zm7.132 0c1.1 0 1.986.888 1.986 1.98v11.598a1.98 1.98 0 0 1-1.986 1.982 1.98 1.98 0 0 1-1.983-1.982V9.2c0-1.093.888-1.98 1.983-1.98m28.682 3.702a4.066 4.066 0 0 0-4.064 4.06 4.07 4.07 0 0 0 4.064 4.063 4.07 4.07 0 0 0 4.068-4.063c0-2.24-1.825-4.06-4.068-4.06M132.535.905c1.211 0 2.195.981 2.195 2.192a2.195 2.195 0 0 1-4.388 0c0-1.21.982-2.192 2.193-2.192m40.425 0c1.214 0 2.196.981 2.196 2.192a2.195 2.195 0 0 1-4.39 0c0-1.21.982-2.192 2.194-2.192" fill="#1E64FF"/><path d="M72.89 10.671a1.11 1.11 0 0 1 1.128 1.103c0 .23-.07.39-.138.551l-6.144 14.013c-.253.574-.69.827-1.15.827-.644 0-1.081-.506-1.081-1.103 0-.23.069-.39.138-.551l1.426-3.285-4.394-9.9c-.07-.162-.138-.322-.138-.552a1.11 1.11 0 0 1 1.127-1.103c.46 0 .897.253 1.15.827l3.452 8.017 3.474-8.017c.253-.574.69-.827 1.15-.827m-18.073-.069c1.703 0 3.176.873 3.774 2.206v-.92c0-.666.552-1.217 1.22-1.217.666 0 1.242.551 1.242 1.218v9.716c0 .666-.576 1.217-1.243 1.217s-1.22-.551-1.22-1.217v-1.24c-.16 1.033-1.587 2.48-3.773 2.48-2.554 0-5.614-1.975-5.614-6.133 0-4.09 3.083-6.11 5.614-6.11m46.084.07c.506 0 .943.436.943.964v10.2c0 .55-.437.987-.943.987a.976.976 0 0 1-.966-.988V11.636c0-.528.437-.965.966-.965M42.428 6.605c3.589 0 5.867 2.205 5.867 5.1 0 3.01-2.278 5.123-5.867 5.123h-3.084v4.732c0 .689-.552 1.263-1.219 1.263a1.26 1.26 0 0 1-1.265-1.263V7.96c0-.78.575-1.355 1.357-1.355Zm63.843 1.218c.529 0 .943.436.943.942v1.975h1.84c.461 0 .806.299.806.781s-.345.804-.805.804h-1.84v7.81c0 .713.298 1.011.873 1.011.346 0 .645-.092.898-.138.506-.114.874.207.874.667 0 .827-1.127 1.125-2.163 1.125-1.22 0-2.393-.436-2.393-2.596v-7.879h-1.012c-.483 0-.805-.321-.805-.804 0-.482.322-.78.805-.78h1.012V8.765c0-.506.438-.942.967-.942m6.405-1.54c.506 0 .943.437.943.942v6.272c.138-.781 1.565-2.872 4.303-2.872 2.163 0 4.486 1.31 4.486 5.123v6.087a.987.987 0 0 1-.966.965c-.53 0-.943-.46-.943-.965v-6.087c0-2.504-1.519-3.446-3.13-3.446-2.346 0-3.543 1.976-3.75 3.285v6.248c0 .506-.437.965-.943.965a.987.987 0 0 1-.966-.965V7.225c0-.505.437-.941.966-.941m-15.534 4.388c.506 0 .92.414.92.896 0 .16-.069.276-.115.437l-3.405 9.992c-.161.46-.483.735-.897.735s-.737-.275-.92-.735l-3.406-8.453-3.382 8.453c-.184.46-.53.735-.92.735-.415 0-.737-.275-.898-.735l-3.405-9.992c-.046-.161-.115-.276-.115-.437 0-.482.414-.896.897-.896.368 0 .737.23.852.529l2.784 8.614 3.313-8.546c.16-.39.46-.597.874-.597s.736.207.897.597l3.314 8.546 2.784-8.614c.115-.322.483-.529.828-.529m-41.933 2.022c-1.68 0-3.567 1.31-3.567 4.02 0 2.733 1.887 4.043 3.567 4.043 1.564 0 3.497-1.195 3.497-4.043 0-2.872-1.933-4.02-3.497-4.02M42.335 8.742h-2.99v5.95h2.99c2.117 0 3.544-.85 3.544-2.987 0-2.022-1.427-2.963-3.544-2.963m58.566-2.688c.506 0 .966.436.966.965s-.46.964-.966.964c-.53 0-.99-.436-.99-.964 0-.529.46-.965.99-.965" fill="#495057" fill-rule="nonzero"/></g></svg>';
        $data['postSchema'][3]['active'] = 1;
        $data['postSchema'][3]['type'] = 'iyzipay';
        
        $data['postSchema'][4]['name'] = '<svg class="w-16 h-5" xmlns="http://www.w3.org/2000/svg" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;fill-rule:evenodd;clip-rule:evenodd" viewBox="8.79 46.32 192.15 33.11"><path style="opacity:.919" fill="#3e80c3" d="M89.906 46.856H74.418q3.336-.524 6.956-.525l6.956.131q.869.075 1.575.394"/><path style="opacity:.943" fill="#3f80c3" d="M115.369 46.856h-9.975q.044-.243.263-.394 4.725-.263 9.45 0 .219.151.263.394"/><path style="opacity:.942" fill="#3f81c3" d="M126.919 46.856h-8.138q-.214-.024-.263-.263a42 42 0 0 1 8.138-.131q.219.151.263.394"/><path style="opacity:.994" fill="#485051" d="M173.381 46.856h-22.313q5.441-.525 11.156-.525t11.156.525"/><path style="opacity:.924" fill="#474f50" d="M190.444 46.856h-15.225q3.271-.524 6.825-.525l6.825.131q.869.075 1.575.394"/><path style="opacity:.955" fill="#1a7ec1" d="M16.144 46.594q5.252-.066 10.5.131.984.459 1.444 1.444.123 1.878-.525 3.675a4.7 4.7 0 0 1-1.444 1.181 95 95 0 0 1-9.975 0q-1.669-.575-1.838-2.363.203-2.391 1.838-4.069"/><path style="opacity:.982" fill="#1c7fc2" d="M34.256 46.594q13.913-.066 27.825.131 3.028 1.192 3.413 4.463a1464 1464 0 0 0-4.069 24.806q-.853 2.428-3.281 3.281-14.306.263-28.613 0-2.25-.891-1.969-3.281l2.1-9.45a4.7 4.7 0 0 1 1.181-1.444 169 169 0 0 1 12.863-.525q2.327-.622 2.756-3.019a16.9 16.9 0 0 0 .263-4.987q-.722-2.297-3.019-3.019l-8.925-.263q-2.347-.508-2.363-2.888-.16-2.482 1.838-3.806"/><path style="opacity:.957" fill="#1b7fc2" d="M74.419 46.856h15.488q6.796 1.349 8.269 8.138Q99.73 65.883 89.12 68.25a55 55 0 0 1-7.35.394v10.763a51 51 0 0 1-7.219-.263 1985 1985 0 0 1-.131-32.288m7.35 5.513q2.366-.065 4.725.131 4.424.421 4.2 4.856.354 3.907-3.413 4.856-2.753.197-5.513.131z"/><path style="opacity:.964" fill="#1c7fc2" d="M105.394 46.856h9.975a1789 1789 0 0 1 11.287 32.55q-3.94.066-7.875-.131-1.217-3.321-2.363-6.694-6.3-.525-12.6 0-1.146 3.372-2.363 6.694-3.673.197-7.35.131a1789 1789 0 0 1 11.287-32.55m4.463 6.563q.393-.012.525.394a552 552 0 0 0 4.2 12.863 76 76 0 0 1-8.925 0 233 233 0 0 0 4.2-13.256"/><path style="opacity:.96" fill="#1c7fc2" d="M118.781 46.856h8.138l6.825 13.125a494 494 0 0 0 7.35-13.388 27.8 27.8 0 0 1 7.613 0 1398 1398 0 0 0-11.156 19.95q-.197 6.43-.131 12.863h-7.35q.066-6.301-.131-12.6a1127 1127 0 0 1-11.156-19.95"/><path style="opacity:.966" fill="#464e4f" d="M151.069 46.856h22.313v5.513h-9.188v27.038h-7.35V52.369a75 75 0 0 0-8.794.263q-.101-.991.525-1.838a66 66 0 0 1 2.494-3.938"/><path style="opacity:.953" fill="#464e4f" d="M175.219 46.856h15.225q8.724 1.705 8.662 10.631.088 6.917-6.038 9.975a478 478 0 0 1 7.613 11.419q-3.735.391-7.613.263a304 304 0 0 1-6.169-10.5q-2.144-.514-4.331 0a113 113 0 0 1 .263 10.763 54.5 54.5 0 0 1-7.481-.263 1985 1985 0 0 1-.131-32.288m7.35 5.513q3.043-.122 6.038.394 3.028 1.263 3.15 4.594-.017 3.652-3.413 4.856a32 32 0 0 1-5.775 0 47.3 47.3 0 0 0 0-9.844"/><path style="opacity:.958" fill="#1b7fc2" d="M19.294 55.781q9.057-.066 18.113.131 2.257 1.252 1.444 3.806-.123 1.758-1.706 2.494-9.188.263-18.375 0-1.482-.628-1.575-2.231.005-2.702 2.1-4.2"/><path style="opacity:.967" fill="#1b7fc2" d="M10.631 64.969q6.564-.066 13.125.131.853.328 1.181 1.181.341 2.046-.525 3.938a3 3 0 0 1-1.181.919 158 158 0 0 1-12.863 0q-1.39-.602-1.575-2.1a63 63 0 0 1 .656-2.756q.403-.861 1.181-1.313"/><path style="opacity:.949" fill="#217fc2" d="M15.356 73.894q3.415-.066 6.825.131 1.235.498 1.313 1.838a17.4 17.4 0 0 1-.656 2.494 3 3 0 0 1-1.181.919q-3.584.371-7.088-.263-1.723-2.837.788-5.119"/><path style="opacity:.128" fill="#959a9b" d="M200.681 78.881q.214.024.263.263-4 .521-7.875 0 3.877.129 7.613-.263"/></svg>';
        $data['postSchema'][4]['active'] = 1;
        $data['postSchema'][4]['type'] = 'paytr';
    }
    
} catch (\Throwable $th) {
    //$logger->logError($th, ['details' -> $th->getMessage(), 'user_id' -> $auth -> getUserId()], 'GET_PAYMENT_METHODS');
    die();
}

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
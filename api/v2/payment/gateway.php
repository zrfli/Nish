<?php
//if($_SERVER['REQUEST_URI'] != '/api/v2/payment/gateway/'.htmlspecialchars($_GET['provider'])){ header('Location: /'); exit(); }

require_once("../../../vendor/autoload.php");
require_once '../../../src/database/config.php';
require_once '../../../src/logger/logger.php';

$dbClass = new dbInformation();
$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $conversionId = null; $provider = ['iyzipay', 'paytr', 'bank_transfer', 'credit_card', 'stripe'];

function paymentStatus($status) {
    return match ($status) {
        200 => '<script>window.parent.handleBankResponse(200)</script>',
        400 => '<script>window.parent.handleBankResponse(400)</script>',
        500 => '<script>window.parent.handleBankResponse(500)</script>'
    };
}

function updatePaymentStatus($conversionId, $checkoutForm) {
    return 200;
}

try {
    $db = new \PDO('mysql:dbname='.$dbClass -> getDbName().';port='.$dbClass -> getPort().';host='.$dbClass -> getHost().';charset=utf8', $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);
	
    //if (!$auth -> check()) { $errors['isLogged'] = false; }
    //if ($_SERVER['REQUEST_METHOD'] != 'POST') { $errors['method'] = 'method not accepted!'; }
    //if (isset($_GET['conversion_id'])) { $conversionId = htmlspecialchars($_GET['conversion_id']); } else { $errors['conversionId'] = 'conversion id is not correct!'; }
    if (isset($_GET['provider'])) { $provider = htmlspecialchars($_GET['provider']); } else { $errors['provider'] = 'provider is not correct!'; }
    //if (!$auth -> hasRole(\Delight\Auth\Role::STUDENT)) { $errors['permission'] = 'permission denied!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        if($provider == 'credit_card') {

        } else if ($provider == 'iyzipay') {
            if(isset($_POST['token'], $_GET['conversionId'])) { 
                require_once('../../../src/payment/iyzipay/config.php');

                $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
                $request -> setLocale(\Iyzipay\Model\Locale::TR);
                $request -> setConversationId($_GET['conversionId']);
                $request -> setToken($_POST['token']);

                $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, Config::options());
                
                if ($checkoutForm -> getPaymentStatus() == 'SUCCESS') {
                    if (updatePaymentStatus($_GET['conversionId'], $checkoutForm) == 200) { echo paymentStatus(200); } else { echo paymentStatus(500); }
                } else { echo paymentStatus(400); }

                exit();
            } else {
                $data['status'] = false; $data['statusCode'] = 400; $data['paymentStatus'] = 'iyzipay execution error!';
            }
        } else if ($provider == 'paytr') {
            echo 'paytr api';
            $post = $_POST;

            require_once('../../../src/payment/paytr/response.php');

            if (responsePaytrPayment($post) == 'success') {
                echo paymentStatus(200);
            } else { echo paymentStatus(400); }
        } else {
            $data['status'] = false; $data['statusCode'] = 400; $data['paymentStatus'] = 'provider is not correct!';
        }

        /*$stripe = new \Stripe\StripeClient($stripeSecretKey);

        function checkPaymentStatus($sessionId){
            global $stripe, $conversionId, $db, $auth;

            $session = $stripe -> checkout -> sessions -> retrieve($conversionId);
      
            try {
                switch (strtoupper($session -> status)) {
                  case 'COMPLETE':
                    $updat_user_balance = $db -> prepare("UPDATE `Ms_Payments` mp, `Ms_Users` mu SET mp.status = :paymentStatus, mu.balance = mu.balance + :insertBalance WHERE mp.conversionId = :conversionId AND mp.user_id = :userId AND mp.status = 0 AND mu.id = :userId");
                    $updat_user_balance -> bindValue(':userId',(int) $auth -> getUserId(), PDO::PARAM_INT);
                    $updat_user_balance -> bindValue(':paymentStatus',(int) 1, PDO::PARAM_INT);
                    $updat_user_balance -> bindValue(':insertBalance',(int) $amount_total, PDO::PARAM_INT);
                    $updat_user_balance -> bindValue(':conversionId',(string) $conversionId, PDO::PARAM_STR);
                    //$updat_user_balance -> execute();
      
                    if($updat_user_balance -> rowCount() > 0){ return 200; }              
                  break;
                  default:        
                    return 400;
                  break;
                }
            } catch (Error $e) {  
                $data['status'] = false; $data['statusCode'] = 406; $data['paymentStatus'] = 'FAILED';
            }
        }

        if(checkPaymentStatus($sessionId) == 200){ header('Location: /wallet/thankyou'); } else { header('Location: /wallet/declined'); }*/
    }
} catch (\Throwable $th) {
    $logger->logError($th, ['details' -> $th->getMessage(), 'user_id' -> $auth -> getUserId()], 'GET_PAYMENT_GATEWAY');
    die();
}

$db = null;

header("Content-type: application/json; charset=utf-8");

echo json_encode($data, JSON_UNESCAPED_UNICODE);
<?php
//if($_SERVER['REQUEST_URI'] != '/api/v2/payment/gateway/'.htmlspecialchars($_GET['provider'])){ header('Location: /'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';

$dbClass = new misyDbInformation();

$errors = []; $data = []; $conversationId = null; $token = null; $paymentStatus = '0'; $paymentCode = null; $cardDetails = []; $provider = ['iyzipay', 'paytr', 'bank_transfer', 'credit_card', 'stripe'];

function paymentStatus($status) {
    return match ($status) {
        200 => '<script>window.parent.handleBankResponse(200)</script>',
        400 => '<script>window.parent.handleBankResponse(400)</script>',
        500 => '<script>window.parent.handleBankResponse(500)</script>',
        default => 'Unknown payment error!'
    };
}

try {
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());

    //$auth = new \Delight\Auth\Auth($db);

    //if (!$auth -> check()) { $errors['isLogged'] = false; }
    //if ($_SERVER['REQUEST_METHOD'] != 'POST') { $errors['method'] = 'method not accepted!'; }
    if (isset($_POST['token'])) { $token = htmlspecialchars($_POST['token']); } else { $errors['token'] = 'token is not correct!'; }
    if (isset($_GET['conversationId'])) { $conversationId = htmlspecialchars($_GET['conversationId']); } else { $errors['conversationId'] = 'conversation id is not correct!'; }
    if (isset($_GET['provider']) && in_array($_GET['provider'], $provider)) { $provider = htmlspecialchars($_GET['provider']); } else { $errors['provider'] = 'provider is not correct!'; }
    //if (!$auth -> hasRole(\Delight\Auth\Role::STUDENT)) { $errors['permission'] = 'permission denied!'; }

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        if($provider == 'credit_card') {

        } else if ($provider == 'iyzipay') {
            require_once $_SERVER['DOCUMENT_ROOT'].'/src/payment/iyzipay/config.php';

            $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
            $request -> setLocale(\Iyzipay\Model\Locale::TR);
            $request -> setConversationId($_GET['conversationId']);
            $request -> setToken($_POST['token']);

            $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, Config::options());
            
            $paymentCode = match ($checkoutForm -> getPaymentStatus()) {'SUCCESS' => paymentStatus(200), 'FAILURE' => paymentStatus(400), default => paymentStatus(500)};
            $paymentStatus = match ($checkoutForm -> getPaymentStatus()) {'SUCCESS' => '1', 'FAILURE' => '2', default => '3'};

            $cardDetails = [
                'payment_status' => $checkoutForm -> getPaymentStatus() ?? '',
                'payment_id' => $checkoutForm -> getPaymentId() ?? '',
                'installment' => $checkoutForm -> getInstallment() ?? '',
                'bin_number' => $checkoutForm -> getBinNumber() ?? '',
                'price' => $checkoutForm -> getPrice() ?? '',
                'paid_price' => $checkoutForm -> getPaidPrice() ?? '',
                'currency' => $checkoutForm -> getCurrency() ?? '',
                'last_four_digits' => $checkoutForm -> getLastFourDigits() ?? '',
                'card_association' => $checkoutForm -> getCardAssociation() ?? '',
                'card_family' => $checkoutForm -> getCardFamily() ?? '',
                'card_type' => $checkoutForm -> getCardType() ?? '',
                'fraud_status' => $checkoutForm -> getFraudStatus() ?? ''
            ];

            $checkoutFormString = trim(print_r($checkoutForm, true)) ?? null;
            $cardDetailsString = json_encode($cardDetails, true) ?? null;

            $update_payment_status = $db -> prepare("UPDATE `Ms_Payments` SET `return_message` = :returnMessage, `paid_amount` = :paidAmount, `currency` = :paidCurrency, `status` = :paymentStatus, `card_details` = :cardDetails WHERE `conversion_id` = :conversationId;");
            $update_payment_status -> bindValue(':returnMessage', $checkoutFormString, PDO::PARAM_STR);
            $update_payment_status -> bindValue(':paidAmount', $checkoutForm -> getPaidPrice(), PDO::PARAM_STR);
            $update_payment_status -> bindValue(':conversationId', $checkoutForm -> getConversationId(), PDO::PARAM_STR);
            $update_payment_status -> bindValue(':paidCurrency', $checkoutForm -> getCurrency(), PDO::PARAM_STR);
            $update_payment_status -> bindValue(':paymentStatus', $paymentStatus, PDO::PARAM_STR);
            $update_payment_status -> bindValue(':cardDetails', $cardDetailsString, PDO::PARAM_STR);

            $update_payment_status -> execute();

            //if ($update_payment_status -> rowCount() <= 0) {  }

            echo $paymentCode;
            
            return;
        } else if ($provider == 'paytr') {
            echo 'paytr api';
            $post = $_POST;

            require_once ('../../../src/payment/paytr/response.php');

            if (responsePaytrPayment($post) == 'success') {
                echo paymentStatus(200);
            } else { echo paymentStatus(400); }
        } else { $data['status'] = false; $data['statusCode'] = 400; $data['paymentStatus'] = 'provider is not correct!'; }

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
    echo $th;
    die();
}

$db = null;

header("Content-type: application/json; charset=utf-8");

echo json_encode($data, JSON_UNESCAPED_UNICODE);
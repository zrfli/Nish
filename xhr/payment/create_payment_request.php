<?php
if ($_SERVER['REQUEST_URI'] != '/xhr/payment/create_payment_request') { header('Location: /not-found'); exit(); }

require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/language/languageController.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/payment/iyzipay/iyzipay.php';
//require_once $_SERVER['DOCUMENT_ROOT'].'/src/payment/paytr/paytr.php';
//require_once $_SERVER['DOCUMENT_ROOT'].'/src/payment/vakifbank/vakifbank.php';

$dbClass = new misyDbInformation();

$errors = []; $data = []; $paymentAmount = 0; $accountNumber = null; $cardHolderName = null; $binCode = null; $paymentType = ['bank_transfer', 'credit_card', 'iyzipay', 'paytr', 'stripe']; $cardNumber = null; $expMonth = null; $expYear = null; $cardCvv = null; $installment = [0, 1, 2, 3, 6, 9, 12]; $amount = 0; $totalPercentage = 0; $vposCredentials = null; $postData = null;

function getRealIpAddr(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

function deleteWrongRow($paymentId = 0) {
    if ($paymentId <= 0) { return 400; }

    $delete_wrong_row = $db -> prepare("DELETE * FROM `Ms_Payments` WHERE `id` :paymentId");
    $delete_wrong_row -> bindValue(':paymentId',(int) $paymentId, PDO::PARAM_INT);
    $delete_wrong_row -> execute();
}

header("Content-type: application/json; charset=utf-8");

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') { $errors['method'] = 'method not accepted!'; }

    if (isset($_POST['paymentType']) AND in_array(htmlspecialchars($_POST['paymentType']), $paymentType)) { $paymentType = htmlspecialchars($_POST['paymentType']); } else { $errors['paymentType'] = 'payment type is not correct!'; }
    if (isset($_POST['paymentAmount'])) { $paymentAmount = htmlspecialchars($_POST['paymentAmount']);  } else { $errors['paymentAmount'] = 'paymentAmount is not correct!'; }
    if (isset($_POST['accountNumber'])) { $accountNumber = htmlspecialchars($_POST['accountNumber']); } else { $errors['accountNumber'] = 'accountNumber is not correct!'; }

    $paymentData = ['conversationId' => rand(), 'clientIp' => getRealIpAddr(), 'currency' => 'TRY'];

    $merchantData = [
        'clientKey' => null,
        'apiUrl' => null, 
        'callBackUrl' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api/v2/payment/gateway/' . $paymentType . '/' . $paymentData['conversationId'],
    ];

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());

        $create_payment_request = $db -> prepare("INSERT INTO `Ms_Payments` (`user_id`,`conversation_id`,`ip_address`,`amount`,`currency`,`platform`,`payment_type`) VALUES (:userId,:conversationId,:ipAddress,:amount,:currency,:platform,:paymentType);");
        $create_payment_request -> bindValue(':userId', $accountNumber, PDO::PARAM_STR);
        $create_payment_request -> bindValue(':conversationId', $paymentData['conversationId'], PDO::PARAM_STR);
        $create_payment_request -> bindValue(':ipAddress', $paymentData['clientIp'], PDO::PARAM_STR);
        $create_payment_request -> bindValue(':amount', $paymentAmount, PDO::PARAM_STR);
        $create_payment_request -> bindValue(':currency', $paymentData['currency'], PDO::PARAM_STR);
        $create_payment_request -> bindValue(':platform', 'iyzipay', PDO::PARAM_STR);
        $create_payment_request -> bindValue(':paymentType', 'nish_card', PDO::PARAM_STR);
        $create_payment_request -> execute();
        
        if($create_payment_request -> rowCount() > 0) {
            $create_payment_request_result = $db -> lastInsertId();

            if ($paymentType == 'credit_card') { 
                $fetch_bin_details = $db -> prepare("SELECT `bankName`,`cardBrand`,`cardBrandCode`,`cardProgram`,`cardType`,`cvvRequired`,`installment`,`countryCode` FROM `Ms_Bins` WHERE `bin` = :binCode;");
                $fetch_bin_details -> bindValue(':binCode',(int) $binCode, PDO::PARAM_INT);
                $fetch_bin_details -> execute();

                if ($fetch_bin_details -> rowCount() > 0) {
                    $result_fetch_bin_details = $fetch_bin_details -> fetch(PDO::FETCH_ASSOC);

                    $binData = [
                        'bankName' => $result_fetch_bin_details['bankName'] ?? NULL,
                        'cardBrand' => $result_fetch_bin_details['cardBrand'] ?? NULL,
                        'cardBrandCode' => $result_fetch_bin_details['cardBrandCode'] ?? NULL,
                        'cardProgram' => $result_fetch_bin_details['cardProgram'] ?? NULL,
                        'cardType' => $result_fetch_bin_details['cardType'] ?? NULL,
                        'cvvRequired' => (bool) $result_fetch_bin_details['cvvRequired'] ?? NULL,
                        'installment' => (bool) $result_fetch_bin_details['installment'] ?? NULL,
                        'countryCode' => $result_fetch_bin_details['countryCode'] ?? NULL,
                    ];

                    if ($binData['bankName'] == 'T. GARANTİ BANKASI A.Ş.' OR $binData['cardProgram'] == 'Bonus') {
                        $vposCredentials = getVposCredentials('garanti', $dbClass);
                    } else if ($binData['bankName'] == 'AKBANK T.A.Ş.' OR $binData['cardProgram'] == 'Axess') {
                        $vposCredentials = getVposCredentials('akbank', $dbClass);
                    } else if ($binData['bankName'] == 'FİNANS BANK A.Ş.' OR $binData['cardProgram'] == 'CardFinans') {
                        $vposCredentials = getVposCredentials('finansbank', $dbClass);
                    } else if ($binData['bankName'] == 'T. HALK BANKASI A.Ş.' OR $binData['cardProgram'] == 'Paraf') {
                        $vposCredentials = getVposCredentials('halkbank', $dbClass);
                    } else if ($binData['bankName'] == 'TÜRK EKONOMİ BANKASI A.Ş.') {
                        $vposCredentials = getVposCredentials('teb', $dbClass);
                    } else if ($binData['bankName'] == 'T. İŞ BANKASI A.Ş.' OR $binData['cardProgram'] == 'Maximum') {
                        $vposCredentials = getVposCredentials('isbank', $dbClass);
                    } else if ($binData['bankName'] == 'YAPI VE KREDİ BANKASI A.Ş.' OR $binData['cardProgram'] == 'World') {
                        $vposCredentials = getVposCredentials('yapikredi', $dbClass);
                    } else if ($binData['bankName'] == 'T. VAKIFLAR BANKASI T.A.O.') {
                        $vposCredentials = getVposCredentials('vakifbank', $dbClass);
                    } else if ($binData['bankName'] == 'HSBC BANK A.Ş.' OR $binData['cardProgram'] == 'Advantage') {
                        $vposCredentials = getVposCredentials('hsbc', $dbClass);
                    } else if ($binData['bankName'] == 'T.C. ZİRAAT BANKASI A.Ş.' OR $binData['cardProgram'] == 'Combo') {
                        $vposCredentials = getVposCredentials('ziraat', $dbClass);
                    } else {
                        $vposCredentials = getVposCredentials('vakifbank', $dbClass);
                    }
                    
                    if ($vposCredentials instanceof VposCredentials) {
                        $credentials = $vposCredentials -> getCredentials();

                        $clientId = $credentials['CLIENT_ID'];
                        $merchantData['clientKey'] = $credentials['CLIENT_KEY'];
                        $merchantData['apiUrl'] = $credentials['API_URL'];
                        $clientUsername = $credentials['CLIENT_USERNAME'];
                        $clientPassword = $credentials['CLIENT_PASSWORD'];

                        switch ($credentials['CLIENT_KEY']) {
                            case 'garanti':
                                $data['paymentContent'] = "garanti";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'akbank':
                                $data['paymentContent'] = "akbank";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'finansbank':
                                $data['paymentContent'] = "finansbank";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'halkbank':
                                $data['paymentContent'] = "halkbank";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'teb':
                                $data['paymentContent'] = "teb";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'isbank':
                                $isBank = new isBankasi;
                                
                                $data['paymentContent'] = $isBank -> isBankasiOdemeYap();
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'sekerbank':
                                $data['paymentContent'] = "sekerbank";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'yapikredi':
                                $data['paymentContent'] = "yapikredi";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'albaraka':
                                $data['paymentContent'] = "albaraka";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'ziraat':
                                $data['paymentContent'] = "ziraat";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'denizbank':
                                $data['paymentContent'] = "denizbank";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'kuveytpos':
                                $data['paymentContent'] = "kuveytpos";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'hsbc':
                                $data['paymentContent'] = "hsbc";
                                $data['clientKey'] = $credentials['CLIENT_KEY'];             
                                break;
                            case 'vakifbank':
                                $cardExp = (substr($expYear, 2)) . $expMonth;
                                $currency = $paymentData['currency'];
                                $cardBrandCode = $binData['cardBrandCode'];
                                $paymentId = $paymentData['conversationId'];
                                $installment = 1; // taksi
                                number_format($amount = 10.50); //tutar
                                $paymentSessionInfo = base64_encode("Pan=$cardNumber&Ay=$expMonth&Yil=$expYear&Cvv=$cardCvv&Tutar=$amount");
                                
                                $postData = "Pan=$cardNumber".
                                            "&ExpiryDate=$cardExp".
                                            "&PurchaseAmount=10.50".
                                            "&Currency=$currency".
                                            "&BrandName=$cardBrandCode".
                                            "&VerifyEnrollmentRequestId=$paymentId".
                                            "&SessionInfo=$paymentSessionInfo".
                                            "&MerchantId=$clientId".
                                            "&MerchantPassword=$clientPassword".
                                            "&SuccessUrl=" . $merchantData['callBackUrl'].
                                            "&FailureUrl=" . $merchantData['callBackUrl'];

                                            if ($installment > 1){
                                                $postData .= "&InstallmentCount=$installment";
                                            }

                                            $data['paymentContent'] = vakifbankPost3d($credentials['API_URL'], $postData);
                                            $data['clientKey'] = $credentials['CLIENT_KEY'];
                                    break;
                            default:
                                    $data['status'] = false;
                                    $data['statusCode'] = 406;
                                    $data['errors'] = 'credentials error';
                                break;
                        }
                    } else {
                        if ($vposCredentials === 400) {
                            $data['status'] = false;
                            $data['statusCode'] = 406;
                            $data['errors'] = 'The specified bank was not found!';
                        } elseif ($vposCredentials === 500) {
                            $data['status'] = false;
                            $data['statusCode'] = 406;
                            $data['errors'] = 'database error';
                        } else {
                            $data['status'] = false;
                            $data['statusCode'] = 406;
                            $data['errors'] = 'unknown error';
                        }
                    }

                } else { $data['status'] = false; $data['statusCode'] = 406; $data['errors'] = 'invalid card number!'; }
            } else if ($paymentType == 'iyzipay') { 
                $data['status'] = 'success'; $data['statusCode'] = 200;
                $data['paymentContent'] = initializeIyzipayPaymentForm($paymentData['conversationId'], $merchantData['callBackUrl'], 'Nişantaşı', 'Üniversites', 'Token Random', 'nisantasi@nisantasi.edu.tr', $accountNumber, 'Token Random 2', 'Token Rnadom 3');
            } else if ($paymentType == 'paytr') { 
              $data['paymentContent'] = generatePaytrToken("asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL, "asf" ?? NULL);
            } 
        } else { $data['status'] = false; $data['statusCode'] = 406; $data['errors'] = 'payment not found!'; }
    }   
} catch(PDOException $e) { $data['status'] = false; $data['statusCode'] = 500; $data['message'] = 'database error!'; }

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
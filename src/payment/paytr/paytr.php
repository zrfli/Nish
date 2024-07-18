<?php

function generatePaytrToken($merchant_id, $merchant_key, $merchant_salt, $email, $payment_amount, $merchant_oid, $user_name, $user_address, $user_phone, $merchant_ok_url, $merchant_fail_url, $user_basket, $timeout_limit, $currency, $test_mode) {
    $ip = $_SERVER["REMOTE_ADDR"];
    $user_ip = $ip;
    $no_installment = 0;
    $max_installment = 0;
    $debug_on = 1;

    $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
    $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));
    $post_vals = array(
        'merchant_id' => $merchant_id,
        'user_ip' => $user_ip,
        'merchant_oid' => $merchant_oid,
        'email' => $email,
        'payment_amount' => $payment_amount,
        'paytr_token' => $paytr_token,
        'user_basket' => $user_basket,
        'debug_on' => $debug_on,
        'no_installment' => $no_installment,
        'max_installment' => $max_installment,
        'user_name' => $user_name,
        'user_address' => $user_address,
        'user_phone' => $user_phone,
        'merchant_ok_url' => $merchant_ok_url,
        'merchant_fail_url' => $merchant_fail_url,
        'timeout_limit' => $timeout_limit,
        'currency' => $currency,
        'test_mode' => $test_mode
    );

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'PAYTR IFRAME connection error';
        }

        $http_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_status_code !== 200) {
            return 'PAYTR IFRAME failed. HTTP status code: ' . $http_status_code;
        }

        curl_close($ch);

        $result = json_decode($result, true);

        if ($result === null) {
           return 'PAYTR IFRAME failed. Invalid JSON response.';
        }

        if ($result['status'] !== 'success' || !isset($result['token'])) {
            return 'PAYTR IFRAME failed. Reason: ' . $result['reason'];
        }

        return 'https://www.paytr.com/odeme/guvenli/' . $result['token'];
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
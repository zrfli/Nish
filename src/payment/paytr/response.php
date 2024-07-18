<?php

function responsePaytrPayment($post) {
    $merchant_key 	= 'YYYYYYYYYYYYYY';
    $merchant_salt	= 'ZZZZZZZZZZZZZZ';

    $hash = base64_encode(hash_hmac('sha256', $post['merchant_oid'].$merchant_salt.$post['status'].$post['total_amount'], $merchant_key, true));

    if($hash != $post['hash']){ return 400; }

    return $post['status'];
}
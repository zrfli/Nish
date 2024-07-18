<?php 
$initPaymentResponse = null;
$result = null;

function vakifbankPost3d($apiUrl, $postData) {
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$apiUrl);
    curl_setopt($ch,CURLOPT_POST,TRUE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type"=>"application/x-www-form-urlencoded"));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);

    $resultXml = curl_exec($ch);
    $errorCurl = curl_error($ch);
    curl_close($ch);

    if ($errorCurl){
        echo "cURL Error #:" . $errorCurl;
        exit();
    }

    $result = vakifbankReadResponse($resultXml);

        // Kart 3D-Secure Programına Dahil
        //echo "Status: ".$result["Status"];
        //echo "<hr>".$result["ACSUrl"];
        //header("Access-Control-Allow-Origin:".$result['ACSUrl']);
    if($result["Status"]=="Y") {
        $initPaymentResponse = '<form name="downloadForm" action="'. $result['ACSUrl'] .'" method="POST">
                                    <div role="status" class="w-full h-56 flex items-center justify-center">
                                        <div class="w-full h-full animate-pulse rounded-lg bg-gray-300 p-6">
                                            <h1 class="text-center">3-D Secure İşleminiz yapılıyor</h1>
                                            <noscript>
                                                <h2 class="text-center">Tarayıcınızda Javascript kullanımı engellenmiştir.</h2>
                                                <h3 class="text-center">3D-Secure işleminizin doğrulama aşamasına geçebilmek için Gönder butonuna basmanız gerekmektedir</h3>
                                                <div class="text-center">
                                                    <input type="submit" value="Gönder" />
                                                </div>
                                            </noscript>
                                        </div>
                                    </div>
                                    <input type="hidden" name="PaReq" value="'. $result['PaReq'] .'" />
                                    <input type="hidden" name="TermUrl" value="'. $result['TermUrl'] .'" />
                                    <input type="hidden" name="MD" value="'. $result['MerchantData'] .'" />
                                    <script>
                                    window.addEventListener("DOMContentLoaded", function() {
                                        document.forms["downloadForm"].submit();
                                    });
                                    </script>';
    } else {
        $initPaymentResponse = "3D-Secure Verify Enrollment Sonucu :".$result["Status"].": ".$result["MessageErrorCode"];
    }

    return $initPaymentResponse;
}

function vakifbankReadResponse($result){
    $resultDocument = new DOMDocument();
    $resultDocument->loadXML($result);

    //Status Bilgisi okunuyor
    $statusNode = $resultDocument->getElementsByTagName("Status")->item(0);
    $status = "";
    if( $statusNode != null )
        $status = $statusNode->nodeValue;

    //PAReq Bilgisi okunuyor
    $PAReqNode = $resultDocument->getElementsByTagName("PaReq")->item(0);
    $PaReq = "";
    if( $PAReqNode != null )
        $PaReq = $PAReqNode->nodeValue;

    //ACSUrl Bilgisi okunuyor
    $ACSUrlNode = $resultDocument->getElementsByTagName("ACSUrl")->item(0);
    $ACSUrl = "";
    if( $ACSUrlNode != null )
        $ACSUrl = $ACSUrlNode->nodeValue;

    //Term Url Bilgisi okunuyor
    $TermUrlNode = $resultDocument->getElementsByTagName("TermUrl")->item(0);
    $TermUrl = "";
    if( $TermUrlNode != null )
        $TermUrl = $TermUrlNode->nodeValue;

    //MD Bilgisi okunuyor
    $MDNode = $resultDocument->getElementsByTagName("MD")->item(0);
    $MD = "";
    if( $MDNode != null )
        $MD = $MDNode->nodeValue;

    //MessageErrorCode Bilgisi okunuyor
    $messageErrorCodeNode = $resultDocument->getElementsByTagName("MessageErrorCode")->item(0);
    $messageErrorCode = "";
    if( $messageErrorCodeNode != null )
        $messageErrorCode = $messageErrorCodeNode->nodeValue;

    // Sonuç dizisi oluşturuluyor
    $result = array
    (
        "Status"=>$status,
        "PaReq"=>$PaReq,
        "ACSUrl"=>$ACSUrl,
        "TermUrl"=>$TermUrl,
        "MerchantData"=>$MD	,
        "MessageErrorCode"=>$messageErrorCode
    );
  
    return $result;
} 
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/src/payment/iyzipay/config.php';

function initializeIyzipayPaymentForm($conversionId, $callBackUrl, $name, $surname, $phoneNumber, $email, $idenityNumber, $address, $ip) {
    try {
        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($conversionId);
        $request->setPrice("1");
        $request->setPaidPrice("24587.14");
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId("B67832");
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl($callBackUrl);
        $request->setEnabledInstallments(array(2, 3, 6, 9, 12));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId("BY789");
        $buyer->setName($name);
        $buyer->setSurname($surname);
        $buyer->setGsmNumber($phoneNumber);
        $buyer->setEmail($email);
        $buyer->setIdentityNumber($idenityNumber);
        $buyer->setLastLoginDate("2015-10-05 12:43:35");
        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress($address);
        $buyer->setIp($ip);
        $buyer->setCity("Istanbul");
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName("Jane Doe");
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($name . ' ' . $surname);
        $billingAddress->setCity("Istanbul");
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress($address);
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);

        $basketItems = array();

        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId("BI101");
        $firstBasketItem->setName("Game code");
        $firstBasketItem->setCategory1("Game");
        $firstBasketItem->setCategory2("Online Game Items");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $firstBasketItem->setPrice("1");
        $basketItems[0] = $firstBasketItem;

        $request->setBasketItems($basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, Config::options());

        if ($checkoutFormInitialize -> getStatus() != 'success') {
            return $checkoutFormInitialize -> getErrorMessage();
        }

        return $checkoutFormInitialize -> getPaymentPageUrl() . '&iframe=true';
        
    } catch (\Exception $e) {
        return 'Ödeme formu oluşturulurken bir hata oluştu';
    }
}
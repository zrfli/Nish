<?php

namespace Gosas\Core;

require_once('../../../src/payment/garanti/core/entity/PaymentRequest.php');
require_once('../../../src/payment/garanti/core/entity/Terminal.php');
require_once('../../../src/payment/garanti/core/entity/Customer.php');
require_once('../../../src/payment/garanti/core/entity/Order.php');
require_once('../../../src/payment/garanti/core/entity/Transaction.php');
require_once('../../../src/payment/garanti/core/entity/Recurring.php');
require_once('../../../src/payment/garanti/core/entity/RecurringPayment.php');
require_once('../../../src/payment/garanti/core/entity/ThreeDPayment.php');
require_once('../../../src/payment/garanti/core/enums/CurrencyCode.php');
require_once('../../../src/payment/garanti/core/enums/MotoInd.php');
require_once('../../../src/payment/garanti/core/enums/TransactionType.php');
require_once('../../../src/payment/garanti/core/enums/CardholderPresentCode.php');
require_once('../../../src/payment/garanti/core/enums/RequestMode.php');
require_once('../../../src/payment/garanti/core/settings/PosSettings.php');
require_once('../../../src/payment/garanti/core/helpers/XmlHelper.php');
require '../../../../vendor/autoload.php';

use Gosas\Core\Entity\CreditCard;
use Gosas\Core\Entity\PaymentRequest;
use Gosas\Core\Entity\Terminal;
use Gosas\Core\Entity\Customer;
use Gosas\Core\Entity\Order;
use Gosas\Core\Entity\Transaction;
use Gosas\Core\Entity\Recurring;
use Gosas\Core\Entity\RecurringPayment;
use Gosas\Core\Enums\CurrencyCode;
use Gosas\Core\Enums\MotoInd;
use Gosas\Core\Enums\TransactionType;
use Gosas\Core\Enums\CardholderPresentCode;
use Gosas\Core\Enums\RequestMode;
use Gosas\Core\Settings\PosSettings;
use Gosas\Core\Entity\ThreeDPayment;
use Gosas\Core\Helpers\XmlHelper;
use GuzzleHttp\Client;

/**
 * Standart payment process methods class
 */
class GarantiPaymentProcess
{
    /**
     * Fields
     */
    public PosSettings $settings;
    public PaymentRequest $request;
    public Order $order;
    public ThreeDPayment $threeDPayment;

    /**
     * Ctor
     */
    public function __construct()
    {
        $this->settings = new PosSettings(RequestMode::Test);
        $this->request = new PaymentRequest();
        $this->order = new Order();
    }

    /**
     * @return payment xml post data
     */
    public function PreparePayment($card, $amount)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareTransaction(TransactionType::Sales, $amount);

        $requestData = $this->CreatePaymentRequestDataXml($this->request);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }

    public function PreparePreAuthPayment($card, $amount)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareTransaction(TransactionType::PreAuth, $amount);

        $requestData = $this->CreatePreAuthPaymentRequestDataXml($this->request);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }

    public function PreparePostAuthPayment($amount, $orderId)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->settings->provUserId = "PROVAUT";

        $this->PrepareTerminal(null, $amount, $orderId);
        $this->PrepareCustomer();
        $this->PrepareTransaction(TransactionType::PostAuth, $amount);

        $requestData = $this->PostAuthPaymentRequestDataXml($this->request, $orderId);
        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");
        return $xml;
    }

    public function PreparePartialCancelPayment($amount, $orderId, $retRefNum)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->settings->provUserId = "PROVRFN";

        $this->PrepareTerminal(null, $amount, $orderId);
        $this->PrepareCustomer();
        $this->PrepareTransaction(TransactionType::PartialVoid, $amount);

        $requestData = $this->PartialCancelPaymentRequestDataXml($this->request, $orderId, $retRefNum);
        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");
        return $xml;
    }
    /**
     * @return payment bonus xml post data
     */
    public function PreparePaymentWithBonus($card, $amount, $rewardInfo)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareTransaction(TransactionType::Sales, $amount);
        $this->PrepareRewardInfo($rewardInfo);

        $requestData = $this->CreatePaymentWithBonusRequestDataXml($this->request);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }
    /**
     * @return payment installment xml post data
     */
    public function PreparePaymentWithInstallment($card, $amount, $installment)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareTransaction(TransactionType::Sales, $amount, $installment);

        $requestData = $this->CreatePaymentWithInstallmentRequestDataXml($this->request);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }
    /**
     * @return payment address xml post data
     */
    public function PreparePaymentWithAddress($card, $address, $amount, $installment)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareAddress($address);
        $this->PrepareTransaction(TransactionType::Sales, $amount, $installment);

        $requestData = $this->CreatePaymentWithAddressRequestDataXml($this->request);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }
    /**
     * @return payment order xml post data
     */
    public function PreparePaymentWithOrder($card, $address, $orderItem, $amount, $installment)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareOrderItem($orderItem);
        $this->PrepareAddress($address);

        $this->PrepareTransaction(TransactionType::Sales, $amount, $installment);

        $requestData = $this->CreatePaymentWithOrderRequestDataXml($this->request);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }

    /**
     * @return payment order comment xml post data
     */
    public function PreparePaymentWithOrderComment($card, $address, $orderItem, $orderComment, $amount, $installment)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareOrderItem($orderItem);
        $this->PrepareOrderComment($orderComment);
        $this->PrepareAddress($address);

        $this->PrepareTransaction(TransactionType::Sales, $amount, $installment);

        $requestData = $this->CreatePaymentWithOrderCommentRequestDataXml($this->request);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }

    public function PrepareCancelPayment($amount, $orderId, $retRefNum)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->settings->provUserId = "PROVRFN";

        $this->PrepareTerminal(null, $amount, $orderId);
        $this->PrepareCustomer();
        $this->PrepareTransaction(TransactionType::Void, $amount);

        $requestData = $this->CancelPaymentRequestDataXml($this->request, $orderId, $retRefNum);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }

    public function PrepareRefundPayment($amount, $orderId)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->settings->provUserId = "PROVRFN";

        $this->PrepareTerminal(null, $amount, $orderId);
        $this->PrepareCustomer();
        $this->PrepareTransaction(TransactionType::Refund, $amount);

        $requestData = $this->RefundPaymentRequestDataXml($this->request, $orderId);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }

    public function PrepareOrderInquiryPayment($card, $amount, $pageNumber)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareTransaction(TransactionType::OrderInquiry, $amount);

        $requestData = $this->CreateInquiryRequestDataXml($this->request, $pageNumber);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }

    public function PrepareOrderDetailInquiryPayment($card, $amount, $pageNumber)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareTransaction(TransactionType::OrderHistoryInquiry, $amount);

        $requestData = $this->CreateInquiryRequestDataXml($this->request, $pageNumber);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }
    public function PrepareDateRangeInquiryPayment($card, $amount, $pageNumber, $startDate, $endDate)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareTransaction(TransactionType::OrderListInq, $amount);

        $requestData = $this->CreateDateRangeInquiryPaymentRequestDataXml($this->request, $pageNumber, $startDate, $endDate);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }
    public function PrepareBonusInquiryPayment($card, $amount, $pageNumber)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareTransaction(TransactionType::BonusInq, $amount);

        $requestData = $this->CreateInquiryRequestDataXml($this->request, $pageNumber);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }
    public function PrepareDCCPayment($card, $amount, $pageNumber)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareTransaction(TransactionType::DCC, $amount);

        $requestData = $this->CreateDCCPaymentRequestDataXml($this->request, $pageNumber);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }
    public function PrepareFixedRecurringPayment($card, $amount, $pageNumber, $recurring)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareRecurring($recurring);
        $this->PrepareTransaction(TransactionType::Sales, $amount);

        $requestData = $this->CreateFixedRecurringPaymentRequestDataXml($this->request, $pageNumber);

        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }
    public function PrepareVariableRecurringPayment($card, $amount, $pageNumber, $recurring, $recurringPaymentList)
    {
        $this->request->mode = $this->settings->requestMode;
        $this->request->version = $this->settings->version;

        $this->PrepareTerminal($card->cardNumber, $amount);
        $this->PrepareCustomer();
        $this->PrepareCreditCard($card);
        $this->PrepareOrder();
        $this->PrepareRecurring($recurring);
        $this->PrepareRecurringPaymentList($recurringPaymentList);
        $this->PrepareTransaction(TransactionType::Sales, $amount);

        $requestData = $this->CreateVariableRecurringPaymentRequestDataXml($this->request, $pageNumber);
        print "data:</br>" . json_encode($requestData) . "</br>";
        $xml = XmlHelper::ArrayToXml($requestData, "<GVPSRequest/>");

        return $xml;
    }
    /**
     * @return response object for payment
     */
    public function Pay($postData)
    {
        return $this->Send($postData);
    }

    public function Cancel($postData)
    {
        return $this->Send($postData);
    }

    public function Refund($postData)
    {
        return $this->Send($postData);
    }

    public function Send($data)
    {
        $client =  new Client;

        $response = $client->request('POST', $this->settings->GetRequestUrl(), [
            'body' => $data,
        ]);

        $data = XmlHelper::XMLStringToObject($response->getBody()->getContents());
        return $data;
    }

    public function CreateDCCPaymentRequestDataXml($request, $pageNumber)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'ListPageNum'           => $pageNumber,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }
    public function CreateFixedRecurringPaymentRequestDataXml($request, $pageNumber)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId,
                'Recurring' => [
                    'Type'        => $request->order->recurring->type,
                    'TotalPaymentNum'        => $request->order->recurring->totalPaymentNum,
                    'FrequencyType'    => $request->order->recurring->frequencyType,
                    'FrequencyInterval'        => $request->order->recurring->frequencyInterval,
                    'StartDate'    => $request->order->recurring->startDate
                ],
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'ListPageNum'           => $pageNumber,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }
    public function CreateVariableRecurringPaymentRequestDataXml($request, $pageNumber)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId,
                'Recurring' => [
                    'Type'        => $request->order->recurring->type,
                    'TotalPaymentNum'        => $request->order->recurring->totalPaymentNum,
                    'FrequencyType'    => $request->order->recurring->frequencyType,
                    'FrequencyInterval'        => $request->order->recurring->frequencyInterval,
                    'StartDate'    => $request->order->recurring->startDate,
                    'PaymentList' => [
                        'Payment' => [
                            [
                                'Amount' => '10000',
                                'PaymentNum' => '1'
                            ],
                            [
                                'Amount' => '11000',
                                'PaymentNum' => '2'
                            ]
                        ]
                    ]
                ],
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'ListPageNum'           => $pageNumber,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }
    public function CreateInquiryRequestDataXml($request, $pageNumber)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'ListPageNum'           => $pageNumber,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }

    public function CreateDateRangeInquiryPaymentRequestDataXml($request, $pageNumber, $startDate, $endDate)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId,
                'StartDate'   => $startDate,
                'EndDate'     => $endDate
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'ListPageNum'           => $pageNumber,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }
    /**
     * @return array for payment xml data
     */
    public function CreatePaymentRequestDataXml($request)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }

    /**
     * @return array for pre auth payment request data
     */
    public function CreatePreAuthPaymentRequestDataXml($request)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }

    /**
     * @return array for payment bonus xml data
     */
    public function CreatePaymentWithBonusRequestDataXml($request)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd,
                'RewardList' => [
                    'Reward' => [
                        'Type'          => $request->transaction->reward->type,
                        'UsedAmount'    => $request->transaction->reward->usedAmount,
                        'GainedAmount'  => $request->transaction->reward->gainedAmount
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array for payment installament xml data
     */
    public function CreatePaymentWithInstallmentRequestDataXml($request)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd,
                'InstallmentCnt'               => $request->transaction->installment
            ],
        ];
    }

    /**
     * @return array for payment address xml data
     */
    public function CreatePaymentWithAddressRequestDataXml($request)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId,
                'AddressList' => [
                    'Address' => [
                        'Type'        => $request->order->address->addressType,
                        'Name'        => $request->order->address->firstName,
                        'LastName'    => $request->order->address->lastName,
                        'Text'        => $request->order->address->addressText,
                        'District'    => $request->order->address->district,
                        'City'        => $request->order->address->city,
                        'PhoneNumber' => $request->order->address->phoneNumber
                    ],
                ],
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd,
                'InstallmentCnt'               => $request->transaction->installment
            ],
        ];
    }

    /**
     * @return array for payment order xml data
     */
    public function CreatePaymentWithOrderRequestDataXml($request)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId,
                'ItemList' => [
                    'Item' => [
                        'Number'        => $request->order->item->number,
                        'ProductID'     => $request->order->item->productId,
                        'ProductCode'   => $request->order->item->productCode,
                        'Quantity'      => $request->order->item->quantity,
                        'Price'         => $request->order->item->price,
                        'TotalAmount'   => $request->order->item->totalAmount
                    ],
                ],
                'AddressList' => [
                    'Address' => [
                        'Type'        => $request->order->address->addressType,
                        'Name'        => $request->order->address->firstName,
                        'LastName'    => $request->order->address->lastName,
                        'Text'        => $request->order->address->addressText,
                        'District'    => $request->order->address->district,
                        'City'        => $request->order->address->city,
                        'PhoneNumber' => $request->order->address->phoneNumber
                    ],
                ],
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd,
                'InstallmentCnt'        => $request->transaction->installment
            ],
        ];
    }

    /**
     * @return array for payment comment xml data
     */
    public function CreatePaymentWithOrderCommentRequestDataXml($request)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Card'        => [
                'Number'     => $request->creditCard->cardNumber,
                'ExpireDate' => $request->creditCard->GetExpireInfo(),
                'CVV2'       => $request->creditCard->cvv,
            ],
            'Order'       => [
                'OrderID'     => $request->order->orderId,
                'GroupID'     => $request->order->groupId,
                'ItemList' => [
                    'Item' => [
                        'Number'        => $request->order->item->number,
                        'ProductID'     => $request->order->item->productId,
                        'ProductCode'   => $request->order->item->productCode,
                        'Quantity'      => $request->order->item->quantity,
                        'Price'         => $request->order->item->price,
                        'TotalAmount'   => $request->order->item->totalAmount
                    ],
                ],
                'AddressList' => [
                    'Address' => [
                        'Type'        => $request->order->address->addressType,
                        'Name'        => $request->order->address->firstName,
                        'LastName'    => $request->order->address->lastName,
                        'Text'        => $request->order->address->addressText,
                        'District'    => $request->order->address->district,
                        'City'        => $request->order->address->city,
                        'PhoneNumber' => $request->order->address->phoneNumber
                    ],
                ],
                'CommentList' => [
                    'Comment' => [
                        'Number'        => $request->order->comment->number,
                        'Text'     => $request->order->comment->commentText
                    ],
                ],
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd,
                'InstallmentCnt'        => $request->transaction->installment
            ],
        ];
    }
    public function CancelPaymentRequestDataXml($request, $orderId, $retRefNum)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Order'       => [
                'OrderID' => $orderId,
                'GroupID' => '',
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'OriginalRetrefNum'     => $retRefNum,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }

    public function RefundPaymentRequestDataXml($request, $orderId)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Order'       => [
                'OrderID' => $orderId,
                'GroupID' => '',
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }
    public function PartialCancelPaymentRequestDataXml($request, $orderId, $retRefNum)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Order'       => [
                'OrderID' => $orderId,
                'GroupID' => '',
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'ListPageNum'           => 0,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'OriginalRetrefNum'     => $retRefNum,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }
    public function PostAuthPaymentRequestDataXml($request, $orderId)
    {
        return [
            'Mode'        => $request->mode,
            'Version'     => $request->version,
            'Terminal'    => [
                'ProvUserID' => $request->terminal->provUserId,
                'UserID'     => $request->terminal->userId,
                'HashData'   => $request->terminal->hashData,
                'ID'         => $request->terminal->id,
                'MerchantID' => $request->terminal->merchantId,
            ],
            'Customer'    => [
                'IPAddress'    =>  $request->customer->ipAddress,
                'EmailAddress' =>  $request->customer->emailAddress,
            ],
            'Order'       => [
                'OrderID' => $orderId,
                'GroupID' => '',
            ],
            'Transaction' => [
                'Type'                  => $request->transaction->type,
                'ListPageNum'           => 0,
                'Amount'                => $request->transaction->amount,
                'CurrencyCode'          => $request->transaction->currencyCode,
                'CardholderPresentCode' => $request->transaction->cardholderPresentCode,
                'MotoInd'               => $request->transaction->motoInd
            ],
        ];
    }
    /** 
     * @return string
     * This method generate hashed password
     */
    public function GenerateSecurityData($data)
    {
        $shaData =  strtoupper(sha1(implode('', $data)));

        return $shaData;
    }

    /**
     * @return string
     * This method generate hash data for xml payment
     */
    public function GenerateHashData($orderId, $terminalId, $cardNumber = null, $amount, $currencyCode)
    {
        $password = $this->settings->provUserPassword;

        $data = [
            $password,
            str_pad((int)$terminalId, 9, 0, STR_PAD_LEFT)
        ];

        $hashedPassword = $this->GenerateSecurityData($data);

        $hashedData = [
            $orderId, $terminalId, $cardNumber, $amount, $currencyCode, $hashedPassword
        ];

        $shaData = strtoupper(hash("sha512", implode('', $hashedData)));

        return $shaData;
    }

    public function PrepareThreeDPayment($orderId, $amount, $currencyCode, $installment, $type)
    {
        $password = $this->settings->provUserPassword;
        $terminalId = $this->settings->terminalId;

        $data = [
            $password,
            str_pad((int)$terminalId, 9, 0, STR_PAD_LEFT)
        ];

        $hashedPassword = $this->GenerateSecurityData($data);

        $hashedDataArr = [
            $terminalId, $orderId, $amount, $currencyCode,
            $this->settings->threeDPaymentResultUrl, $this->settings->threeDPaymentResultUrl,
            $type, $installment,
            $this->settings->storeKey, $hashedPassword
        ];

        $shaData = strtoupper(hash("sha512", implode('', $hashedDataArr)));

        $this->threeDPayment = new ThreeDPayment();

        $this->threeDPayment->currency = $currencyCode;
        $this->threeDPayment->type = $type;
        $this->threeDPayment->orderId = $orderId;
        $this->threeDPayment->errorUrl = $this->settings->threeDPaymentResultUrl;
        $this->threeDPayment->successUrl = $this->settings->threeDPaymentResultUrl;
        $this->threeDPayment->storeKey = $this->settings->storeKey;
        $this->threeDPayment->hashedPassword = $hashedPassword;
        $this->threeDPayment->hashedData = $shaData;

        return $this->threeDPayment;
    }
    /**
     * prepare customer object model
     */
    public function PrepareCustomer()
    {
        $this->request->customer = new Customer(
            $this->settings->emailAddress,
            $this->settings->ipAddress
        );
    }

    /**
     * prepare order object model
     */
    public function PrepareOrder()
    {
        $this->request->order = $this->order;
    }
    /**
     * prepare order item object model
     */
    public function PrepareOrderItem($orderItem)
    {
        $this->request->order->item = $orderItem;
    }
    /**
     * prepare order comment object model
     */
    public function PrepareOrderComment($orderComment)
    {
        $this->request->order->comment = $orderComment;
    }
    /**
     * prepare transaction object model
     */
    public function PrepareTransaction($transactionType, $amount, $installment = 0)
    {
        $this->request->transaction = new Transaction(
            $transactionType,
            $amount,
            CurrencyCode::TL,
            CardholderPresentCode::Normal,
            MotoInd::ECommerce,
            $installment
        );
    }
    /**
     * prepare reward info object model
     */
    public function PrepareRewardInfo($reward)
    {
        $this->request->transaction->reward = $reward;
    }
    /**
     * prepare credit card object model
     */
    public function PrepareCreditCard($card)
    {
        $this->request->creditCard = $card;
    }
    public function PrepareRecurring($recurring)
    {
        $this->request->order->recurring = $recurring;
    }
    public function PrepareRecurringPaymentList($recurringPaymentList)
    {
        $this->request->order->recurring->paymentList = $recurringPaymentList;
    }
    /**
     * prepare order address
     */
    public function PrepareAddress($address)
    {
        $this->request->order->address = $address;
    }
    /**
     * prepare terminal object model
     */
    public function PrepareTerminal($cardNumber, $amount, $orderId = null)
    {
        $this->request->terminal = new Terminal(
            $this->settings->provUserId,
            $this->settings->userId,
            $this->settings->terminalId,
            $this->settings->merchantId,
            $this->GenerateHashData(
                $orderId != null ?  $orderId : $this->order->orderId,
                $this->settings->terminalId,
                $cardNumber,
                $amount,
                CurrencyCode::TL
            )
        );
    }
}

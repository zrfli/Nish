<?php

namespace Gosas\Core\Enums;

abstract class TransactionType
{
    const Sales = "sales";
    const Void = "void";
    const Refund = "refund";
    const PreAuth = "preauth";
    const PostAuth = "postauth";
    const PartialVoid ="partialvoid";
    const OrderInquiry = "orderinq";
    const OrderHistoryInquiry = "orderhistoryinq";
    const OrderListInq="orderlistinq";
    const BonusInq="rewardinq";
    const DCC = "dccinq";
}

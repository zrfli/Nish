<?php

namespace Gosas\Core\Entity;

use Gosas\Core\Entity\Terminal;
use Gosas\Core\Entity\Customer;
use Gosas\Core\Entity\CreditCard;
use Gosas\Core\Entity\Order;
use Gosas\Core\Entity\Transaction;

class PaymentRequest
{
    public string $mode;
    public string $version;

    public Terminal $terminal;
    public Customer $customer;
    public CreditCard $creditCard;
    public Order $order;
    public Transaction $transaction;
}

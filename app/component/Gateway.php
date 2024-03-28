<?php

namespace uranium\component\payment;

use uranium\component\payment\Payment;
use uranium\component\Cache;

class Gateway{
    private Payment $_payment;
    public function __construct(Private Payment $payment){
        $this->_payment = $payment;
    }
}

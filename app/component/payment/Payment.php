<?php

namespace uranium\component\payment;

enum PaymentStatusType {
    case PAID;
    case UNPAID;
    case HOLD;
}

class Payment {
    private String $_amount;
    private String $_reference;
    private String $_description;
    private PaymentStatusType $_status;

    public function __construct(String $amount, String $reference, PaymentStatusType $status){
        echo "test";
    }

    public function setStatus(PaymentStatusType $newStatus){
        $this->_status = $newStatus;
    }

    public function setAmount(){
        
    }
}

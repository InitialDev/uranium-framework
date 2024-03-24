<?php

namespace uranium\component\payment;

use uranium\component\payment\Payment;
use uranium\component\Cache;

class Gateway{
    private Payment $_payment;
    public function __construct(Private Payment $payment){
        $this->_payment = $payment;
    }

    public static function getConversionRate(String $currency):String {
        $conversionRate = Cache::get("conversion_rate_".$currency);
        if(is_null($conversionRate)){
            self::updateConversionRates();
            $conversionRate = Cache::get("conversion_rate_".$currency);
            if(is_null($conversionRate)){
                $conversionRate = 1;
            };
        };
        return $conversionRate;
    }

    public static function updateConversionRates():Void {
        // TODO: Call bitcoin ticker and update cache
    }
}

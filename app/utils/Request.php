<?php
namespace uranium\utils;

class Request{
    public static function sanitize($input){
        return (htmlspecialchars($input));
        //return $input;
    }

    public static function generateCSRF(){
        if(session_id() === "") session_start();
        $passwordGen = password_hash(session_id(), PASSWORD_DEFAULT);
        return base64_encode($passwordGen);
    }

    public static function checkCSRF($hash){
        if(session_id() === "") session_start();
        $password = base64_decode($hash);
        return password_verify(session_id(), $password);
    }
}
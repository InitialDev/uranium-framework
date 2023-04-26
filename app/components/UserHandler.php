<?php

namespace uranium\component;

use uranium\config\db;
use EmailValidation\EmailValidatorFactory as emailValidator;
use uranium\boiler\enc;
use \PDO;

class UserHandler{
    public static function authenticate($username, $password){
        
    }

    public static function isAuthenticated(){
        if(isset($_SESSION['uid']) && !empty($_SESSION['uid'])){
            return true;
        }else{
            return false;
        };
    }

    public static function getAuthenticatedUser(){
        if(!self::isAuthenticated()){
            return false;
        }
        $uid = $_SESSION['uid'];
        return getUser($uid);
    }

    public static function getUser($uid){

    }

    public static function status(){
        return true;
    }
}

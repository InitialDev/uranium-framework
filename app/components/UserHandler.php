<?php

namespace uranium\component;

use uranium\config\db;
use EmailValidation\EmailValidatorFactory as emailValidator;
use uranium\boiler\enc;
use uranium\model\Session;
use uranium\model\userModel;
use \PDO;

class UserHandler{
    public static function authenticate($username, $password){
        error_log($username." ".$password);
    }

    public static function isAuthenticated(){
        if(isset($_SESSION["token"])){
            return self::authenticateToken($_SESSION["token"]);
        };
        return false;
    }

    public static function getAuthenticatedUser(){
        if(!self::isAuthenticated()){
            return false;
        }
        $token = $_SESSION['token'];
        return self::getUser($token);
    }

    public static function getUser(string $token): array{
        $userid = self::getUserIdFromToken($token);
        $userModel = new userModel();
        $result = $userModel->where("id", $userid)->get()->getResults();
        return array_pop($result);
    }

    public static function getUserIdFromToken(string $token): String{
        $sessionModel = new Session();
        $results = $sessionModel->where("token", $token)->get()->getResults();
        $session = array_pop($results);
        return $session["userId"];
    }

    public static function status(){
        return true;
    }

    public static function authenticateToken(string $token){
        $sessionModel = new Session();
        $results = $sessionModel->where("token", $token)->get()->getResults();
        if(count($results) > 0){
            return true;
        };
        return false;
    }
}

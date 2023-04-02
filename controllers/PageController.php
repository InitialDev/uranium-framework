<?php

use uranium\core\pageHandler;

class PageController{
    public static function index(){
        pageHandler::view("root");
    }
    public static function login(){
        pageHandler::view("login");
    }
}

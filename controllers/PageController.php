<?php

use uranium\core\pageHandler;

class PageController{
    public static function login(){
        pageHandler::view("login");
    }
}
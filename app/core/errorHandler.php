<?php

namespace uranium\core;

class errorHandler{
    public static function log_error( $num, $str, $file, $line, $context = null ){
        log_exception( new ErrorException( $str, 0, $num, $file, $line ) );
    }
    
    public static function log_exception( $e ){
        error_log("An error!");
        echo($e);
        error_log($e);
        echo "<br />";
        echo "<br />";
       
        exit();
    }
    
    public static function check_for_fatal(){
        $error = error_get_last();
        if ( !is_null($error) && array_key_exists("type", $error) && $error["type"] == E_ERROR )
            log_error( $error["type"], $error["message"], $error["file"], $error["line"] );
    }
}
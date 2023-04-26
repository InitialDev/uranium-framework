<?php

namespace uranium\core;

class ErrorHandler{
    public static function log_error( $num, $str, $file, $line, $context = null ){
		self::handleError( new \ErrorException($str, 0 , $num, $file, $line));
    }

	public static function log_exception($num){
		self::handleError(new \ErrorException($str, 0, $num, $file, $line));
	}
    
    public static function handleError( $str ){
        error_log("An error!");
		echo("AN ERROR!<br />");
        echo(str_replace(PHP_EOL, '<br />', $str));
        error_log($str);
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

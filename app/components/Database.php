<?php

namespace uranium\component;

use \PDO;

class Database {
    private static $objInstance;
    private function __construct() {}
    private function __clone() {}
    
    public static function getInstance() {
        global $_CONFIG;
        if(!self::$objInstance){
            $db_host = $_CONFIG["db_host"];
            $db_name = $_CONFIG["db_name"];	
            $dsn = "mysql:dbname=$db_name;host=$db_host";
            self::$objInstance = new PDO($dsn, $_CONFIG["db_user"], $_CONFIG["db_pass"]);
            self::$objInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$objInstance;
    }

    final public static function __callStatic( $chrMethod, $arrArguments ) {
        $objInstance = self::getInstance();
        return call_user_func_array(array($objInstance, $chrMethod), $arrArguments);
    }
}

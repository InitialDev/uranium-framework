<?php
require __DIR__."/app/app.php";

use uranium\scriptloader;
use uranium\core\router;
use uranium\core\errorHandler;

register_shutdown_function(array('uranium\core\errorHandler', 'check_for_fatal'));
set_error_handler(array('uranium\core\errorHandler', 'log_error'));
//set_exception_handler(array('uranium\core\errorHandler', 'log_exception'));

ini_set("display_errors", "off");
error_reporting(E_ALL);

scriptloader::folder(__DIR__."/scripts");
router::init();

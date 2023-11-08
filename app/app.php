<?php

require_once(__DIR__."/ScriptLoader.php");

use uranium\ScriptLoader;

$_SERVER['URANIUM_ENV_CONFIG_PATH'] = __DIR__."/../.env";

ScriptLoader::folder(__DIR__."/utils");
ScriptLoader::file(__DIR__."/core/Route.php");
ScriptLoader::file(__DIR__."/core/RouteRegister.php");
ScriptLoader::file(__DIR__."/../routes.php");
ScriptLoader::folder(__DIR__."/core");
ScriptLoader::folder(__DIR__."/../models");
ScriptLoader::folder(__DIR__."/components");
ScriptLoader::folder(__DIR__."/middleware");
ScriptLoader::folder(__DIR__."/../controllers");
ScriptLoader::folder(__DIR__."/cli");

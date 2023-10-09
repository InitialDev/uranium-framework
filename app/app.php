<?php

require_once(__DIR__."/ScriptLoader.php");

use uranium\ScriptLoader;

ScriptLoader::file(__DIR__."/../env.php");
ScriptLoader::file(__DIR__."/../routes.php");
ScriptLoader::folder(__DIR__."/core");
ScriptLoader::folder(__DIR__."/cli");
ScriptLoader::folder(__DIR__."/components");
ScriptLoader::folder(__DIR__."/../models");
ScriptLoader::folder(__DIR__."/../controllers");

<?php
namespace uranium\core;

use uranium\core\routes;

class Router{

    public static function init(){
        $route = self::getRoute();
        if($route){
            self::loadRoute($route["route"], $route['variables']);
        }else{
            echo "404";
            error_log("[*] Router: Route not found");
        }
    }

    private static function getRouteList(){
        $routeRegister = new routes();
        $routes = $routeRegister->getRegister();

        $method = $_SERVER["REQUEST_METHOD"];
        if(array_key_exists($method, $routes)){
            return $routes[$method];
        }
        return [];
    }

    private static function getRoute(){
        $URI = $_SERVER['REQUEST_URI'];
        $URIComps = explode("/", $URI);
        if($URIComps[0] == ""){
            $URIComps = array_slice($URIComps, 1); // Remove first empty item
        };
        $compSize = count($URIComps);
        $matches = self::getRouteList();
        $variables = [];
        $level = 0; 
        foreach($URIComps as $URIComp){
            foreach(self::getRouteList() as $routePath=>$route){
                $routeComps = explode("/", $routePath); 
                if($routeComps[0] == ""){
                    $routeComps = array_slice($routeComps, 1); // Remove empty array item
                };
                $routeSize = count($routeComps);
                if($compSize != $routeSize){
                    unset($matches[$routePath]);
                }else{
                    $variableMatch = preg_match("/\{([a-zA-Z_]+)\}/", $routeComps[$level], $variableMatch);
                    if($routeComps[$level] != $URIComp){
                        if(!$variableMatch){
                            unset($matches[$routePath]);
                        }else{
                            $variableID = substr($routeComps[$level], 1, strlen($routeComps[$level]) -2); 
                            $variables[$variableID] = $URIComp;
                        };
                    };
                };
            };
            $level += 1;
        };
        if(count($matches) > 0){
            return [
                "route" 	=> array_pop($matches),
                "variables" => $variables
            ];
        }else{
            return false;
        };
    }

    private static function loadRoute($route, $variables=[]){

        if($route->hasMiddleware()){
            //error_log("Has middleware");
           // var_dump($route->middleware);
            foreach($route->middleware as $middlewareClassString){
                $middlewareClass = new $middlewareClassString();
                $middlewareClass::handle();
            }
        };

        $routeID = $route->getHandler();
        $splitRoute = explode("@", $routeID);
        $class = new $splitRoute[0];
        $function = $splitRoute[1];
        $class::$function($variables);
    }
}

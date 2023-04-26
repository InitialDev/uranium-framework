<?php

namespace uranium\core;

class ConfigHandler{

    private $_PATH = "";

    public function __construct($config){
        $this->loadConfig($config);
    }

    protected function loadConfig($path){
        $content = $this->getFileContent($path);
        $configLines = preg_split("/\r\n|\n|\r/", $content); 
        foreach($configLines as $line){
            $splitComment = preg_split("/#/", $line);
            $notComment = $splitComment[0];
            if(!empty($notComment)){
                $splitItem = explode("=", $notComment);
                global $_CONFIG;
                $_CONFIG[$splitItem[0]] = $splitItem[1];
            }
        }
    }

    private function getFileContent($path){
        if(!file_exists($path)){
            throw new \Exception(".env does not exist");
        }
        $content = file_get_contents($path);
        if($content === false){
            return "";
        };
        return $content;
    }
}
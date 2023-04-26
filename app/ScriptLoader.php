<?php
namespace uranium;

class ScriptLoader{

    /**
     * Load single file
     * @param String file to load
     * @return bool if loaded
     */
    public static function file($FILEPATH = ""){
        if(strlen($FILEPATH) <= 0){
            return false;
        };
        if(file_exists($FILEPATH)){
            require_once($FILEPATH);
            return true;
        }else{
            return false;
        };
    }

    /**
     * Load all php scripts in a folder
     * @param String path to load scripts from
     * @return bool if path found
     */
    public static function folder($FOLDERPATH = ""){
        if(strlen($FOLDERPATH) <= 0){
            return false;
        };
        if(file_exists($FOLDERPATH)){
            $filesToInclude = scandir($FOLDERPATH);
            foreach($filesToInclude as $file){
                if(!is_dir($FOLDERPATH.'/'.$file)){
                    require_once($FOLDERPATH.'/'.$file);
                }
            }
            return true;
        }else{
            return false;
        }
    }
}

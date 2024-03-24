<?php
namespace uranium\core;

use uranium\component\Database;
use \PDO;
use Throwable;

class DatabaseDataTypes{
    public const VARCHAR    = ["ID" => "VARCHAR", "MAX" => 255];
    public const INTEGER    = ["ID" => "INT", "MAX" => 100];
    public const BOOLEAN    = ["ID" => "BOOL", "LENGTH" => NULL];
    public const TEXT       = ["ID" => "TEXT", "MAX" => 2000];
    public const MEDIUMTEXT = ["ID" => "MEDIUMTEXT"];
    public const LONGTEXT   = ["ID" => "LONGTEXT"];
	public const FLOAT      = ["ID" => "FLOAT", "MAX" => 64, "LENGTH" => 8];
	public const TIMESTAMP  = ["ID" => "TIMESTAMP", "DEFAULT" => "CURRENT_TIMESTAMP", "LENGTH" => 6];
}

class Model extends DatabaseDataTypes{

    public Bool $test = false; // set to test to not run query

    public $cols = array();  	// Array of data columns
    public $rows = array(); 	// Array of data from database
    protected $tableName;	
    protected $pkn;				// Primary key name
    private $withProtected = false; 
    private	$query = ["selectors" => array(),
                      "relationships" => array()];		// Build a query

    public function __construct(){
        $this->setupQuery(); // Setup the default empty query
    }

    /**
     * Valid col options
     */
    private $colOptions = [
        "name" => "",
        "type" => NULL,
        "length" => NULL,
        "default" => "",
        "key" => "",
        "null" => true,
        "extra" => false,
        "unique" => false,
        "protected" => false
    ];
    
    /**
     * Adds an auto incrementing primary key
     * 		with specified name
     * @param String - Name of the primary key
     * @return void
     */
    protected function addPrimary(String $name): void{
        $this->pkn = $name;
        $this->cols[] = [
            "name"=> $name,
            "type"=> databaseDataTypes::INTEGER,
            "key" => "PRI",
            "length" => 10,
            "extra" => "AUTO_INCREMENT",
            "null" => false,
            "default" => "",
            "unique" => true,
            "protected" => false
        ];
    }

    /**
     * setup the default empty query
     * (put here in case of change later on)
     * @return void
     */
    private function setupQuery(): void{
        $this->query = ["selectors" => array(),
                      "relationships" => array()];
    }
    
    /**
     * Add a data column to the table
     * 		values are specified by colOptions
     * @param String - name of col
     * @param Array  - array of col options
     * @return void
     */
    protected function addCol(string $name, array $options): void{
        $newcol = $this->colOptions;
        $newcol["name"] = $name;
        foreach($this->colOptions as $key=>$option){
            if(array_key_exists($key, $options)){
                $newcol[$key] = $options[$key];
            }
        }
        $this->cols[] = $newcol;
    }

    /**
     * Add where selector to query
     * @param String - column name to filter
     * @param String - value to filter
     * @return model object
     */
    public function where(String $key, String $value): Model{
        $this->query["selectors"][] = ["key" => $key, "value" => $value]; 
        return $this;
    }

    /**
     * Add where selector to a query but with array of wheres
     * @param Array - array of keys as db key and value as db value
     * @return model this
     */
    public function whereAnd(Array $attributes): Model{
        foreach($attributes as $key => $value){
            $this->where($key, $value);
        };
        return $this;
    }

    public function getSelectors(): Array{
        return $this->query["selectors"];
    }

    /**
     * add a limit to ammount of rows to fetch
     * @param Integer limit to rows
     * @return model object
     */
    public function limit(int $limit): Model{
        $this->query["limit"] = $limit;
        return $this;
    }

    /**
     * Setup the default relationship array
     * @param String name of class
     * @param String name of foreign key
     * @param Strin gname of local key
     * @return Array
     */
    private function setupRelationship(String $className, String $foreignKey, String $localKey): Array{
        $defaultForeignKey = $this->tableName."_id";
        $class = new $className();
        $newRelationship = [
            "class"			=> new $className(),
            "localKey" 		=> ($localKey!=null)?$localKey:$this->pkn,
            "foreignKey"	=> ($foreignKey!=null)?$foreignKey:$defaultForeignKey
        ];
        return $newRelationship;
    }

    /**
     * Add a one-to-one relationship
     * @param String full class name with namespace
     * @param String name of foreign key
     * @param String name of local key
     * @return void
     */
    public function hasOne(String $className, String $foreignKey=null, String $localKey=null): void{
        $newRelationship = $this->setupRelationship($className, $foreignKey, $localKey);
        $newRelationship["class"]->limit(1);
        $this->query["relationships"][] = $newRelationship; 
    }

    /**
     * Add a one-to-many relationship
     * @param String full class name with namespace
     * @param String name of foreign column
     * @param String name of local key
     * @return void
     */
    public function hasMany(String $className, String $foreignKey=null, String $localKey=null): void{
        $newRelationship = $this->setupRelationship($className, $foreignKey, $localKey);
        $this->query["relationships"][] = $newRelationship; 
    }  

    /**
     * Include relationship with get reponse
     * @param String name of the relationship method inside the model
     * @return Model $this
     */ 
    public function with(String $relationshipName): Model{
        try{
            $this->$relationshipName();
        }catch(Exception $e){
            error_log("Specified relationship not found");
        }
        return $this;
    }

    /**
     * Include the protected data in the response
     * @return Model $this
     */
    public function withProtected(): Model{
        $this->withProtected = true;
        return $this;
    }

    /**
     * Fetch item in database
     * @return mixed
     */
    public function get(){
        $tableName = $this->tableName;
        $sql = "SELECT ";
        $cols = $this->getColumnNames();
        $wheres = [];
        foreach($cols as $key=>$col){
            $sql .= "`".$col."`";
            if(($key+1) != count($cols)){
                $sql.=", ";
            };
        };
        $sql .= " FROM $tableName";
        if(count($this->query["selectors"]) > 0){
            $sql .= " WHERE ";
            foreach($this->query["selectors"] as $key=>$selector){
                $wheres[$selector["key"]] = $selector["value"];
                if($key >= 1)
                    $sql .= " AND ";
                $sql .= "`".$selector["key"]."`=:".$selector["key"]." ";
            };
        };
        if(key_exists("limit", $this->query)){
            $sql .= " LIMIT ".$this->query["limit"];
        };
        if(!$this->test){
            $database = Database::getInstance();
            $query = $database->prepare($sql);
            try{
                $s = $query->execute($wheres);
                if($s){
                    while($row = $query->fetch(PDO::FETCH_ASSOC)){
                        // Get relationships
                        if(count($this->query["relationships"]) > 0){
                            foreach($this->query["relationships"] as $r){
                                $relationshipModel = $r["class"];
                                $results = $relationshipModel->where($r["foreignKey"], $row[$r["localKey"]])->get()->getResults();
                                $row[$relationshipModel->tableName] = $results;
                            };
                        };
                        $this->rows[] = $row;
                    };
                };
            }catch(PDOException $e){
                echo "Error";
            }
            return $this;
        }else{
            return $sql;
        };
    }

    /**
     * Get result rows
     * @return rows array
     */
    public function getResults(){
        return $this->rows;
    }

    public function delete(){
        $tableName = $this->tableName;
        $sql = "DELETE FROM $tableName";
        if(count($this->query["selectors"]) > 0){
            $sql .= " WHERE ";
            foreach($this->query["selectors"] as $key=>$selector){
                $wheres[$selector["key"]] = $selector["value"];
                if($key >= 1)
                    $sql .= " AND ";
                $sql .= $selector["key"]." = :".$selector["key"]." ";
            };
        };
        if(!$this->test){
            $database = Database::getInstance();
            $query = $database->prepare($sql);
            foreach($wheres as $whereKey => $whereValue){
                $query->bindParam($whereKey, $whereValue);
            };
            if($query->execute()){
                $this->get();
            };
            return $this;
        }else{
            return $sql;
        };
    }

    /**
     * Get the names of the columns
     * @return array
     */
    private function getColumnNames(): array{
        $cols = array();
        foreach($this->cols as $col){
            if($this->withProtected || !$col["protected"]){
                $cols[] = $col["name"];
            };
        };
        return $cols;
    }
 
     /**
      * Save new data in rows to database
      * @return Bool status
      */
    public function save(): bool{
        if(count($this->rows) <= 0){
            return true;
        };
        $pkn = $this->pkn;
        $tableName = $this->tableName;
        $database = Database::getInstance();
        $database->beginTransaction();
        foreach($this->rows as $row){
            $variables = [];
            $template = "";
            if(array_key_exists($pkn, $row)){
                $template = "UPDATE `$tableName` SET ";
                foreach($this->cols as $col){
                    if(array_key_exists($col["name"], $row)){
                        $currentKey = $col["name"];
                        $currentValue = $row[$col["name"]];
                        $template .= "`$currentKey`=?";
                        $variables[] = $currentValue;
                    }
                }
                $template = substr($template, 0, -1);
                $currentPK = $row[$pkn];
                $template .= " WHERE `$pkn`='$currentPK'";
            }else{
                $values = "";
                $template = "INSERT INTO `$tableName` (";
                foreach($this->cols as $col){
                    if($col["key"] != "PRI"){
                        if(array_Key_exists($col["name"], $row)){
                            $currentKey = $col["name"];
                            $currentValue = $row[$col["name"]];
                            $template .= "`$currentKey`,";
                            $values .= "?,";
                            $variables[] = $currentValue;
                        }
                    }
                }
                $template = substr($template, 0, -1); // Remove the final comma from keys
                $values = substr($values, 0, -1); // And on values
                $template .= ") VALUES (".$values.");";
            };
            $query = $database->prepare($template);
            $query->execute($variables);
        };
        try{
            $database->commit();
            return true;
        }catch(PDOException $e){
            $database->rollBack();
            error_log($e);
            return false;
        }
    }
    
    /**
     * Crete the table in the database
     * @return Bool status
     */
    public function create(): bool{
        $tableName = $this->tableName;
        $template = "CREATE TABLE `$tableName`(";
        $pkName = "";
        foreach($this->cols as $col){
            if($col["key"] === "PRI"){
                $pkName = $col["name"];
            }
            $null = $col["null"]?"":"NOT NULL";
            $name = "`".$col["name"]."`";
            $type = $col["type"]["ID"];
			$default = "";
			if(array_key_exists("default", $col)){
				$default = $col["default"];
			}else{
				if(array_key_exists("DEFAULT", $col["type"])){
					$default = $col["type"]["DEFAULT"];
				};
			};
			$extra = $col["extra"]?$col["extra"]:"";

			$length = $col["length"];
            if(is_null($length)){
                if(array_key_exists("length", $col) && !is_null($col["length"])){
                    $length = "(".$col["length"].")";
                    error_log("1");
                }else if(array_key_exists("LENGTH", $col["type"]) && !is_null($col["type"]["LENGTH"])){
                    $length = "(".$col["type"]["LENGTH"].")";
                }else if(array_key_exists("MAX", $col["type"])){
                    $length = "(".$col["type"]["MAX"].")";
                }else{
                    $length = "";
                };
            }else{
                $length = "(".$length.")";
            };
    
            $template .= "$name $type$length $null";
            if(strlen($default) > 0){
                $template .= " default $default ";
            }
            if(strlen($extra) > 0){
                $template .= " $extra ";
            }
            if($col["unique"]){
                $template .= " UNIQUE "; 
            }
            $template .= ',';
        };
        $template .= "PRIMARY KEY(".$pkName.")";
        $template .= ")";
        $database = Database::getInstance();
        $query = $database->prepare($template);        
        if($query->execute()){
            return true;
        }else{
            return false;
        };
    }

    /**
     * Checks if database table exists
     * @return Boolean 
     */
    public function exists(): bool{
        $database = Database::getInstance();
        $query = $database->prepare("DESCRIBE ".$this->tableName);
        try{
            $query->execute();
            return true;
        }catch(Throwable $e){
            // Error, table probably doesnt exist.
            return false;
        }
    }

    /**
     * Drop table
     * @return Boolean
     */
    public function drop(): bool{
        $database = Database::getInstance();
        $tableName = $this->tableName;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        $query = $database->prepare($sql);
        if($query->execute()){
            return true;
        }else{
            return false;
        };
    }

    /**
     * Get tables fields from the database
     * @return Mixed
     */
    public function getExistingColumns(): mixed{
        $database = Database::getInstance();
        $tableName = $this->tableName;
        $query = $database->prepare("SHOW COLUMNS FROM `$tableName`;");
        $rows = [];
        if($query->execute()){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $rows[] = $row;
            }
            return $rows;
        }else{
            return false;
        };
    }
}

<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Database extends PDO
{
    private $connections = array(

        'sqlite' => array(
            'driver' => 'sqlite',
            'database' => null,
            'prefix' => '',
        ),

        'mysql' => array(
            'driver' => 'mysql',
            'host' => null,
            'port' => '3306',
            'database' => null,
            'username' => null,
            'password' => null,
            'unix_socket' => null,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ),

        'pgsql' => array(
            'driver' => 'pgsql',
            'host' => null,
            'port' => '5432',
            'database' => null,
            'username' => null,
            'password' => null,
            'charset' => 'utf8',
            'prefix' => '',
            // Notice the following has been modified
            'schema'   => null,
            'sslmode' => 'prefer',
        ),

        'sqlsrv' => array(
            'driver' => 'sqlsrv',
            'host' => null,
            'port' => '1433',
            'database' => null,
            'username' => null,
            'password' => null,
            'charset' => 'utf8',
            'prefix' => '',
        ),
    );
    
    private $driver=null;
    private $host=null;
    private $database=null;
    private $username=null;
    private $password=null;
    private $prefix=null;
    
    public function __construct($DB_TYPE, $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_PFIX){
        parent::__construct($DB_TYPE.':host='.$DB_HOST.';dbname='.$DB_NAME, $DB_USER, $DB_PASS);
        
        $this->driver = $DB_TYPE;
        $this->host = $DB_HOST;
        $this->database = $DB_NAME;
        $this->username = $DB_USER;
        $this->password = $DB_PASS;
        $this->prefix = $DB_PFIX;
        
    }
    
    public function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC){
        $sth = $this->prepare($sql);
        foreach($array as $key => $value){
            $sth->bindValue("$key", $value);
        }
        $sth->execute();
        return $sth->fetchAll($fetchMode);
    }
    
    public function insert($table, $data){
        ksort($data);
        $fieldNames = implode("`, `", array_keys($data));
        $fieldValues = ":".implode(", :", array_keys($data));
        $sth = $this->prepare("INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)");
        
        foreach ($data as $key => $value){
            $sth->bindValue(":$key", $value);
        }
        $sth->execute();
        
    }
    
    public function update($table, $data, $where){
        ksort($data);
        
        $fieldDetails = null;
        foreach($data as $key => $value){
            $fieldDetails .= "`$key`=:$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');
        
        $sth = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");
        
        foreach ($data as $key => $value){
            $sth->bindValue(":$key", $value);
        }
        
        $sth->execute();
    }
    
    /**
     * delete
     * 
     * @param string $table
     * @param string $where
     * @param integer $limit
     * @return interger
     */
    public function delete($table, $where, $limit = 1){
        $sth = $this->prepare("UPDATE $table SET hidden = 1 WHERE $where LIMIT $limit");
        $sth->execute();
        return $sth->rowCount();
    }
    
    public function activate($table, $where, $limit = 1){
        $sth = $this->prepare("UPDATE $table SET status = 1 WHERE $where LIMIT $limit");
    }
    
    public function deactivate($table, $where, $limit =1){
        $sth = $this->prepare("UPDATE $table SET status = 0 WHERE $where LIMIT $limit");
    }
}
?>
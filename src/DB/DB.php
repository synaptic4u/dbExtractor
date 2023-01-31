<?php

namespace Synaptic4u\DB;

use Exception;
use PDO;
use Synaptic4u\Log\Activity;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Log;

class DB
{
    private $lastinsertid = -1;
    private $rowcount = -1;
    private $conn;
    private $status;
    private $dsn;
    private $pdo;
    private $error;
    private $config;

    public function __construct($config)
    {
        $dsn = null;
        
        try {
            
            $this->config = $config;
            $this->error = null;

            $this->conn = [
                "host" => $this->config->host,
                "dbname" => $this->config->db,
                "user" => $this->config->user,
                "pass" => $this->config->password
            ];
        
            if($this->conn === null){
                throw new Exception("Something wrong with db config");
            }

            if(isset($this->config->enabled)){
                
                $dsn = 'mysql:host='.$this->conn['host'].';';
            }
            if(!isset($this->config->enabled)){
    
                $dsn = 'mysql:host='.$this->conn['host'].';dbname='.$this->conn['dbname'];
            }

            //  Create PDO instance.
            $this->pdo = new PDO($dsn, $this->conn['user'], $this->conn['pass']);

            // $this->log([
            //     'Location' => __METHOD__,
            //     'conn' => json_encode($this->conn, JSON_PRETTY_PRINT),
            // ]);
        } catch (Exception $e) {
            
            $this->error = $e->__toString();

            $this->error([
                'Location' => __METHOD__,
                'conn' => $this->conn,
                'config' => $this->config,
                'error' => $e->__toString(),
            ]);
        }
    }

    public function query($params, $sql)
    {
        try {
            $stmt = null;
            $result = [];
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);

            $this->status = ($stmt->execute($params)) ? 'true' : 'false';

            $this->lastinsertid = $this->pdo->lastInsertId();

            $this->pdo->commit();

            $this->rowcount = $stmt->rowCount();

            $result = $stmt->fetchAll();

            $stmt = null;
        } catch (Exception $e) {
            
            $this->error([
                'Location' => __METHOD__,
                'pdo->errorInfo' => $this->pdo->errorInfo(),
                'error' => $e->__toString(),
                'stmt' => $stmt,
                'sql' => $sql,
                'params' => $params,
            ]);

            $result = null;
            $stmt = null;
            $this->pdo = null;
        } finally {
            return $result;
        }
    }

    public function getTableRowCount($table){

        $count = 0;

        $sql = 'select count(*) as rowcount from '.$table.' where 1 = ?;';

        $result = $this->query([1], $sql);


        foreach ($result as $res) {
            $count = $res['rowcount'];
        }

        return $count;
    }

    public function getTablesList()
    {

        $table_list = [];
        
        $sql = 'show tables where 1=?;';

        $result = $this->query([1], $sql);

        foreach ($result as $res) {
            $table_list[] = $res[0];
        }

        return $table_list;
        // print_r(json_encode($table_list, JSON_PRETTY_PRINT).PHP_EOL);
    }

    public function getDBList($table)
    {
        $dbs = [];
        
        $sql = 'show databases where 1=?;';

        $result = $this->query([1], $sql);

        foreach ($result as $res) {
            $dbs[] = $res[0];
        }

        array_shift($dbs);

        return $dbs;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getLastId()
    {
        return $this->lastinsertid;
    }

    public function getrowCount()
    {
        return $this->rowcount;
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    public function error($msg)
    {
        new Log($msg, new Error());
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    protected function log($msg)
    {
        new Log($msg, new Activity());
    }
}
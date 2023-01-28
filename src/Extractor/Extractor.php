<?php

namespace Synaptic4u\Extractor;

use Exception;
use Synaptic4u\Log\Activity;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Log;
use Synaptic4u\DB\DB;

class Extractor
{
    private $db;
    
    public function __construct($options)
    {
        try {
            
            $this->db = new DB();
            
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);
        }
    }

    public function confirmVHostDB($vhost_detail_list){
        
    }

    public function readTablesList()
    {

        $table_list = [];
        
        $sql = 'show tables where 1=?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $table_list[] = $res[0];
        }

        return $table_list;
        // print_r(json_encode($table_list, JSON_PRETTY_PRINT).PHP_EOL);
    }

    public function readTableColumns($table)
    {
        $columns = [];
        
        $sql = 'show columns from '.$table->alias.' where 1=?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $columns[] = $res[0];
        }

        array_shift($columns);

        return $columns;
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    protected function error($msg)
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
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
    private $config;
    
    public function __construct($config)
    {
        try {
            $this->config = $config;

            if($this->config->root_db_login->enabled === true){

                $this->db = new DB($this->config);
            }
            
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);
        }
    }

    public function getTablesList()
    {
        return $this->db->getTablesList();
    }

    public function getTableColumns($table)
    {
        return $this->db->getTableColumns($table);
    }

    public function getDBList($table)
    {
        return $this->db->getDBList($table);
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
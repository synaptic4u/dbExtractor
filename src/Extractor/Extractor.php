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

            $this->db = null;

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

    public function getDataDetails($vhost_detail_list){
        try{

            foreach($vhost_detail_list as $name => $vhost){

                $this->db = new DB($vhost['vhost_web_config']);

                var_dump(get_class($this->db));

                $table_list = $this->getTablesList();
                
                $vhost_detail_list[$name]['db_details_source']['table_count'] = sizeof($table_list);
                $vhost_detail_list[$name]['db_details_source']['table_list'] = $table_list;

                foreach($table_list as $table){

                    $row_count = $this->getTableRowCount($table);

                    $vhost_detail_list[$name]['db_details_source']['tables'][] = [
                        "name" => $table,
                        "row_count" => ($row_count > 0) ? $row_count : null,
                    ];
                }
            }
            

        }catch(Exception $e){

            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);
        }finally{
            
            return $vhost_detail_list;
        }
    }

    public function getTablesList()
    {
        return $this->db->getTablesList();
    }

    public function getTableRowCount($table)
    {
        return $this->db->getTableRowCount($table);
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
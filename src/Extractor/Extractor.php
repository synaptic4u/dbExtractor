<?php

namespace Synaptic4u\Extractor;

use Exception;
use stdClass;
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

            if($this->config->db->mysql_server_creds_source->enabled == true){

                $this->db = new DB( $this->config->db->mysql_server_creds_source);
            }
        } catch (Exception $e) {

            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);
        }
    }

    public function dumpDBs(array $vhost_detail_list)
    {
        try{

            $output = null;
            $returnVar = null;

            foreach($vhost_detail_list as $name => $vhost){

                $conn1 = json_decode(json_encode($vhost['vhost_web_config']), false);
                $conn = (object) $vhost['vhost_web_config'];

                $this->log([
                    "Location" => __METHOD__,
                    "conn" => get_class($conn),
                    "conn1" => get_class($conn1),
                    "conn1" => json_encode($conn1, JSON_PRETTY_PRINT),
                    "conn" => json_encode($conn, JSON_PRETTY_PRINT),
                ]);
                
                $this->db = new DB($conn);

                if($this->db->getError() != null){
                
                    $vhost_detail_list[$name]['db_connect_error'] .= $this->db->getError();
                }else{
                    
                    $vhost_detail_list[$name]['db_connect_success'] = true;

                    $vhost_detail_list[$name]['db_dump_log_path'] = dirname(__FILE__, 2).'/logs/mysql_logs/'.str_replace("-","_", $name).'_mysql_dump.txt';
                
                    $vhost_detail_list[$name]['db_dump_path'] = dirname(__FILE__, 3).'/mysql_dumps/'.str_replace("-","_", $name).'_mysql_dump.sql';
                    
                    $cli_cmd = 'mysqldump -h'.$this->config->db->mysql_server_creds_source->host.' -u"'.$this->config->db->mysql_server_creds_source->user.'" -p"'.$this->config->db->mysql_server_creds_source->password.'"  '.$vhost['vhost_web_config']['db'].' > '.$vhost_detail_list[$name]['db_dump_path'].'';

                    exec($cli_cmd, $output, $returnVar);

                    $vhost_detail_list[$name]['db_connect_error'] .= ' -> Status: '.$returnVar.' Output: '.json_encode($output, JSON_PRETTY_PRINT);
                
                    if(file_exists($vhost_detail_list[$name]['db_dump_path'])){

                        if(filesize($vhost_detail_list[$name]['db_dump_path']) > 1){

                            $vhost_detail_list[$name]['db_dump_success'] = true;
                        }
                    }
                    if(file_exists($vhost_detail_list[$name]['db_dump_log_path'])){
                        
                        $vhost_detail_list[$name]['db_dump_log_success'] = true;
                    }
                    
                    $this->log([
                        "Location" => __METHOD__,
                        "vhost" => $name,
                        "mysql_log_file" => $vhost_detail_list[$name]['db_dump_log_path'],
                        "mysql_dump_file" => $vhost_detail_list[$name]['db_dump_path'],
                        "cli_cmd" => $cli_cmd,
                        "output" => json_encode($output, JSON_PRETTY_PRINT),
                        "returnVar" => json_encode($returnVar, JSON_PRETTY_PRINT),
                        "vhost_detail_list" => json_encode($vhost_detail_list, JSON_PRETTY_PRINT),
                    ]);
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

    public function getTargetDetails(array $vhost_detail_list)
    {
        try{

            foreach($vhost_detail_list as $name => $vhost){

                $this->db = new DB((object) $vhost['vhost_web_config']);
                
                if($this->db->getError() != null){
                
                    $vhost_detail_list[$name]['db_insert_connect_error'] .= $this->db->getError();
                }else{
                    
                    $vhost_detail_list[$name]['db_insert_connect_confirm'] = true;
                    
                    $table_list = $this->getTablesList();
                    
                    $vhost_detail_list[$name]['db_details_target']['table_count'] = sizeof($table_list);
                    $vhost_detail_list[$name]['db_details_target']['table_list'] = $table_list;

                    foreach($table_list as $table){

                        $row_count = $this->getTableRowCount($table);

                        $vhost_detail_list[$name]['db_details_target']['tables'][] = [
                            "name" => $table,
                            "row_count" => $row_count,
                        ];
                    }
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

    public function getSourceDetails(array $vhost_detail_list)
    {
        try{

            foreach($vhost_detail_list as $name => $vhost){

                $this->db = new DB((object)$vhost['vhost_web_config']);

                if($this->db->getError() != null){
                
                    $vhost_detail_list[$name]['db_connect_error'] .= $this->db->getError();
                }else{
                    
                    $vhost_detail_list[$name]['db_connect_success'] = true;
                    
                    $table_list = $this->getTablesList();
                    
                    $vhost_detail_list[$name]['db_details_source']['table_count'] = sizeof($table_list);
                    $vhost_detail_list[$name]['db_details_source']['table_list'] = $table_list;

                    foreach($table_list as $table){

                        $row_count = $this->getTableRowCount($table);

                        $vhost_detail_list[$name]['db_details_source']['tables'][] = [
                            "name" => $table,
                            "row_count" => $row_count,
                        ];
                    }
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
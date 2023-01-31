<?php

namespace Synaptic4u\Inserter;

use Exception;
use Synaptic4u\Log\Activity;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Log;
use Synaptic4u\DB\DB;

class Inserter
{
    private $db;
    private $config;
    
    public function __construct($config)
    {
        try {
            $this->config = $config;

            $this->db = null;
        } catch (Exception $e) {

            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);
        }
    }

    public function insertDBs(array $vhost_detail_list)
    {

        $this->log([
            "Location" => __METHOD__,
        ]);

        try{

            foreach($vhost_detail_list as $name => $vhost){

                $this->db = new DB($this->config->db->mysql_server_creds_target);
                
                if($this->db->getError() != null){
                
                    $vhost_detail_list[$name]['db_connect_error'] .= $this->db->getError();
                }else{

                    $timestamp = microtime(true);
                    
                    $this->log([
                        "Location" => __METHOD__,
                    ]);

                    // CREATE USER
                    $cli_cmd = 'mysql -h'.$this->config->db->mysql_server_creds_target->host.' -u'.$this->config->db->mysql_server_creds_target->user.' -p'.$this->config->db->mysql_server_creds_target->password.' -e \'create USER "'.$vhost['vhost_web_config']['user'].'"@"'.$this->config->db->mysql_server_creds_target->host.'" IDENTIFIED BY "'.$vhost['vhost_web_config']['password'].'";\';';
                    exec($cli_cmd, $output, $returnVar);
                    
                    if($returnVar === 0){
                        
                        $vhost_detail_list[$name]['db_insert_success_create_user'] = true;
                    }
                    
                    if($returnVar !== 0){
                        
                        $vhost_detail_list[$name]['db_connect_error'] .= ' -> Status: '.$returnVar.' Output: '.json_encode($output, JSON_PRETTY_PRINT);
                    }
                    
                    $this->log([
                        "Location" => __METHOD__,
                        "cli_cmd" => $cli_cmd,
                        "output" => json_encode($output, JSON_PRETTY_PRINT),
                        "returnVar" => json_encode($returnVar, JSON_PRETTY_PRINT),
                    ]);

                    // GRANT PRIVILEGES
                    $cli_cmd = 'mysql -h'.$this->config->db->mysql_server_creds_target->host.' -u'.$this->config->db->mysql_server_creds_target->user.' -p'.$this->config->db->mysql_server_creds_target->password.' -e \' GRANT ALL PRIVILEGES ON '.$vhost['vhost_web_config']['db'].'.* TO "'.$vhost['vhost_web_config']['user'].'"@'.$vhost['vhost_web_config']['db'].' WITH GRANT OPTION;\';';
                    exec($cli_cmd, $output, $returnVar);

                    
                    if($returnVar === 0){
                    
                        $vhost_detail_list[$name]['db_insert_success_priv_user'] = true;
                    }
                    
                    if($returnVar !== 0){
                        
                        $vhost_detail_list[$name]['db_connect_error'] .= ' -> Status: '.$returnVar.' Output: '.$output;
                    }
                    
                    $this->log([
                        "Location" => __METHOD__,
                        "cli_cmd" => $cli_cmd,
                        "output" => json_encode($output, JSON_PRETTY_PRINT),
                        "returnVar" => json_encode($returnVar, JSON_PRETTY_PRINT),
                    ]);
                    
                    // FLUSH
                    $cli_cmd = 'mysql -h'.$this->config->db->mysql_server_creds_target->host.' -u'.$this->config->db->mysql_server_creds_target->user.' -p'.$this->config->db->mysql_server_creds_target->password.' -e \'FLUSH PRIVILEGES;\';';
                    exec($cli_cmd, $output, $returnVar);
                    
                    if($returnVar === 0){
                        $vhost_detail_list[$name]['db_insert_success_priv_flush'] = true;
                    }
                    
                    if($returnVar !== 0){
                        
                        $vhost_detail_list[$name]['db_connect_error'] .= ' -> Status: '.$returnVar.' Output: '.$output;
                    }
                    
                    $this->log([
                        "Location" => __METHOD__,
                        "cli_cmd" => $cli_cmd,
                        "output" => json_encode($output, JSON_PRETTY_PRINT),
                        "returnVar" => json_encode($returnVar, JSON_PRETTY_PRINT),
                    ]);
                    
                    $vhost_detail_list[$name]['db_insert_dump_log_path'] = dirname(__FILE__, 2).'/logs/mysql_logs/'.str_replace("-","_", $name).'_'.$timestamp.'_mysql_insert_dump.txt';
                    
                    // INSERT DUMP
                    $cli_cmd = 'mysql -h'.$this->config->db->mysql_server_creds_target->host.' -u'.$this->config->db->mysql_server_creds_target->user.' -p'.$this->config->db->mysql_server_creds_target->password.' '.$vhost['vhost_web_config']['db'].' < '.$vhost_detail_list[$name]['db_dump_path'].' ;';
                    exec($cli_cmd, $output, $returnVar);
                    
                    if($returnVar === 0){
                        
                        $vhost_detail_list[$name]['db_insert_success_dump'] = true;
                    }
                    
                    if($returnVar !== 0){
                        
                        $vhost_detail_list[$name]['db_connect_error'] .= ' -> Status: '.$returnVar.' Output: '.$output;
                    }
                    
                    $this->log([
                        "Location" => __METHOD__,
                        "cli_cmd" => $cli_cmd,
                        "output" => json_encode($output, JSON_PRETTY_PRINT),
                        "returnVar" => json_encode($returnVar, JSON_PRETTY_PRINT),
                    ]);
                }
            }
     
            $this->log([
                "Location" => __METHOD__,
                "vhost_detail_list" => json_encode($vhost_detail_list, JSON_PRETTY_PRINT),
            ]);

            $this->db = new DB($this->config->db->mysql_server_creds_target);
        
            if($this->db->getError() != null){

                $table_list = $this->db->getTablesList();
                
                $vhost_detail_list[$name]['db_details_target']['table_count'] = sizeof($table_list);
                $vhost_detail_list[$name]['db_details_target']['table_list'] = $table_list;

                foreach($table_list as $table){

                    $row_count = $this->db->getTableRowCount($table);

                    $vhost_detail_list[$name]['db_details_target']['tables'][] = [
                        "name" => $table,
                        "row_count" => $row_count,
                    ];
                }
            }

        }catch(Exception $e){

            $this->error([
                'Location' => __METHOD__,
                "vhost_detail_list" => json_encode($vhost_detail_list, JSON_PRETTY_PRINT),
                'error' => $e->__toString(),
            ]);
        }finally{
            
            return $vhost_detail_list;
        }
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
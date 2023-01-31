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
    
    public function __construct(stdClass $config)
    {
        try {
            $this->config = $config;

            $this->db = null;

            if($this->config->db->mysql_server_creds_source->enabled === true){

                $this->db = new DB($this->config->db->mysql_server_creds_source);
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

            foreach($vhost_detail_list as $name => $vhost){

                $this->db = new DB($vhost['vhost_web_config']);

                // var_dump(get_class($this->db));
                // var_dump($this->db->getError());

                if($this->db->getError() != null){
                
                    $vhost_detail_list[$name]['db_connect_success'] = $this->db->getError();
                }else{
                    
                    // $timestamp = microtime(true);
                    $timestamp = '';

                    $vhost_detail_list[$name]['db_connect_success'] = true;

                    $vhost_detail_list[$name]['db_dump_log_path'] = dirname(__FILE__, 2).'/logs/mysql_logs/'.str_replace("-","_", $name).'_'.$timestamp.'_mysql_dump.txt';
                    $vhost_detail_list[$name]['db_dump_path'] = dirname(__FILE__, 3).'/mysql_dumps/'.str_replace("-","_", $name).'_'.$timestamp.'_mysql_dump.sql';
                    
                    $cli_cmd = 'mysqldump -h '.$vhost['vhost_web_config']['host'].' -u"'.$vhost['vhost_web_config']['user'].'" -p'.$vhost['vhost_web_config']['password'].' --opt --comments --hex-blob --tz-utc --events --routines --force --log-error='.$vhost_detail_list[$name]['db_dump_log_path'].' '.$vhost['vhost_web_config']['db'].' > '.$vhost_detail_list[$name]['db_dump_path'].'';
                    // mysqldump -h localhost -u"omni-joomla1" -p"omni-joomla1"  --force omnicasa_joomla1 > /omnicasa_joomla1.sql
                    
                    exec($cli_cmd, $output, $returnVar);

                    if(file_exists($vhost_detail_list[$name]['db_dump_path'])){

                        $vhost_detail_list[$name]['db_dump_success'] = true;
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

    public function getDataDetails(array $vhost_detail_list)
    {
        try{

            foreach($vhost_detail_list as $name => $vhost){

                $this->db = new DB($vhost['vhost_web_config']);

                // var_dump(get_class($this->db));
                // var_dump($this->db->getError());

                if($this->db->getError() != null){
                
                    $vhost_detail_list[$name]['db_connect_success'] = $this->db->getError();
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
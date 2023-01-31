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

        try{

            foreach($vhost_detail_list as $name => $vhost){

                $this->db = new DB($this->config->db->mysql_server_creds_target);

                // var_dump(get_class($this->db));
                // var_dump($this->db->getError());

                if($this->db->getError() != null){
                
                    $vhost_detail_list[$name]['db_connect_success'] = $this->db->getError();
                }else{

                    $cli_cmd = 'mysql -h'.$this->config->db->mysql_server_creds_target->sitename.' -u'.$this->config->db->mysql_server_creds_target->user.' -p'.$this->config->db->mysql_server_creds_target['password'].' -e \'create USER "'.$vhost['vhost_web_config']['user'].'"@'.$vhost['vhost_web_config']['db'].' IDENTIFIED BY "'.$vhost['vhost_web_config']['user'].'";\';';
                    exec($cli_cmd, $output, $returnVar);
                    $this->log([
                        "Location" => __METHOD__,
                        "cli_cmd" => $cli_cmd,
                        "output" => json_encode($output, JSON_PRETTY_PRINT),
                        "returnVar" => json_encode($returnVar, JSON_PRETTY_PRINT),
                    ]);
                    if($returnVar === 0){
                        $vhost_detail_list[$name]['db_insert_success_create_user'] = true;
                    }

                    $cli_cmd = 'mysql -h'.$this->config->db->mysql_server_creds_target->sitename.' -u'.$this->config->db->mysql_server_creds_target->user.' -p'.$this->config->db->mysql_server_creds_target['password'].' -e \' GRANT ALL PRIVILEGES ON '.$vhost['vhost_web_config']['db'].'.* TO "'.$vhost['vhost_web_config']['user'].'"@'.$vhost['vhost_web_config']['db'].' WITH GRANT OPTION;\';';
                    exec($cli_cmd, $output, $returnVar);
                    $this->log([
                        "Location" => __METHOD__,
                        "cli_cmd" => $cli_cmd,
                        "output" => json_encode($output, JSON_PRETTY_PRINT),
                        "returnVar" => json_encode($returnVar, JSON_PRETTY_PRINT),
                    ]);
                    if($returnVar === 0){
                        $vhost_detail_list[$name]['db_insert_success_priv_user'] = true;
                    }

                    $cli_cmd = 'mysql -h'.$this->config->db->mysql_server_creds_target->sitename.' -u'.$this->config->db->mysql_server_creds_target->user.' -p'.$this->config->db->mysql_server_creds_target['password'].' -e \'FLUSH PRIVILEGES;\';';
                    exec($cli_cmd, $output, $returnVar);
                    $this->log([
                        "Location" => __METHOD__,
                        "cli_cmd" => $cli_cmd,
                        "output" => json_encode($output, JSON_PRETTY_PRINT),
                        "returnVar" => json_encode($returnVar, JSON_PRETTY_PRINT),
                    ]);
                    if($returnVar === 0){
                        $vhost_detail_list[$name]['db_insert_success_priv_flush'] = true;
                    }
                    
                    $cli_cmd = 'mysql -h'.$this->config->db->mysql_server_creds_target->sitename.' -u'.$this->config->db->mysql_server_creds_target->user.' -p'.$this->config->db->mysql_server_creds_target['password'].' --log-error='.$vhost_detail_list[$name]['db_dump_log_path'].' '.$vhost['vhost_web_config']['db'].' < '.$vhost_detail_list[$name]['db_dump_path'].' ;';
                    exec($cli_cmd, $output, $returnVar);
                    $this->log([
                        "Location" => __METHOD__,
                        "cli_cmd" => $cli_cmd,
                        "output" => json_encode($output, JSON_PRETTY_PRINT),
                        "returnVar" => json_encode($returnVar, JSON_PRETTY_PRINT),
                    ]);
                    if($returnVar === 0){
                        $vhost_detail_list[$name]['db_insert_success_dump'] = true;
                    }
                }
            }
     
            $this->log([
                "Location" => __METHOD__,
                "vhost_detail_list" => json_encode($vhost_detail_list, JSON_PRETTY_PRINT),
            ]);
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

    public function createDB()
    {
        //
    }

    public function runInsertCycle($vhost_detail_list)
    {
        //
    }

    public function runDBInsert($table)
    {
        //
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
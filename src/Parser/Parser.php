<?php

namespace Synaptic4u\Parser;

use DateTime;
use Exception;
use Synaptic4u\Log\Log;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Activity;
use Synaptic4u\Files\Reader\FileReader;

class Parser
{
    private $file_reader;
    private $config;

    public function __construct($config)
    {
        try{

            $this->config = $config;
            
            $this->log([
                'Location' => __METHOD__,
                'config' => json_encode($this->config, JSON_PRETTY_PRINT),
            ]);

            $this->file_reader = new FileReader();
        }catch(Exception $e){
                        
            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);
        }
    }

    public function confirmVHostFiles(array $vhost_detail_list)
    {
        try{

            $this->log([
                'Location' => __METHOD__.' 1',
                'vhost_detail_list' => json_encode($vhost_detail_list, JSON_PRETTY_PRINT),
            ]);

            foreach ($vhost_detail_list as $name => $vhost) {
                
                $config_file = $vhost['vhost_root_dir_path'].$this->config->web_config_file->search_name;
                
                if(file_exists($config_file)){
                    
                    $vhost_detail_list[$name]['vhost_root_dir_path_exists'] = true;
                    
                    $rows = $this->file_reader->parseFile($config_file);

                    foreach($rows as $row){

                        $line = $this->file_reader->stringClear($row);
                        
                        if(strrpos($line, '$sitename', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['sitename'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$sitename', 0)+strlen('$sitename'), strlen($line))))));
                        }
                        if(strrpos($line, '$dbtype', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['dbtype'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$dbtype', 0)+strlen('$dbtype'), strlen($line))))));
                        }
                        if(strrpos($line, '$host', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['host'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$host', 0)+strlen('$host'), strlen($line))))));
                        }
                        if(strrpos($line, '$user', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['user'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$user', 0)+strlen('$user'), strlen($line))))));
                        }
                        if(strrpos($line, '$password', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['password'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$password', 0)+strlen('$password'), strlen($line))))));
                        }
                        if(strrpos($line, '$dbprefix', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['dbprefix'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$dbprefix', 0)+strlen('$dbprefix'), strlen($line))))));
                        }
                        if((strrpos($line, '$db =', 0) > 0) || (strrpos($line, '$db=', 0) > 0)){

                            $vhost_detail_list[$name]['vhost_web_config']['db'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$db', 0)+strlen('$db'), strlen($line))))));
                        }
                    }
                }
            }
            
            $this->log([
                'Location' => __METHOD__.' 2',
                'vhost_detail_list' => json_encode($vhost_detail_list, JSON_PRETTY_PRINT),
            ]);
        }catch(Exception $e){
                        
            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);
        }finally{

            return $vhost_detail_list;
        }
    }

    public function parseVHostFiles(array $vhosts)
    {
        $vhost_detail_list = [];        
        
        try{

            $this->log([
                'Location' => __METHOD__.' 1',
                'vhosts' => json_encode($vhosts, JSON_PRETTY_PRINT),
            ]);

            foreach($vhosts as $name => $vhost){
                
                $rows = [];
                $nu_rows = 0; 
                $vhost_detail_list[$name] = [
                    "vhost_conf_path" => $vhost,
                    "vhost_file_not_empty" => null,
                    "vhost_url" => null,
                    "vhost_root_dir_path_exists" => null,
                    "vhost_root_dir_path" => null,
                    "vhost_web_config" => [
                        "sitename" => null,
                        "dbtype" => null,
                        "host" => null,
                        "user" => null,
                        "password" => null,
                        "dbprefix" => null,
                        "db" => null,
                    ],
                    "db_connect_success" => null,
                    "db_dump_path" => null,
                    "db_dump_success" => null,
                    "db_dump_log_path" => null,
                    "db_dump_log_success" => null,     
                    "db_details_source" => [
                        "table_count" => null,
                        "table_list" => null,
                        "tables" => [],
                    ],
                    "db_insert_success" => null,   
                    "db_details_target" => [
                        "table_count" => null,
                        "table_list" => null,
                        "tables" => [],
                    ],
                ];

                $rows = $this->file_reader->parseFile($vhost);

                $nu_rows = sizeof($rows);

                if ($nu_rows > 0) {

                    $vhost_detail_list[$name]['vhost_file_not_empty'] = $nu_rows;

                    foreach ($rows as $key => $row) {
                        
                        $line = $this->file_reader->stringClear($row);
         
                        // $this->log([
                        //     'Location' => __METHOD__.' DEBUG',
                        //     'vhosts' => json_encode($vhosts, JSON_PRETTY_PRINT),
                        //     "line" => $line,
                        //     "root_exists" => (strrpos($line, "root ", 0) > 0) ? "true" : "false",
                        //     "root_line" => substr($line, strrpos($line, "server_name", 0)+11, strlen($line)),
                        //     "root_path" => str_replace(";", "", substr($line, strrpos($line, "root ", 0)+5, strlen($line))) ,
                        //     "server_exists" => (strrpos($line, "server_name ", 0) > 0) ? "true" : "false",
                        //     "server_line" => substr($line, strrpos($line, "server_name", 0)+11, strlen($line)),
                        //     "server_path" => str_replace(";", "", str_replace("  ", " ", substr($line, strrpos($line, "server_name", 0)+11, strlen($line)))),
                        // ]);

                        if(strrpos($line, "server_name ", 0) > 0){

                            $vhost_detail_list[$name]['vhost_url'] = str_replace(";", "", str_replace("  ", " ", substr($line, strrpos($line, "server_name", 0)+11, strlen($line))));
                        }
                        if(strrpos($line, "root ", 0) > 0){

                            $vhost_detail_list[$name]['vhost_root_dir_path'] = str_replace(";", "", substr($line, strrpos($line, "root ", 0)+5, strlen($line)));
                        }           
                    }
                }else{
                    
                    $vhost_detail_list[$name]['vhost_file_not_empty'] = null;   
                }

                $rows = null;
            }

            $this->log([
                'Location' => __METHOD__.' 2',
                'vhosts' => json_encode($vhosts, JSON_PRETTY_PRINT),
                'vhost_detail_list' => json_encode($vhost_detail_list, JSON_PRETTY_PRINT),
            ]);
        }catch(Exception $e){

            $rows = null;

            $this->error([
                'Location' => __METHOD__,
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
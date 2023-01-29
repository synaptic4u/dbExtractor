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
    private $result;
    private $config;

    public function __construct($config)
    {
        try{

            $this->result = [];
            $this->config = $config;
            
            $this->log([
                'Location' => __METHOD__,
                'config' => json_encode($this->config, JSON_PRETTY_PRINT),
                'result' => json_encode($this->result, JSON_PRETTY_PRINT),
            ]);

            $this->file_reader = new FileReader();
        }catch(Exception $e){
                        
            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);
        }
    }

    public function confirmVHostFiles(array $vhost_detail_list){
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
                        if(strrpos($line, '$dbencryption', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['dbencryption'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$dbencryption', 0)+strlen('$dbencryption'), strlen($line))))));
                        }
                        if(strrpos($line, '$dbsslverifyservercert', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['dbsslverifyservercert'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$dbsslverifyservercert', 0)+strlen('$dbsslverifyservercert'), strlen($line))))));
                        }
                        if(strrpos($line, '$dbsslkey', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['dbsslkey'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$dbsslkey', 0)+strlen('$dbsslkey'), strlen($line))))));
                        }
                        if(strrpos($line, '$dbsslcert', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['dbsslcert'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$dbsslcert', 0)+strlen('$dbsslcert'), strlen($line))))));
                        }
                        if(strrpos($line, '$dbsslca', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['dbsslca'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$dbsslca', 0)+strlen('$dbsslca'), strlen($line))))));
                        }
                        if(strrpos($line, '$dbsslcipher', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['dbsslcipher'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$dbsslcipher', 0)+strlen('$dbsslcipher'), strlen($line))))));
                        }
                        if(strrpos($line, '$dbsslcipher', 0) > 0){

                            $vhost_detail_list[$name]['vhost_web_config']['dbsslcipher'] = str_replace("'", "", str_replace(" ", "", str_replace(";", "", str_replace("=", "", substr($line, strrpos($line, '$dbsslcipher', 0)+strlen('$dbsslcipher'), strlen($line))))));
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
        try{

            $vhost_detail_list = [];

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
                        "dbencryption" => null,
                        "dbsslverifyservercert" => null,
                        "dbsslkey" => null,
                        "dbsslcert" => null,
                        "dbsslca" => null,
                        "dbsslcipher" => null,
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

                        // $test = [
                        //     "line" => $line,
                        //     "root_exists" => (strrpos($line, "root ", 0) > 0) ? "true" : "false",
                        //     "root_line" => substr($line, strrpos($line, "server_name", 0)+11, strlen($line)),
                        //     "root_path" => str_replace(";", "", substr($line, strrpos($line, "root ", 0)+5, strlen($line))) ,
                        //     "server_exists" => (strrpos($line, "server_name ", 0) > 0) ? "true" : "false",
                        //     "server_line" => substr($line, strrpos($line, "server_name", 0)+11, strlen($line)),
                        //     "server_path" => str_replace(";", "", str_replace("  ", " ", substr($line, strrpos($line, "server_name", 0)+11, strlen($line)))),
                        // ];              
                        // $this->log([
                        //     'Location' => __METHOD__.' DEBUG',
                        //     'vhosts' => json_encode($vhosts, JSON_PRETTY_PRINT),
                        //     'test' => json_encode($test, JSON_PRETTY_PRINT),
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
     * Sanitizes the string of certain characters : ",',\,,`,[]
     * Had an issue when importing MySQL data dump files.
     * Still need to prove that it was because of unwanted characters.
     *
     * @param array $columns : Array of a file line
     *
     * @return array : Returns cleaned array
     */
    protected function stringClean(array $columns)
    {
        foreach ($columns as $key => $string) {
            $columns[$key] = str_replace('"', '', $string);
            $columns[$key] = str_replace("'", '', $string);
            $columns[$key] = str_replace(',', '~', $string);
            $columns[$key] = str_replace('`', '#', $string);
            $columns[$key] = str_replace('[', '', str_replace(']', '', $string));
        }

        return $columns;
    }

    /**
     * Sanitizes the string of certain characters : ",',\,,`,[]
     * Had an issue when importing MySQL data dump files.
     * Still need to prove that it was because of unwanted characters.
     *
     * @param string $columns : string of a file line
     *
     * @return string : Returns cleaned string
     */
    protected function blobClean(string $blob)
    {
        $blob = str_replace('"', '~~~dblquote~~~', $blob);
        $blob = str_replace("'", '~~~sngquote~~~', $blob);
        $blob = str_replace(',', '~~~comma~~~', $blob);
        $blob = str_replace('`', '~~~backtick~~~', $blob);
        $blob = str_replace('\\', '~~~backslash~~~', $blob);
        $blob = str_replace(';', '~~~semicolon~~~', $blob);
        $blob = str_replace(':', '~~~colon~~~', $blob);
        $blob = str_replace('{', '~~~lbraces~~~', $blob);
        $blob = str_replace('}', '~~~rbraces~~~', $blob);
        $blob = str_replace('(', '~~~lparenthesis~~~', $blob);
        $blob = str_replace(')', '~~~rparenthesis~~~', $blob);
        $blob = str_replace('[', '~~~lbracket~~~', $blob);

        return str_replace(']', '~~~rbracket~~~', $blob);
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
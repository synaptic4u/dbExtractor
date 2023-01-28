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

    public function confirmVHostFiles($vhost_detail_list){
        try{

            $this->log([
                'Location' => __METHOD__,
                'vhost_detail_list' => json_encode($vhost_detail_list, JSON_PRETTY_PRINT),
            ]);

            
        }catch(Exception $e){
                        
            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);
        }finally{

        }
    }

    public function parseVHostFiles($vhosts)
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
                    "db_connect_success" => null,
                    "db_dump_success" => null,
                    "db_dump_path" => null,
                    "db_insert_success" => null,
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
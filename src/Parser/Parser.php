<?php

namespace Synaptic4u\Parser;

use DateTime;
use Synaptic4u\Files\Reader\FileReader;
use Synaptic4u\Log\Activity;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Log;

class Parser
{
    protected $file_reader;
    protected $db_list;
    protected $db_cred;
    protected $config;

    public function __construct($config)
    {
        $this->db_list = [];
        $this->db_cred = [
            "host" => null,
            "ip" => null,
            "db" => null,
            "username" => null,
            "password" => null,
        ];

        $this->config = $config;
        $this->file_reader = new FileReader();
    }

    public function parseVHosts(array $vhosts)
    {

        foreach($vhosts as $name => $vhost){
            $row = [];
        
            $rows = $this->file_reader->parseFile($vhost);

            $nu_rows = sizeof($rows);

            if ($nu_rows > 0) {
                foreach ($rows as $key => $row) {
                    
                    $line = $this->file_reader->stringClear($row);

                    var_dump($line);
                    
                    if (substr_count($line, "root", 0, strlen($line)) > 0) {
                        var_dump($line);

                    } else {
                        --$nu_rows;
                    }
                }
            }

            $rows = null;
    
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
    protected function stringClean(array $columns): array
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
    protected function blobClean(string $blob): string
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
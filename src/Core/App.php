<?php

namespace Synaptic4u\Core;

use Exception;
use Synaptic4u\Files\Reader\FileReader;
use Synaptic4u\Files\Writer\FileWriter;
use Synaptic4u\Files\Writer\FileWriterArray;
use Synaptic4u\Crawler\Crawler;
use Synaptic4u\Logs\Activity;
use Synaptic4u\Logs\Error;
use Synaptic4u\Logs\Log;
use Synaptic4u\Parser\Parser;
use Synaptic4u\Structure\Structure;

/**
 * Class::App :
 * Initiates the app to parse vhost files and extract databases.
 */
class App
{
    private $crawler;
    private $vhosts;
    private $options;
    private $config;
    private $tree;
    private $flat_tree;
    private $file_reader;
    private $file_writer;
    private $parser;
    private $report;
    private $result;

    public function __construct()
    {
        try {
            $start = microtime(true);

            $this->report = [
                'app_timer' => [],
                'summary' => [],
            ];

            $this->vhosts = [];

            $this->file_reader = new FileReader();

            $this->file_writer = new FileWriter();

            $this->config = $this->readConfig();
            
            if($this->config === null){
                // EXITS APP WITH CUSTOM ERROR MESSAGE & PREVENTS DISPLAYING FULL DEV ERROR
                // TOGGLE WITH COMMENT!
                throw new Exception("ERROR: The configuration file is faulty!".PHP_EOL);
            }
            // var_dump($this->config);
            
            $this->crawler = new Crawler($this->config->vhost->search_suffix);

            $this->vhosts = $this->crawler->crawl($this->config->vhost->dir_path, []);

            $this->log([
                'Location' => __METHOD__.'()',
                'vhosts' => json_encode($this->vhosts, JSON_PRETTY_PRINT),
            ]);

            // var_dump($this->vhosts);

            $this->vhosts = $this->crawler->flattenTree($this->vhosts);
            
            // var_dump($this->vhosts);
            
            $this->log([
                'Location' => __METHOD__.'()',
                'vhosts' => json_encode($this->vhosts, JSON_PRETTY_PRINT),
            ]);
            
        } catch (Exception $e) {
            // Errors currently print to screen
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);

        }finally{
            print_r(PHP_EOL."Application has exited.".PHP_EOL);
        }
    }


    private function readConfig()
    {
        try{

            $error = null;
            $config = null;
            $config_path = dirname(__FILE__, 3).'/config.json';

            $config = $this->file_reader->readJSONFile($config_path, 1);
            
            $this->log([
                'Location' => __METHOD__.'()',
                'config_path' => $config_path,
                'config' => json_encode($config, JSON_PRETTY_PRINT)
            ]);

            if(($config->vhost->dir_path === null) || (strlen($config->vhost->dir_path) < 2)){
                $error = "The configuration file is faulty!".PHP_EOL.
                         "       VHost directory path cannot be empty.";
                throw new Exception($error);
            }
            if(sizeof($config->vhost->search_suffix) === 0){
                $error = "The configuration file is faulty!".PHP_EOL.
                         "       VHost search suffix cannot be empty.";
                throw new Exception($error);
            }

            // $this->log([
            //     'Location' => __METHOD__.'()',
            //     'dir path = null' => ($config->vhost->dir_path === null) ? "true":"false",
            //     'dir path length check' => (strlen($config->vhost->dir_path) > 0) ? "true":"false",
            //     'dir path length' => strlen($config->vhost->dir_path),
            //     'suffix length check' => (sizeof($config->vhost->search_suffix) === 0) ? "true":"false",
            //     'suffix length' => sizeof($config->vhost->search_suffix),
            // ]);
        }catch(Exception $e){
    
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);

            print_r(PHP_EOL."ERROR: ".$error.PHP_EOL);
            
            $config = null;
        }finally{
            return $config;
        }
    }

    private function writeTree()
    {
        $this->file_writer->writeToFile(new FileWriterArray(), '/structure_files/tree.txt', $this->tree);

        $this->file_writer->writeToFile(new FileWriterArray(), '/structure_files/flattened.txt', $this->flat_tree);
    }


    private function buildStructure()
    {
        $structure = new Structure($this->config, $this->options);
        $structure->parse();
    }

    private function loadLogs(): array
    {
        return $this->parser->loadLogs($this->flat_tree);
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    public function error($msg)
    {
        new Log($msg, new Error());
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    private function log($msg)
    {
        new Log($msg, new Activity());
    }
}
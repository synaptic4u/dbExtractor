<?php

namespace Synaptic4u\Core;

use Exception;
use Synaptic4u\Log\Log;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Activity;
use Synaptic4u\Parser\Parser;
use Synaptic4u\Crawler\Crawler;
use Synaptic4u\Inserter\Inserter;
use Synaptic4u\Extractor\Extractor;
use Synaptic4u\Structure\Structure;
use Synaptic4u\Files\Reader\FileReader;
use Synaptic4u\Files\Writer\FileWriter;
use Synaptic4u\Files\Writer\FileWriterText;
use Synaptic4u\Files\Writer\FileWriterArray;

/**
 * Class::App :
 * Initiates the app to parse vhost files and extract databases.
 */
class App
{
    // Class Objects
    private $crawler;
    private $file_reader;
    private $file_writer;

    // Data Structs
    private $vhost_list;
    private $vhost_detail_list;
    private $config;
    
    private $start;
    private $parser;
    private $report;
    private $result;

    public function __construct()
    {
        try {
            $error = null;
            $this->start = microtime(true);

            print_r(PHP_EOL."Application has started.".PHP_EOL);
            
            $this->vhost_list = null;
            $this->vhost_detail_list = [];
            
            $this->report = [
                'app_timer' => [],
                'summary' => [
                    'batch' => [],
                ],
            ];

            $this->file_reader = new FileReader();
            $this->file_writer = new FileWriter();

            $this->config = $this->readConfig();
            
            if($this->config === null){
                $error = "ERROR: The configuration file is faulty!".PHP_EOL;
                throw new Exception($error);
            }

            $this->vhost_list = $this->parseVHosts();
            
            if($this->vhost_list === null){
                $error = "ERROR: The Virtual Host List couldn't be compiled!".PHP_EOL;
                throw new Exception($error);
            }
            
            $this->runBatch();

        } catch (Exception $e) {
            
            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);

        }finally{
            
            print_r(PHP_EOL."Application has exited.".PHP_EOL);
        }
    }

    private function runBatch(){

        try{

            $this->vhost_detail_list = $this->parseVHostFiles();
            
            if(($this->vhost_detail_list === null) || (sizeof($this->vhost_detail_list) < 2)){
                $error = "ERROR: The Virtual Host Detailed List could not be compiled!".PHP_EOL;
                throw new Exception($error);
            }

            $this->vhost_detail_list = $this->confirmVHostFiles();

            $this->prepDodgyVHostDetailReport();
            $this->writeToFileJSON('/reports/vhost_detail_list.txt', $this->vhost_detail_list);
            
            $this->log([
                'Location' => __METHOD__.' 1',
                'vhost_detail_list' => json_encode($this->vhost_detail_list, JSON_PRETTY_PRINT),
            ]);

            $this->vhost_detail_list = $this->getDataDetails();

            $this->writeToFileJSON('/reports/vhost_detail_data_list.txt', $this->vhost_detail_list);
            
            $this->log([
                'Location' => __METHOD__.' 2',
                'vhost_detail_list' => json_encode($this->vhost_detail_list, JSON_PRETTY_PRINT),
            ]);

            $this->vhost_detail_list = $this->dumpDBs();

            $this->writeToFileJSON('/reports/vhost_detail_data_list.txt', $this->vhost_detail_list);
            
            $this->log([
                'Location' => __METHOD__.' 3',
                'vhost_detail_list' => json_encode($this->vhost_detail_list, JSON_PRETTY_PRINT),
            ]);
        }catch(Exception $e){
            
            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);
        }finally{

        }
    }

    private function insertDBs(){
        return (new Inserter($this->config))->insertDBs($this->vhost_detail_list);
    }

    private function dumpDBs(){
        return (new Extractor($this->config))->dumpDBs($this->vhost_detail_list);
    }

    private function getDataDetails(){
        return (new Extractor($this->config))->getDataDetails($this->vhost_detail_list);
    }

    private function confirmVHostFiles(){
        return (new Parser($this->config))->confirmVHostFiles($this->vhost_detail_list);
    }

    private function parseVHostFiles(){  
        return (new Parser($this->config))->parseVHostFiles($this->vhost_list);
    }

    private function parseVHosts()
    {
        try{

            $vhosts = [];

            $this->crawler = new Crawler($this->config->vhost->search_suffix);

            $vhosts = $this->crawler->crawl($this->config->vhost->dir_path, []);
            // var_dump($vhosts);

            $this->log([
                'Location' => __METHOD__.' 1',
                'vhosts' => json_encode($vhosts, JSON_PRETTY_PRINT),
            ]);

            $vhosts = $this->crawler->flattenArray($vhosts);
            // var_dump($vhosts);
            
            $this->writeToFileJSON('/reports/vhost_list.txt', $vhosts);
            
            $this->log([
                'Location' => __METHOD__.' 2',
                'vhosts' => json_encode($vhosts, JSON_PRETTY_PRINT),
            ]);

            $this->dumpDBs();
        }catch(Exception $e){

            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);

            $vhosts = null;
        }finally{
            
            return $vhosts;
        }
    }

    private function readConfig()
    {
        try{

            $error = null;
            $config = null;
            $config_path = dirname(__FILE__, 3).'/config.json';

            $config = $this->file_reader->readJSONFile($config_path, 1);
            
            // $this->log([
            //     'Location' => __METHOD__.' 1',
            //     'config_path' => $config_path,
            //     'config' => json_encode($config, JSON_PRETTY_PRINT)
            // ]);

            // $this->log([
            //     'Location' => __METHOD__.' DEBUG',
            //     'dir path = null' => ($config->vhost->dir_path === null) ? "true":"false",
            //     'dir path length check' => (strlen($config->vhost->dir_path) > 0) ? "true":"false",
            //     'dir path length' => strlen($config->vhost->dir_path),
            //     'suffix length check' => (sizeof($config->vhost->search_suffix) === 0) ? "true":"false",
            //     'suffix length' => sizeof($config->vhost->search_suffix),
            // ]);

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

            $this->log([
                'Location' => __METHOD__.' 2',
                'config_path' => $config_path,
                'config' => json_encode($config, JSON_PRETTY_PRINT)
            ]);
        }catch(Exception $e){
    
            $this->error([
                'Location' => __METHOD__,
                'error' => $e->__toString(),
            ]);

            print_r(PHP_EOL."ERROR: ".$error.PHP_EOL);
            
            $config = null;
        }finally{
            
            return $config;
        }
    }

    private function writeToFileJSON(string $filename, mixed $content)
    {   
    
        $date = date('e Y-m-d H:i:s');
        
        $this->file_writer->writeToFile(new FileWriterArray(), $filename, [
            "DateTime" => $date,
            "Content" => $content
        ], 4);
    }

    private function writeToFileText(string $filename, mixed $content)
    {
        
        $date = date('e Y-m-d H:i:s');
        
        $this->file_writer->writeToFile(new FileWriterText(), $filename, [
            "DateTime" => $date,
            "Content" => $content
        ], 4);
    }

    private function prepDodgyVHostDetailReport(){

        $vhosts = $this->vhost_detail_list;

        foreach($vhosts as $name => $vhost){

            $vhosts[$name] = array_splice($vhost, 0, 6);
        }

        $this->writeToFileJSON('/reports/vhost_detail_list.txt', $vhosts);
    }

    private function prepDodgyVHostDetailDataReport()
    {

        $this->writeToFileJSON('/reports/vhost_detail_data_list.txt', $this->vhost_detail_list);
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
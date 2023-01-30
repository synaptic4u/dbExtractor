<?php

namespace Synaptic4u\Batch;

use Exception;
use Synaptic4u\Log\Log;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Activity;

class Batch{
    
    private $config;

    public function __construct($config)
    {
    
        try{
            
            $this->config = $config;


        }catch(Exception $e){

        }
    }

    public function runBatch(){
        try{

        }catch(Exception $e){

            
        }finally{

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
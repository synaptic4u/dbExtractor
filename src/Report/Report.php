<?php

namespace Synaptic4u\Report;


use Synaptic4u\DB\DB;
use Synaptic4u\Logs\Log;
use Synaptic4u\Logs\Error;
use Synaptic4u\Logs\Activity;

class Report{

    protected $db;

    public function __construct()
    {
        $this->db = new DB();
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
    protected function log($msg)
    {
        new Log($msg, new Activity());
    }
}

?>
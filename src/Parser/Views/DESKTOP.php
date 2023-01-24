<?php

namespace Synaptic4u\Parser\Views;

use Synaptic4u\Logs\Activity;
use Synaptic4u\Logs\Error;
use Synaptic4u\Logs\Log;

class DESKTOP implements IParserUI
{
    public function display(array $params = [])
    {
        return 1;
    }

    public function finished(array $params = [])
    {
        return 1;
    }

    public function timeReport(array $result)
    {
        return 1;
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

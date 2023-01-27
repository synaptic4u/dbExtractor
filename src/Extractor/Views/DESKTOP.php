<?php

namespace Synaptic4u\Tables\Views;

use Synaptic4u\Log\Activity;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Log;

class DESKTOP implements ITablesUI
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

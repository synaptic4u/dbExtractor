<?php

namespace Synaptic4u\Structure\Models;

use Synaptic4u\DB\DB;
use Synaptic4u\Log\Activity;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Log;

class MYSQL implements IStructureDB
{
    protected $db;

    public function __construct($options)
    {
        $db = '\\Synaptic4u\\DB\\'.$options['DB'];
        $this->db = new DB(new $db());
    }

    /**
     * Queries the given table for total number of rows.
     *
     * @param mixed $table
     *
     * @return int : Number of table rows
     */
    public function getRowCount($table): int
    {
        $sql = 'select count(*) as nu_rows from '.$table.' where 1 = ?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $count = $res['nu_rows'];
        }

        return $count;
    }

    /**
     * Queries table for the max primary key : logid.
     *
     * @param mixed $table : std::Class
     */
    public function getMaxLogID($table): mixed
    {
        $sql = 'select max(logid) as max_logid from '.$table.' where 1 = ?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $id = $res['max_logid'];
        }

        return $id;
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

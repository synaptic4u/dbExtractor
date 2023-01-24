<?php

namespace Synaptic4u\Logs;

interface ILog
{
    public function writeLog(string $msg);
}

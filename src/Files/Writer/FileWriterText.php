<?php

namespace Synaptic4u\Files\Writer;

use Synaptic4u\Log\Activity;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Log;

/**
 * Class FileWriterText
 * Writes content to file as received, no formatting.
 */
class FileWriterText implements IFileWriter
{
    public function appendToFile(string $path, $content)
    {
        $file = fopen($path, 'a');

        fwrite($file, $content);

        fclose($file);
    }

    public function writeToFile(string $path, $content)
    {
        $file = fopen($path, 'w');

        fwrite($file, $content);

        fclose($file);
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
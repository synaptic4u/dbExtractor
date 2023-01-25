<?php

namespace Synaptic4u\Logs;

use Exception;

use Synaptic4u\Files\Reader\FileReader;
use Synaptic4u\Files\Writer\FileWriter;
use Synaptic4u\Files\Writer\FileWriterArray;
use Synaptic4u\Files\Writer\FileWriterText;

/**
 * Class::Error
 * Interface::ILog.
 *
 * Passes log message to FileWriter instance to be written to file.
 */
class Error implements ILog
{
    private $path;
    private $file_writer;

    /**
     * Assignes log path to local variable.
     * Instantiates the FileWriter class.
     */
    public function __construct()
    {
        try {
            $this->path = '/logs/error.txt';

            $this->file_writer = new FileWriter();
        } catch (Exception $e) {
            //  This is the log file, so...? Go look in the OS error log!
        }
    }

    /**
     * Calls to FileWriter::appendToFile -> Passes IFileWriter instance.
     *
     * @param string $msg : Received log message
     */
    public function writeLog(string $msg)
    {
        $this->file_writer->appendToFile(
            new FileWriterText(),
            $this->path,
            $msg
        );
    }
}
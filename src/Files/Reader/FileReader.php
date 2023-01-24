<?php

namespace Synaptic4u\Files\Reader;

use Exception;
use Synaptic4u\Logs\Activity;
use Synaptic4u\Logs\Error;
use Synaptic4u\Logs\Log;

class FileReader
{
    /**
     * Calls FileReader::readLogFile.
     *
     * @param string $file : file path
     *
     * @return array : array of file contents separated by "\n"
     */
    public function parseFile(string $file): array
    {
        return $this->readLogFile($file);
    }

    /**
     * Retrieves contents of JSON encoded file.
     * Optional same directory or one directory up.
     *
     * @param string $file        : file name
     * @param int    $dir_include : selection to choose different directory or current directory
     *
     * @return mixed : Returns std::Class object
     */
    public function readJSONFile(string $file, int $dir_include = 0): mixed
    {        
        if (0 === $dir_include) {
            $file = dirname(__FILE__, 3).$file;
        }

        return json_decode(file_get_contents($file));
    }

    /**
     * Replaces white space.
     *
     * @param string $string : string with whitespace
     *
     * @return string : string with minimal whitespace
     */
    public function stringClear(string $string): string
    {
        return str_replace(
            '     ',
            ' ',
            str_replace(
                '    ',
                ' ',
                str_replace(
                    '   ',
                    ' ',
                    str_replace(
                        '  ',
                        ' ',
                        str_replace(
                            ' :',
                            ':',
                            str_replace(' ;', ';', $string)
                        )
                    )
                )
            )
        );
    }

    /**
     * Retrieves the contents from a log file returned as a array.
     *
     * @param string $file : file path
     *
     * @return array : file contents separated into array
     */
    private function readLogFile(string $file): array
    {
        $rows = [];

        try {
            $log_txt = file_get_contents($file);

            $rows = explode("\n", $log_txt);
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);
        } finally {
            return $rows;
        }
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    private function error($msg)
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
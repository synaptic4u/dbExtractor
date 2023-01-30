<?php

namespace Synaptic4u\Files\Writer;

/**
 * Class::FileWriter
 * Handles all the file writing functionality.
 * Uses the IFileWriter interface to determine to write array or string.
 *
 * FileWriter::setPath()
 * FileWriter::appendToFile()
 * FileWriter::writeToFile()
 */
class FileWriter
{
    protected $path;

    public function appendToFile(IFileWriter $file_writer, string $file, $params, int $levels = 3)
    {
        $this->setPath($file, $levels);

        $file_writer->appendToFile($this->path, $params);
    }

    public function writeToFile(IFileWriter $file_writer, string $file, $params, int $levels = 3)
    {
        $this->setPath($file, $levels);

        $file_writer->writeToFile($this->path, $params);
    }

    protected function setPath(string $file, int $levels)
    {
        $this->path = dirname(__FILE__, $levels).$file;
    }
}
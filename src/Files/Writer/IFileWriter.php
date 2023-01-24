<?php

namespace Synaptic4u\Files\Writer;

interface IFileWriter
{
    public function appendToFile(string $file, $content);

    public function writeToFile(string $file, $content);
}

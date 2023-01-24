<?php

namespace Synaptic4u\Parser\Views;

interface IParserUI
{
    public function display(array $params = []);

    public function finished(array $params = []);

    public function timeReport(array $result);
}

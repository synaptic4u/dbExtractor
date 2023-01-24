<?php

namespace Synaptic4u\Tables\Views;

interface ITablesUI
{
    public function display(array $params = []);

    public function finished(array $params = []);

    public function timeReport(array $result);
}

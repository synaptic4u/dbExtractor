<?php

namespace Synaptic4u\Structure\Models;

interface IStructureDB
{
    public function getRowCount($table): int;

    public function getMaxLogID($table): mixed;
}

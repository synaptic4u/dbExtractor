<?php

namespace Synaptic4u\Structure\Views;

interface IStructureUI
{
    public function exists(array $params): int;

    public function created(array $params = []): int;

    public function processing(): int;
}

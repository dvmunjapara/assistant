<?php

namespace DV\Assistant\Contracts;

interface HasFunction
{
    public function tools(?array $tools): static;

    public function functionCallbacks(string $function, callable $callback): static;
}

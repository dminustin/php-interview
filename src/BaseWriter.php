<?php

namespace Danila\PhpCoder;

abstract class BaseWriter
{
    protected string $document;

    abstract public function makeDocument(): static;
}
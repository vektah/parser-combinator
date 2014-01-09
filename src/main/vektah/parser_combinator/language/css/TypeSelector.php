<?php


namespace vektah\parser_combinator\language\css;

class TypeSelector
{
    public $type;

    public $namespace;

    public function __construct($type, $namespace = null)
    {
        $this->type = $type;
        $this->namespace = $namespace;
    }
}

<?php

namespace vektah\parser_combinator\language\proto;

class Enum {
    public $name;
    public $values;
    public $options;

    function __construct($name, array $array, array $options = [])
    {
        $this->name = $name;
        $this->values = $array;
        $this->options = $options;
    }
}

<?php

namespace vektah\parser_combinator\language\proto;

class Enum {
    public $name;
    public $values;

    function __construct($name, array $array)
    {
        $this->name = $name;
        $this->values = $array;
    }
}

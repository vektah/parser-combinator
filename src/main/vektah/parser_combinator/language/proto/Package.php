<?php

namespace vektah\parser_combinator\language\proto;

class Package {
    public $name;
    public $elements;

    function __construct($name, $elements = [])
    {
        $this->name = $name;
        $this->elements = $elements;
    }
}

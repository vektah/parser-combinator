<?php

namespace vektah\parser_combinator\language\proto;

class Package {
    public $name;

    function __construct($name)
    {
        $this->name = $name;
    }
}

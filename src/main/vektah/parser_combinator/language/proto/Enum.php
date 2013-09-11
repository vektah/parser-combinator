<?php

namespace vektah\parser_combinator\language\proto;

class Enum {
    /** @var string */
    public $name;

    /** @var EnumValue[] */
    public $values;

    /** @var Option[] */
    public $options;

    function __construct($name, array $array, array $options = [])
    {
        $this->name = $name;
        $this->values = $array;
        $this->options = $options;
    }
}

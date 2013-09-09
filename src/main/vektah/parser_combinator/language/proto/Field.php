<?php

namespace vektah\parser_combinator\language\proto;

class Field
{
    public $label;
    public $type;
    public $identifier;
    public $index;
    public $default;

    function __construct($label, $type, $identifier, $index, $default = null)
    {
        $this->identifier = $identifier;
        $this->index = $index;
        $this->label = $label;
        $this->type = $type;
        $this->default = $default;
    }
}

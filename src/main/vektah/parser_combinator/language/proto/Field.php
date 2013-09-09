<?php

namespace vektah\parser_combinator\language\proto;

class Field
{
    public $label;
    public $type;
    public $identifier;
    public $index;

    function __construct($label, $type, $identifier, $index)
    {
        $this->identifier = $identifier;
        $this->index = $index;
        $this->label = $label;
        $this->type = $type;
    }
}

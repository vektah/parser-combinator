<?php

namespace vektah\parser_combinator\language\proto;

class Field
{
    public $label;
    public $type;
    public $identifier;
    public $index;
    public $options;

    function __construct($label, $type, $identifier, $index, $options = null)
    {
        $this->identifier = $identifier;
        $this->index = $index;
        $this->label = $label;
        $this->type = $type;
        $this->options = $options;
    }
}

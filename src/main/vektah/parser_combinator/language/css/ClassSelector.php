<?php


namespace vektah\parser_combinator\language\css;

class ClassSelector
{
    public $class;

    public function __construct($class)
    {
        $this->class = $class;
    }
}

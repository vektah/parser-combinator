<?php


namespace vektah\parser_combinator\language\css;

class Selector
{
    public $selectors = [];

    public function __construct($selectors)
    {
        $this->selectors = $selectors;
    }
}

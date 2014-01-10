<?php


namespace vektah\parser_combinator\language\css\selectors;

abstract class Selector
{
    abstract public function __toString();
    abstract public function toCss();
}

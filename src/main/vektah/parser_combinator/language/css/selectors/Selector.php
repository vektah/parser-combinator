<?php


namespace vektah\parser_combinator\language\css\selectors;

use vektah\parser_combinator\language\css\CssObject;
use vektah\parser_combinator\language\css\CssSelectorParser;

abstract class Selector
{
    public function matches($selector) {
        if (is_string($selector)) {
            $selector = CssSelectorParser::instance()->parse($selector);
        }
        return $this->matchesObject($selector->define());

    }

    abstract public function matchesObject(CssObject $object);


    /**
     * @return CssObject
     */
    abstract public function define();
    abstract public function __toString();
    abstract public function toCss();
}

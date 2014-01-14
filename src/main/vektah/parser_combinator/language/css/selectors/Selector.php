<?php


namespace vektah\parser_combinator\language\css\selectors;

use vektah\parser_combinator\language\css\CssObject;
use vektah\parser_combinator\language\css\CssSelectorParser;

abstract class Selector
{
    public function matches($selector) {
        if (!is_string($selector)) {
            throw new \InvalidArgumentException("$selector must be a string");
        }
        $selector = CssSelectorParser::instance()->parse($selector);
        $object = $selector->define();
        $object->isRoot = true;
        return $this->matchesObject($object);

    }

    abstract public function matchesObject(CssObject $object);


    /**
     * @return CssObject
     */
    abstract public function define();
    abstract public function __toString();
    abstract public function toCss();
}

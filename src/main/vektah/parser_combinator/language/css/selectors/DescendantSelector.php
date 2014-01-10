<?php


namespace vektah\parser_combinator\language\css\selectors;

class DescendantSelector extends Selector
{
    private $parent;
    private $child;

    public function __construct(Selector $parent, Selector $child)
    {
        $this->child = $child;
        $this->parent = $parent;
    }

    public function toCss()
    {
        return "{$this->parent->toCss()} {$this->child->toCss()}";
    }

    public function __toString() {
        return "Descendant($this->parent, $this->child)";
    }
}

<?php


namespace vektah\parser_combinator\language\css\selectors;

use vektah\parser_combinator\exception\GrammarException;

class AnySelector extends Selector
{
    public function __construct($children) {
        if (!is_array($children)) {
            $children = func_get_args();
        }

        foreach ($children as $child) {
            if (!($child instanceof Selector)) {
                throw new GrammarException('Children must be selectors');
            }
        }

        $this->children = $children;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function toCss()
    {
        $child_css = array_map(function($child) {
            return $child->toCss();
        }, $this->children);

        return implode(', ', $child_css);
    }

    public function __toString()
    {
        return 'Any(' .  implode(', ', $this->children) . ')';
    }
}

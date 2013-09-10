<?php

namespace vektah\parser_combinator\language\proto;

class File {
    public $elements;

    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    /**
     * Calls the given callback once per element in the tree
     *
     * @param callable $callback
     */
    public function traverse(callable $callback)
    {
        $this->recurse($this->elements, $callback);
    }

    /**
     * Internal function for recursing over the parse tree.
     */
    private function recurse(array $elements, callable $callback) {
        foreach ($elements as $element) {
            $callback($element);

            if (isset($element->values) && is_array($element->values)) {
                $this->recurse($element->values, $callback);
            }

            if (isset($element->options) && is_array($element->options)) {
                $this->recurse($element->options, $callback);
            }

            if (isset($element->members) && is_array($element->members)) {
                $this->recurse($element->members, $callback);
            }

            if (isset($element->endpoints) && is_array($element->endpoints)) {
                $this->recurse($element->members, $callback);
            }
        }
    }
}

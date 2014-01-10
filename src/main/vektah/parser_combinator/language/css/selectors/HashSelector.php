<?php


namespace vektah\parser_combinator\language\css\selectors;

class HashSelector extends Selector
{
    /** @var string */
    private $element;

    public function __construct($element)
    {
        $this->element = $element;
    }

    /**
     * @return string
     */
    public function getElement()
    {
        return $this->element;
    }

    public function toCss() {
        return "#{$this->element}";
    }

    public function __toString() {
        return "Hash($this->element)";
    }
}

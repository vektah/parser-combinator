<?php


namespace vektah\parser_combinator\language\css\selectors;

class ElementSelector extends Selector
{
    /** @var string */
    private $namespace;

    /** @var string */
    private $element;

    public function __construct($element, $namespace = null)
    {
        $this->namespace = $namespace;
        $this->element = $element;
    }

    /**
     * @return string
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    public function __toString() {
        $namespace = $this->namespace ? ', ' . $this->namespace : '';
        return "Element({$this->element}{$namespace})";
    }

    public function toCss() {
        $namespace = $this->namespace ? $this->namespace . '|' : '';
        return "{$namespace}{$this->element}";
    }
}

<?php


namespace vektah\parser_combinator\language\css\selectors;

class ClassSelector extends Selector
{
    /** @var string */
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    public function toCss() {
        return ".{$this->class}";
    }

    public function __toString() {
        return "Class($this->class)";
    }
}

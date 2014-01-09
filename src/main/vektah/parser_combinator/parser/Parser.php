<?php

namespace vektah\parser_combinator\parser;


use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;

abstract class Parser
{
    private $name;

    /**
     * Parse the given input
     *
     * @param Input $input
     *
     * @return Result result
     */
    abstract public function parse(Input $input);

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public static function getParserStack()
    {
        $message = '';
        $trace = array_reverse(debug_backtrace());

        $lastName = null;
        foreach ($trace as $frame) {
            if (isset($frame['object']) && $frame['object'] instanceof Parser && $name = $frame['object']->getName()) {
                if ($name != $lastName) {
                    $message .= $name . '.';
                }

                $lastName = $name;
            }
        }

        return substr($message, 0, strlen($message) - 1);
    }
}

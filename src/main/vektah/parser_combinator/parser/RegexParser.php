<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;

/**
 * Look for a matching regex at the given offset
 */
class RegexParser extends Parser
{
    private $expression;
    private $capturing;

    /**
     * @param string $expression The string to match against. Keep in mind that the delimiter is always ~
     * @param string $options
     * @param bool $capturing
     */
    public function __construct($expression, $options = '', $capturing = true)
    {
        $this->expression = '~\G' . $expression . '~' . $options;
        $this->capturing = $capturing;
    }

    public function parse(Input $input)
    {
        if (!$input->match($this->expression, $matches)) {
            return $input->errorHere("Expected regex '{$this->expression}' to match '{$input->get()}', it does not.")->addParser($this);
        }

        $output = $input->get(strlen($matches[0]));
        $input->consume(strlen($matches[0]));

        if (!$this->capturing) {
            return Result::nonCapturingMatch($output)->addParser($this);
        } else {
            return Result::match($output)->addParser($this);
        }
    }
}

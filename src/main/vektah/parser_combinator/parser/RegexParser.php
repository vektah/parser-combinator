<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;

/**
 * Look for a matching regex at the given offset
 */
class RegexParser implements Parser
{
    private $expression;
    private $capture;

    /**
     * @param string $expression        The string to match against
     * @param bool $capture             If true this parser will output a result.
     */
    public function __construct($expression, $capture = true)
    {
        $this->expression = $expression;
        $this->capture = $capture;
        if ($expression[1] != '^') {
            throw new \InvalidArgumentException('Regex must start with an anchor');
        }
    }

    public function parse(Input $input)
    {
        if (!$input->match($this->expression, $matches)) {
            throw new ParseException("At {$input->getPositionDescription()}: Expected regex '{$this->expression}' to match '{$input->get()}', it does not.");
        }

        if (!$this->capture) {
            $input->consume(strlen($matches[0]));
            return null;
        }

        $output = $input->get(strlen($matches[0]));
        $input->consume(strlen($matches[0]));

        return $output;
    }
}

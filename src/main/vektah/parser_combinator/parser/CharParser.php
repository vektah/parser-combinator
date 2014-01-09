<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\exception\GrammarException;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;

/**
 * Matches any sequence of chars, generally preferred over regex for performance reasons (regex requires a copy
 * of the whole remaining buffer)
 */
class CharParser extends Parser
{
    private $raw_chars;
    private $chars;
    private $min;
    private $max;
    private $capture;

    /**
     * @param string $chars  The valid chars
     * @param int $min    The minimum number of chars to capture
     * @param int $max    The maximum number of chars to capture
     * @param bool $capture If true this will be a capturing parser
     */
    public function __construct($chars, $min = 0, $max = null, $capture = true)
    {
        if (!is_numeric($min) && !is_null($min)) {
            throw new GrammarException('Min must be numeric');
        }

        if (!is_numeric($max) && !is_null($max)) {
            throw new GrammarException('Max must be numeric');
        }

        if (!is_bool($capture)) {
            throw new GrammarException('Capture must be a boolean');
        }

        $this->raw_chars = $chars;
        $this->chars = array_flip(str_split($chars));
        $this->min = $min;
        $this->max = $max;
        $this->capture = $capture;
    }

    /**
     * Parse the given input
     *
     * @param Input $input
     *
     * @return mixed result
     */
    public function parse(Input $input)
    {
        $offset = 0;

        while (true) {
            if ($this->max !== null && $offset >= $this->max) {
                break;
            }

            $char = $input->peek($offset);

            if (!isset($this->chars[$char])) {
                break;
            }

            $offset++;
            continue;
        }
        $result = $input->get($offset);
        $input->consume($offset);

        if ($offset < $this->min) {
            return Result::error("At {$input->getPositionDescription()}: Did not capture enough chars, expected {$this->min} of [{$this->raw_chars}], found {$offset}.");
        }

        if ($this->capture) {
            return Result::match($result);
        }

        return Result::nonCapturingMatch();
    }
}

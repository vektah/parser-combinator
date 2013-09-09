<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;

/**
 * Parse a single static string. String matching is quite efficient, but returning a result will copy that substring.
 */
class StringParser implements Parser
{
    private $needle;
    private $case_sensitive;
    private $capture;

    /**
     * @param string $needle        The string to match against
     * @param bool $case_sensitive  should the comparison be done case sensitively?
     * @param bool $capture         If true this parser will output a result.
     */
    public function __construct($needle, $case_sensitive = true, $capture = true)
    {
        $this->needle = $needle;
        $this->case_sensitive = $case_sensitive;
        $this->capture = $capture;
    }

    public function parse(Input $input)
    {
        if ($this->needle == '') {
            return Result::nonCapturingMatch();
        }

        if (!$input->startsWith($this->needle, $this->case_sensitive)) {
            return Result::error("At {$input->getPositionDescription()}: Expected '{$this->needle}' but found '{$input->get()}'");
        }

        if (!$this->capture) {
            $input->consume(strlen($this->needle));
            return Result::nonCapturingMatch();
        }

        $output = $input->get(strlen($this->needle));
        $input->consume(strlen($this->needle));

        return Result::match($output);
    }
}

<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;
use vektah\parser_combinator\parser\SingletonTrait;

/**
 * Does not consume any input, but asserts that if this point is reached no back tracking is allowed.
 */
class PositiveMatch extends Parser
{
    use SingletonTrait;

    /**
     * Parse the given input
     *
     * @param Input $input
     *
     * @return mixed result
     */
    public function parse(Input $input)
    {
        return Result::nonCapturingMatch(true);
    }
}

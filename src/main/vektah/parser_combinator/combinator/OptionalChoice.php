<?php

namespace vektah\parser_combinator\combinator;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;

/**
 * Matches any one of the parsers
 */
class OptionalChoice extends Combinator
{
    public function combine(Input $input)
    {
        $initialOffset = $input->getOffset();

        foreach ($this->getParsers() as $parser) {
            // Keep trying each of the parsers until one matches.

            $result = $parser->parse($input);

            // Errors and positive results will stop us from searching.
            if (!$result->errorMessage || $result->positiveMatch) {
                return $result;
            }

            // To be safe we rewind the input after each attempt.
            $input->setOffset($initialOffset);
        }

        return Result::match(null);
    }
}

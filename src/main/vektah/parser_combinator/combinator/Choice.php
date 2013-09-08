<?php

namespace vektah\parser_combinator\combinator;

use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;

/**
 * Matches any one of the parsers
 */
class Choice extends Combinator
{
    public function combine(Input $input)
    {
        $initialOffset = $input->getOffset();

        foreach ($this->getParsers() as $parser) {
            // Keep trying each of the parsers until one matches.
            try {
                $result = $parser->parse($input);
            } catch (ParseException $e) {
                // To be safe we rewind the input
                $input->setOffset($initialOffset);
                continue;
            }

            return $result;
        }

        throw new ParseException('At ' . $input->getPositionDescription() . ": Could not find any options that match {$input->get()}");
    }
}

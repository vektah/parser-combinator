<?php

namespace vektah\parser_combinator\combinator;


use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;
use vektah\parser_combinator\exception\GrammarException;

class Not extends Combinator
{
    public function combine(Input $input)
    {
        // Initially no parser should match, this would be a zero width result
        if ($this->matches($input)) {
            return Result::error('At ' . $input->getPositionDescription() . ": Found a match at the start of the source {$input->get()}");
        }

        $consumed = '';
        while (!$this->matches($input) && !$input->complete()) {
            $consumed .= $input->peek(0);
            $input->consume(1);
        }

        return Result::match([$consumed]);
    }

    private function matches(Input $input) {
        $initialOffset = $input->getOffset();

        foreach ($this->getParsers() as $parser) {
            // Keep trying each of the parsers until one matches.

            $result = $parser->parse($input);

            // Errors and positive results will stop us from searching.
            if ($result->match) {
                $input->setOffset($initialOffset);
                return true;
            }

            // To be safe we rewind the input after each attempt.
            $input->setOffset($initialOffset);
        }

        return false;
    }
}

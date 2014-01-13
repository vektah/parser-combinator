<?php

namespace vektah\parser_combinator\combinator;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;

class Sequence extends Combinator
{
    public function combine(Input $input)
    {
        $aggregatedData = [];
        $isPositive = false;

        foreach ($this->getParsers() as $parser) {
            $result = $parser->parse($input)->addParser($this);

            if ($result->hasData) {
                $aggregatedData[] = $result->data;
            }

            // Any single positive match in the sequence makes the whole sequence positive.
            if ($result->positiveMatch) {
                $isPositive = true;
            }

            // Any single error causes the whole sequence to error
            if ($result->errorMessage) {
                $result->positiveMatch = $isPositive;
                return $result;
            }
        }

        if ($aggregatedData === []) {
            return Result::nonCapturingMatch($isPositive)->addParser($this);
        } else {
            return Result::match($aggregatedData, $isPositive)->addParser($this);
        }
    }
}

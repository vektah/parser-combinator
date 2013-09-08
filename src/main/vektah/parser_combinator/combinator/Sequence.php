<?php

namespace vektah\parser_combinator\combinator;

use vektah\parser_combinator\Input;

class Sequence extends Combinator
{
    public function combine(Input $input)
    {
        $aggregatedResults = [];
        foreach ($this->getParsers() as $parser) {
            $result = $parser->parse($input);

            if ($result !== null) {
                $aggregatedResults[] = $result;
            }
        }

        if ($aggregatedResults == []) {
            return null;
        }

        return $aggregatedResults;
    }
}

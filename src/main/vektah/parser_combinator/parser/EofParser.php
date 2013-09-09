<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;

class EofParser implements Parser
{

    public function parse(Input $input)
    {
        if (!$input->complete()) {
            return Result::error("At {$input->getPositionDescription()}: Unable to process {$input->get()}");
        }

        return Result::nonCapturingMatch();
    }
}

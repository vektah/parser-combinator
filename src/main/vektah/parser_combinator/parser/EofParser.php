<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;

class EofParser implements Parser
{

    public function parse(Input $input)
    {
        if (!$input->complete()) {
            throw new ParseException("At {$input->getPositionDescription()}: Unable to process {$input->get()}");
        }

        return null;
    }
}

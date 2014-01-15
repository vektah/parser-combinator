<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;
use vektah\parser_combinator\parser\SingletonTrait;

class EofParser extends Parser
{
    use SingletonTrait;

    public function parse(Input $input)
    {
        if (!$input->complete()) {
            return $input->errorHere("Unable to process {$input->get()}")->addParser($this);
        }

        return Result::nonCapturingMatch()->addParser($this);
    }
}

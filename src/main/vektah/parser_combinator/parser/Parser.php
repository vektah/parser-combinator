<?php

namespace vektah\parser_combinator\parser;


use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;

interface Parser
{
    /**
     * Parse the given input
     *
     * @param Input $input
     *
     * @return Result result
     */
    public function parse(Input $input);
}

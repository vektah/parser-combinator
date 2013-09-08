<?php

namespace vektah\parser_combinator\parser;


use vektah\parser_combinator\Input;

interface Parser
{
    /**
     * Parse the given input
     *
     * @param Input $input
     *
     * @return mixed result
     */
    public function parse(Input $input);
}

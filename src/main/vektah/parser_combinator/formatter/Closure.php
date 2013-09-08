<?php

namespace vektah\parser_combinator\formatter;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\Parser;

class Closure implements Parser
{
    /** @var Parser */
    private $parser;

    /** @var  callable */
    private $function;

    public function __construct(Parser $parser, callable $function)
    {
        $this->function = $function;
        $this->parser = $parser;
    }


    public function parse(Input $input)
    {
        return call_user_func($this->function, $this->parser->parse($input));
    }
}

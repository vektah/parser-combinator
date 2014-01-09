<?php

namespace vektah\parser_combinator\formatter;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\Parser;

class Closure extends Parser
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
        $result = $this->parser->parse($input);
        // Don't call the callback if there was an error.
        if ($result->errorMessage) {
            return $result;
        }

        $result->data = call_user_func($this->function, $result->data);

        return $result;
    }
}

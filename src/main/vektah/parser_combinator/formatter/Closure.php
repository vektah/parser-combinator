<?php

namespace vektah\parser_combinator\formatter;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\Parser;
use vektah\parser_combinator\parser\StringParser;

class Closure extends Parser
{
    /** @var Parser */
    private $parser;

    /** @var  callable */
    private $function;

    public function __construct($parser, callable $function)
    {
        if (is_string($parser)) {
            $parser = new StringParser($parser);
        }
        $this->function = $function;
        $this->parser = $parser;
    }


    public function parse(Input $input)
    {
        $result = $this->parser->parse($input)->addParser($this);
        // Don't call the callback if there was an error.
        if ($result->errorMessage) {
            return $result;
        }

        $result->data = call_user_func($this->function, $result->data);

        return $result;
    }
}

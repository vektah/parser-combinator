<?php

namespace vektah\parser_combinator\formatter;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\Parser;

class Flatten implements Parser
{
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function parse(Input $input)
    {
        $original_array = $this->parser->parse($input);

        if (is_array($original_array)) {
            $result = array();
            array_walk_recursive(
                $original_array,
                function ($v, $k) use (&$result) {
                    $result[] = $v;
                }
            );

            return $result;
        } else {
            return $result;
        }
    }
}

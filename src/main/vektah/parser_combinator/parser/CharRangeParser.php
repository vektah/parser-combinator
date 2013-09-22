<?php

namespace vektah\parser_combinator\parser;

class CharRangeParser extends CharParser
{
    public function __construct(array $ranges, $min = null, $max = null, $capture = true)
    {
        $chars = '';

        foreach ($ranges as $first => $last) {
            // Use non numeric keys as chars directly.
            if (!is_string($first)) {
                $chars .= $last;
            }

            for ($i = ord($first); $i <= ord($last); $i++) {
                $chars .= chr($i);
            }
        }

        parent::__construct($chars, $min, $max, $capture);
    }
}

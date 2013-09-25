<?php

namespace vektah\parser_combinator\parser;

class CharRangeParser extends CharParser
{
    public function __construct(array $ranges, $min = null, $max = null, $capture = true)
    {
        $chars = '';

        foreach ($ranges as $first => $last) {
            // If the value is an array with one value then treat it as a sequence of chars instead of a range.
            if (is_array($last)) {
                $chars .= $last[0];
            } else {
                for ($i = ord($first); $i <= ord($last); $i++) {
                    $chars .= chr($i);
                }
            }
        }

        parent::__construct($chars, $min, $max, $capture);
    }
}

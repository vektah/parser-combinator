<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\parser\SingletonTrait;

/**
 * Matches zero or more whitespace characters.
 */
class WhitespaceParser extends RegexParser
{
    use SingletonTrait;

    public function __construct($min = 0, $capturing = false)
    {
        $count = "*";
        if ($min) {
            $count = '{' . $min . ',}';
        }
        parent::__construct("\\s$count", 'ms', $capturing);
    }
}

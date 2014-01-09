<?php

namespace vektah\parser_combinator\parser;

use vektah\parser_combinator\parser\SingletonTrait;

/**
 * Matches zero or more whitespace characters.
 */
class WhitespaceParser extends CharParser
{
    use SingletonTrait;

    public function __construct($min = 0, $capturing = false)
    {
        parent::__construct("\n\t\r ", $min, null, $capturing);
    }
}

<?php

namespace vektah\parser_combinator\parser;

/**
 * Matches zero or more whitespace characters.
 */
class WhitespaceParser extends CharParser
{
    public function __construct()
    {
        parent::__construct("\n\t\r ", 0, null, false);
    }
}

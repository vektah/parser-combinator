<?php

namespace vektah\parser_combinator\test\parser;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\CharParser;

class CharParserTest extends TestCase
{
    public function testMatchWhitespace()
    {
        $matcher = new CharParser(" \n\t\r");

        $input = new Input('    asdf');
        $this->assertEquals('    ', $matcher->parse($input)->data);

        $this->assertEquals(4, $input->getOffset());
    }

    public function testLimitedMatch()
    {
        $matcher = new CharParser(" \n\t\r", 0, 1);

        $input = new Input('    asdf');
        $this->assertEquals(' ', $matcher->parse($input)->data);

        $this->assertEquals(1, $input->getOffset());
    }

    public function testNonCapturingMatch()
    {
        $matcher = new CharParser(" \n\t\r", 1, 1, false);

        $input = new Input('    asdf');
        $this->assertEquals(null, $matcher->parse($input)->data);

        $this->assertEquals(1, $input->getOffset());
    }
}

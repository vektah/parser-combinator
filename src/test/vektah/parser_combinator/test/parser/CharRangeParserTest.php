<?php

namespace vektah\parser_combinator\test\parser;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\CharRangeParser;

class CharRangeParserTest extends TestCase
{
    public function testMatchAlphaNum()
    {
        $parser = new CharRangeParser(['a' => 'z', 'A' => 'Z', '0' => '9']);

        $this->assertEquals('asdZ0239', $parser->parse(new Input('asdZ0239-'))->data);
        $this->assertEquals('asdf', $parser->parse(new Input('asdf-1234-'))->data);
    }
}

<?php

namespace vektah\parser_combinator\test\combinator;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\combinator\Choice;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\StringParser;

class OneOfTest extends TestCase
{
    public function testSingleOneOf()
    {
        $combinator = new Choice(
            [
                new StringParser('asdf')
            ]
        );

        $this->assertEquals('asdf', $combinator->parse(new Input("asdf")));
    }

    public function testMultiple()
    {
        $combinator = new Choice(
            [
                new StringParser('asdf'),
                new StringParser('hjkl')
            ]
        );

        $this->assertEquals('hjkl', $combinator->parse(new Input("hjkl")));
    }
}

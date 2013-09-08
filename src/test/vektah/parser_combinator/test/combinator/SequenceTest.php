<?php

namespace vektah\parser_combinator\test\combinator;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\StringParser;

class SequenceTest extends TestCase
{
    public function testSequenceOfOne()
    {
        $parser = new Sequence(
            [
                new StringParser('asdf')
            ]
        );

        $this->assertEquals(['asdf'], $parser->parse(new Input('asdf')));
    }

    public function testSequenceOfTwo()
    {
        $parser = new Sequence(
            [
                new StringParser('asdf'),
                new StringParser('hjkl')
            ]
        );

        $this->assertEquals(['asdf', 'hjkl'], $parser->parse(new Input('asdfhjkl')));
    }

    public function testMismatch()
    {
        $parser = new Sequence(
            [
                new StringParser('asdf'),
                new StringParser('hjkl')
            ]
        );

        $this->setExpectedException(ParseException::_CLASS, "At line 1 offset 5: Expected 'hjkl' but found 'asdf");
        $parser->parse(new Input('asdfasdf'));
    }

    public function testNonCapturingChild()
    {
        $parser = new Sequence(
            [
                new StringParser('asdf'),
                new StringParser('hjkl', true, false)
            ]
        );

        $this->assertEquals(['asdf'], $parser->parse(new Input('asdfhjkl')));
    }

    public function testAllNonCapturingChildren()
    {
        $parser = new Sequence(
            [
                new StringParser('asdf', true, false),
                new StringParser('hjkl', true, false)
            ]
        );

        $this->assertNull($parser->parse(new Input('asdfhjkl')));
    }
}

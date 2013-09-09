<?php

namespace vektah\parser_combinator\test\combinator;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\parser\StringParser;

class SequenceTest extends TestCase
{
    public function testSequenceOfOne()
    {
        $parser = new Sequence(['asdf']);

        $this->assertEquals(['asdf'], $parser->parse(new Input('asdf'))->data);
    }

    public function testSequenceOfTwo()
    {
        $parser = new Sequence(['asdf', 'hjkl']);

        $this->assertEquals(['asdf', 'hjkl'], $parser->parse(new Input('asdfhjkl'))->data);
    }

    public function testMismatch()
    {
        $parser = new Sequence(['asdf', 'hjkl']);

        $this->assertEquals("At line 1 offset 5: Expected 'hjkl' but found 'asdf'", $parser->parse(new Input('asdfasdf'))->errorMessage);
    }

    public function testNonCapturingChild()
    {
        $parser = new Sequence(['asdf', new StringParser('hjkl', true, false)]);

        $this->assertEquals(['asdf'], $parser->parse(new Input('asdfhjkl'))->data);
    }

    public function testAllNonCapturingChildren()
    {
        $parser = new Sequence([new StringParser('asdf', true, false), new StringParser('hjkl', true, false)]);

        $this->assertFalse($parser->parse(new Input('asdfhjkl'))->hasData);
    }
}

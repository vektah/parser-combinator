<?php

namespace vektah\parser_combinator\test\combinator;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\combinator\Many;
use vektah\parser_combinator\exception\GrammarException;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\RegexParser;
use vektah\parser_combinator\parser\StringParser;

class ManyTest extends TestCase
{
    public function testSingle()
    {
        $many = new Many([new StringParser('asdf')]);

        $this->assertEquals(['asdf'], $many->parse(new Input('asdf')));
        $this->assertEquals(['asdf', 'asdf'], $many->parse(new Input('asdfasdf')));
    }

    public function testMultiple()
    {
        $many = new Many([new StringParser('asdf'), new StringParser('hjkl')]);

        $this->assertEquals(['hjkl'], $many->parse(new Input('hjkl')));
        $this->assertEquals(['hjkl', 'asdf'], $many->parse(new Input('hjklasdf')));
    }

    public function testMin()
    {
        $many = new Many([new StringParser('asdf')], 2);

        $this->setExpectedException(ParseException::_CLASS, 'At line 1 offset 5: Expected 2 elements, but found 1');
        $many->parse(new Input('asdf'));
    }

    public function testMax()
    {
        $many = new Many([new StringParser('asdf')], 0, 2);

        $input = new Input('asdfasdfasdf');
        $this->assertEquals(['asdf', 'asdf'], $many->parse($input));
        $this->assertEquals(['asdf'], $many->parse($input));
    }

    public function testZeroWidthMatch()
    {
        $many = new Many([new RegexParser('/^/')]);

        $this->setExpectedException(GrammarException::_CLASS);
        $many->parse(new Input('asdf'));
    }
}

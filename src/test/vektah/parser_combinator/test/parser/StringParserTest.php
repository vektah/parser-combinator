<?php

namespace vektah\parser_combinator\test\parser;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\StringParser;

class StringParserTest extends TestCase
{
    public function testMatchAtStart()
    {
        $input = new Input("asdf");
        $parser = new StringParser('asdf');
;
        $this->assertEquals('asdf', $parser->parse($input));
        $this->assertEquals(4, $input->getOffset());
    }

    public function testMatchAtOffset()
    {
        $input = new Input("asdf", 1);
        $parser = new StringParser('sdf');

        $this->assertEquals('sdf', $parser->parse($input));
        $this->assertEquals(4, $input->getOffset());
    }

    public function testPartialMatchAtOffset()
    {
        $input = new Input("asdf", 1);
        $parser = new StringParser('sd');

        $this->assertEquals('sd', $parser->parse($input));
        $this->assertEquals(3, $input->getOffset());
    }

    public function testNotMatchingAtStart()
    {
        $input = new Input("asdf");
        $parser = new StringParser('sdf');

        $this->setExpectedException(ParseException::_CLASS);
        $parser->parse($input);
    }

    public function testNotMatchingAtOffset()
    {
        $input = new Input("asdf", 1);
        $parser = new StringParser('asdf');

        $this->setExpectedException(ParseException::_CLASS);
        $parser->parse($input);
    }

    public function testNonCapturing()
    {
        $parser = new StringParser('asdf', true, false);

        $this->assertNull($parser->parse(new Input('asdf')));
    }

    public function testCaseInsensitive()
    {
        $parser = new StringParser('asdf', false);
        $this->assertEquals('Asdf', $parser->parse(new Input('Asdf')));
    }

    public function testCaseSensitive()
    {
        $parser = new StringParser('asdf', true);

        $this->setExpectedException(ParseException::_CLASS, 'At line 1 offset 1: Expected \'asdf\' but found \'Asdf\'');
        $parser->parse(new Input("Asdf"));
    }
}

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
        $result = $parser->parse($input);
        $this->assertEquals('asdf', $result->data);
        $this->assertEquals(true, $result->hasData);
        $this->assertEquals(4, $input->getOffset());
    }

    public function testMatchAtOffset()
    {
        $input = new Input("asdf", 1);
        $parser = new StringParser('sdf');

        $this->assertEquals('sdf', $parser->parse($input)->data);
        $this->assertEquals(4, $input->getOffset());
    }

    public function testPartialMatchAtOffset()
    {
        $input = new Input("asdf", 1);
        $parser = new StringParser('sd');

        $this->assertEquals('sd', $parser->parse($input)->data);
        $this->assertEquals(3, $input->getOffset());
    }

    public function testNotMatchingAtStart()
    {
        $input = new Input("asdf");
        $parser = new StringParser('sdf');

        $this->assertNotNull($parser->parse($input)->errorMessage);
    }

    public function testNotMatchingAtOffset()
    {
        $input = new Input("asdf", 1);
        $parser = new StringParser('asdf');

        $this->assertNotNull($parser->parse($input)->errorMessage);
    }

    public function testNonCapturing()
    {
        $parser = new StringParser('asdf', true, false);

        $this->assertFalse($parser->parse(new Input('asdf'))->hasData);
    }

    public function testCaseInsensitive()
    {
        $parser = new StringParser('asdf', false);
        $this->assertEquals('Asdf', $parser->parse(new Input('Asdf'))->data);
    }

    public function testCaseSensitive()
    {
        $parser = new StringParser('asdf', true);

        $this->assertEquals('At line 1 offset 1: Expected \'asdf\' but found \'Asdf\'', $parser->parse(new Input("Asdf"))->errorMessage);
    }
}

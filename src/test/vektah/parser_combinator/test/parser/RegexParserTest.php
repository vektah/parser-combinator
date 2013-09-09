<?php

namespace vektah\parser_combinator\test\parser;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\RegexParser;

class RegexParserTest extends TestCase
{
    public function testMatch()
    {
        $parser = new RegexParser('/^asdf/i');
        $this->assertEquals('asdf', $parser->parse(new Input('asdf'))->data);
        $this->assertEquals('Asdf', $parser->parse(new Input('Asdf'))->data);
    }

    public function testMustStartWithAnchor()
    {
        $this->setExpectedException('InvalidArgumentException', 'Regex must start with an anchor');
        $parser = new RegexParser('asdf');
    }

    public function testNonMatching()
    {
        $parser = new RegexParser('/^asdf/i');

        $this->assertEquals("At line 1 offset 1: Expected regex '/^asdf/i' to match 'fff', it does not.", $parser->parse(new Input('fff'))->errorMessage);
    }

    public function testNonCapturing()
    {
        $parser = new RegexParser('/^asdf/', false);

        $this->assertEquals(null, $parser->parse($input = new Input('asdf'))->data);
        $this->assertEquals(4, $input->getOffset());
    }
}

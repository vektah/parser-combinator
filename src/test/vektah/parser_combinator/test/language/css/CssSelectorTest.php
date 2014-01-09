<?php


namespace vektah\parser_combinator\test\language\css;

use vektah\parser_combinator\language\css\CssSelectorParser;
use PHPUnit_Framework_TestCase as TestCase;

class CssSelectorTest extends TestCase
{
    /** @var CssSelectorParser */
    private $parser;

    public function setUp() {
        $this->parser = new CssSelectorParser();
    }

    public function testSimple()
    {
        // From https://metacpan.org/module/Google::ProtocolBuffers
        $input = '.class #foo asd .foo #hash ag';

        print_r($this->parser->parse($input));
    }
}

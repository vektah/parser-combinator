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
        $input = '.class #foo asd bob.foo #hash ag, .foo, .class #foo asd bob.foo #hash ag, .foo, .class #foo asd bob.foo #hash ag, .foo, .class #foo asd bob.foo #hash ag, .foo, .class #foo asd bob.foo #hash ag, .foo, .class #foo asd bob.foo #hash ag, .foo';

        $ast = $this->parser->parse($input);
        print_r($ast);

        echo $ast ."\n";

        echo $ast->toCss() ."\n";
    }
}

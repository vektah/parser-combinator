<?php


namespace vektah\parser_combinator\test\language\css;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\language\css\CssDeclaration;
use vektah\parser_combinator\language\css\CssParser;

class CssTest extends TestCase
{
    /** @var CssParser */
    private $parser;

    public function setUp() {
        $this->parser = new CssParser();
    }

    public function testEmptyFile()
    {
        $css = $this->parser->parseString('');

        $this->assertEquals([], $css->getDeclarations('h1'));
    }

    public function testEmptyRule()
    {
        $css = $this->parser->parseString('h1{}');

        $this->assertEquals([], $css->getDeclarations('h1'));
        $this->assertEquals('h1{}', $css->toCss());
    }

    public function testSimpleRule()
    {
        $css = $this->parser->parseString('h1 { color: brown }');
        $this->assertEquals([new CssDeclaration('color', 'brown')], $css->getDeclarations('h1'));
        $this->assertEquals('h1{color:brown;}', $css->toCss());
    }

    public function testSimpleRuleWithTrailingSemicolon()
    {
        $css = $this->parser->parseString('h1 { color: brown; }');
        $this->assertEquals([new CssDeclaration('color', 'brown')], $css->getDeclarations('h1'));
        $this->assertEquals('h1{color:brown;}', $css->toCss());
    }

    public function testMultipleRules()
    {
        $css = $this->parser->parseString('h1 { color: brown; background-color: #00f; }');
        $this->assertEquals([new CssDeclaration('color', 'brown'), new CssDeclaration('background-color', '#00f')], $css->getDeclarations('h1'));
        $this->assertEquals('h1{color:brown;background-color:#00f;}', $css->toCss());
    }

    public function testComments()
    {
        $css = $this->parser->parseString('/*h1 { color: brown; background-color: #00f; } */');
        $this->assertEquals([], $css->getDeclarations('h1'));
    }

    public function testPercentage()
    {
        $css = $this->parser->parseString('h1 { -ms-text-size-adjust: 3.14157654%; }');
        $this->assertEquals([new CssDeclaration('-ms-text-size-adjust', '3 .14157654%')], $css->getDeclarations('h1'));
        $this->assertEquals('h1{-ms-text-size-adjust:3 .14157654%;}', $css->toCss());
    }

    public function testImportant()
    {
        $css = $this->parser->parseString('h1 { -ms-text-size-adjust: 100% !important}');
        $this->assertEquals([new CssDeclaration('-ms-text-size-adjust', '100%', true)], $css->getDeclarations('h1'));
        $this->assertEquals('h1{-ms-text-size-adjust:100%!important;}', $css->toCss());
    }
}

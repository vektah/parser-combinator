<?php


namespace vektah\parser_combinator\test\language\css;

use vektah\parser_combinator\language\css\CssObject;
use vektah\parser_combinator\language\css\CssSelectorParser;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * These tests are taken from http://www.w3.org/Style/CSS/Test/CSS3/Selectors/current/html/index.html
 */
class CssSelectorTest extends TestCase
{
    /** @var CssSelectorParser */
    private $parser;

    public function setUp() {
        $this->parser = new CssSelectorParser();
    }

    public function testElement()
    {
        $input = 'p';

        $ast = $this->parser->parse($input);

        $this->assertEquals($input, $ast->toCss());
        $this->assertEquals('Element(p)', $ast->__toString());
        $this->assertEquals(new CssObject(['element' => 'p']), $ast->define());


        $this->assertTrue($ast->matches('p'));
        $this->assertFalse($ast->matches('li'));
    }

    public function testGroups()
    {
        $input = 'li, p';

        $ast = $this->parser->parse($input);

        $this->assertEquals($input, $ast->toCss());
        $this->assertEquals('Any(Element(li), Element(p))', $ast->__toString());
        $this->assertEquals(new CssObject(['children' => [new CssObject(['element' => 'li']), new CssObject(['element' => 'p'])]]), $ast->define());

        $this->assertTrue($ast->matches('p'));
        $this->assertTrue($ast->matches('li'));
        $this->assertFalse($ast->matches('div'));
    }

    public function testUniversalNoNamespace()
    {
        $input = '*';

        $ast = $this->parser->parse($input);

        $this->assertEquals($input, $ast->toCss());
        $this->assertEquals('Universal()', $ast->__toString());

        $this->assertEquals(new CssObject(), $ast->define());

        $this->assertTrue($ast->matches('p'));
        $this->assertTrue($ast->matches('li'));
    }

    public function testUniversalOmitted()
    {
        $input = '#foo';

        $ast = $this->parser->parse($input);
        $this->assertEquals(new CssObject(['id' => 'foo']), $ast->define());

        $this->assertEquals($input, $ast->toCss());
        $this->assertEquals('Hash(foo)', $ast->__toString());

        $this->assertTrue($ast->matches('p#foo'));
        $this->assertTrue($ast->matches('li#foo'));
    }

    public function testAttribute()
    {
        $input = 'p[title]';


        $ast = $this->parser->parse($input);

        $this->assertEquals(new CssObject(['element' => 'p', 'attributes' => ['title' => true]]), $ast->define());

        $this->assertEquals($input, $ast->toCss());
        $this->assertEquals('All(Element(p), Attribute(title))', $ast->__toString());

        $this->assertTrue($ast->matches('p[title]'));
        $this->assertTrue($ast->matches("p[title='foo']"));
    }

    public function testAttributeEquals()
    {
        $input = "address[title='foo']";


        $ast = $this->parser->parse($input);

        $this->assertEquals(new CssObject(['element' => 'address', 'attributes' => ['title' => 'foo']]), $ast->define());

        $this->assertEquals($input, $ast->toCss());
        $this->assertEquals("All(Element(address), Attribute(title='foo'))", $ast->__toString());

        $this->assertTrue($ast->matches("address[title='foo']"));
    }

    public function testMultipleAttributeEquals()
    {
        $input = "p[class~='foo']";
        $ast = $this->parser->parse($input);

        $this->assertEquals(new CssObject(['element' => 'p', 'attributes' => ['class' => 'foo']]), $ast->define());

        $this->assertEquals($input, $ast->toCss());
        $this->assertEquals("All(Element(p), Attribute(class~='foo'))", $ast->__toString());

        $this->assertTrue($ast->matches("p[class='bar foo baz']"));
        $this->assertFalse($ast->matches("p[class='bar foobies baz']"));
    }

    /**
     * Section 6.3.1: Represents the att attribute whose value is a
     * space-separated list of words, one of which is exactly "val". If this
     * selector is used, the words in the value must not contain spaces
     * (since they are separated by spaces).
     */
    public function testMultipleAttributeEquals2()
    {

        $input = "p[title~='hello world']";
        $ast = $this->parser->parse($input);

        $this->assertEquals(new CssObject(['element' => 'p', 'attributes' => ['title' => 'hello world']]), $ast->define());

        $this->assertEquals($input, $ast->toCss());
        $this->assertEquals("All(Element(p), Attribute(title~='hello world'))", $ast->__toString());

        $this->assertFalse($ast->matches("p[class='hello world']"));
    }

    public function testAttributeValueSelectorsWithHyphens()
    {
        $input = "p[lang|='en']";
        $ast = $this->parser->parse($input);

        $this->assertEquals(new CssObject(['element' => 'p', 'attributes' => ['lang' => 'en']]), $ast->define());

        $this->assertEquals($input, $ast->toCss());
        $this->assertEquals("All(Element(p), Attribute(lang|='en'))", $ast->__toString());

        $this->assertTrue($ast->matches("p[lang='en-gb']"));
        $this->assertFalse($ast->matches("p[lang='fr-en']"));
    }
}

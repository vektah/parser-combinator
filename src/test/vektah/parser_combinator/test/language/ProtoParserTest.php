<?php

namespace vektah\parser_combinator\test\language;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\language\ProtoParser;
use vektah\parser_combinator\language\proto\Field;

class ProtoParserTest extends TestCase
{
    public function testBasicMessage()
    {
        $parser = new ProtoParser();

        $messages = $parser->parse(new Input('message Foo {required int32 foo = 1;}'));

        $this->assertEquals(1, count($messages));
        $message = $messages[0];

        $this->assertEquals('Foo', $message->getName());

        $this->assertEquals(1, count($message->getMembers()));
        $field = $message->getMembers()[0];

        $this->assertEquals('required', $field->label);
        $this->assertEquals('int32', $field->type);
        $this->assertEquals('foo', $field->identifier);
        $this->assertEquals(1, $field->index);
    }

    public function testIntTypes()
    {
        $parser = new ProtoParser();

        $messages = $parser->parse(new Input('
            message Foo {
                required int32 foo = 1;
                required int32 bar = 0x2;
                required int32 baz = 04;
                required int32 fit = 0xFF;
                required int32 far = 077;
            }
        '));

        $this->assertEquals(1, count($messages));
        $message = $messages[0];

        $this->assertEquals('Foo', $message->getName());

        $this->assertEquals(5, count($message->getMembers()));

        $this->assertEquals(new Field('required', 'int32', 'foo', 1), $message->getMembers()[0]);
        $this->assertEquals(new Field('required', 'int32', 'bar', 2), $message->getMembers()[1]);
        $this->assertEquals(new Field('required', 'int32', 'baz', 4), $message->getMembers()[2]);
        $this->assertEquals(new Field('required', 'int32', 'fit', 255), $message->getMembers()[3]);
        $this->assertEquals(new Field('required', 'int32', 'far', 63), $message->getMembers()[4]);
    }
}

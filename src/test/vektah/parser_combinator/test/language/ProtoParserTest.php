<?php

namespace vektah\parser_combinator\test\language;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\language\ProtoParser;
use vektah\parser_combinator\language\proto\Enum;
use vektah\parser_combinator\language\proto\Extend;
use vektah\parser_combinator\language\proto\Extensions;
use vektah\parser_combinator\language\proto\Field;
use vektah\parser_combinator\language\proto\Identifier;
use vektah\parser_combinator\language\proto\Import;
use vektah\parser_combinator\language\proto\Message;
use vektah\parser_combinator\language\proto\Option;
use vektah\parser_combinator\language\proto\Package;

class ProtoParserTest extends TestCase
{
    /**
     * @var ProtoParser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new ProtoParser();
    }

    public function testBasicMessage()
    {
        $messages = $this->parser->parse('message Foo {required int32 foo = 1;}');

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

        $messages = $this->parser->parse('
            message Foo {
                required int32 foo = 1;
                required int32 bar = 0x2;
                required int32 baz = 04;
                required int32 fit = 0xFF;
                required int32 far = 077;
            }
        ');

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

    public function testImport()
    {
        $this->assertEquals([new Import('asdf')], $this->parser->parse('import "asdf";'));
    }

    public function testComment()
    {
        $this->assertEquals([new Import('asdf'), new Import('hjkl')], $this->parser->parse('
            import "asdf";
            // Ignore meeee
            import "hjkl";
        '));
    }

    public function testOption()
    {
        $this->assertEquals([new Option('hello_world', true)], $this->parser->parse('option (hello_world) = true;'));
        $this->assertEquals([new Option('hello_world', 'OK')], $this->parser->parse('option hello_world = "OK";'));
        $this->assertEquals([new Option('.hello_world', 123)], $this->parser->parse('option .hello_world = 123;'));
    }

    public function testEnum()
    {
        $this->assertEquals([new Enum('Foo', ['asdf', 'qwer'])], $this->parser->parse('enum Foo {
            asdf = 0;
            qwer = 1;
        }'));

        $this->assertEquals([new Enum('Foo', [
            10 => 'asdf',
            20 => 'qwer'
        ])], $this->parser->parse('enum Foo {
            asdf = 10;
            qwer = 20;
        }'));
    }

    public function testCombination()
    {
        $input = '
            message Bar {
                required MessageType type = 0;
                required bytes data = 1;

                enum Foo {
                    asdf = 0;
                    qwer = 1;
                }
            }
        ';

        $expected = [
            new Message('Bar', [
                new Field('required', 'MessageType', 'type', 0),
                new Field('required', 'bytes', 'data', 1),
                new Enum('Foo', ['asdf', 'qwer'])
            ])
        ];

        $this->assertEquals($expected, $this->parser->parse($input));
    }

    public function testExtensions()
    {
        $this->assertEquals([new Message('Foo', [new Extensions(10, new Identifier('max'))])], $this->parser->parse('
            message Foo {
                extensions 10 to max;
            }
        '));
    }

    public function testExtend()
    {
        $this->assertEquals([new Extend('Foo', [new Field('optional', 'int32', 'foo', 10)])], $this->parser->parse('
            extend Foo {
                optional int32 foo = 10;
            }
        '));
    }

    public function testPackage()
    {
        $this->assertEquals([new Package('foobar')], $this->parser->parse('
            package foobar;
        '));
    }
}

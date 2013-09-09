<?php

namespace vektah\parser_combinator\test\language;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\language\ProtoParser;
use vektah\parser_combinator\language\proto\Enum;
use vektah\parser_combinator\language\proto\Extend;
use vektah\parser_combinator\language\proto\Extensions;
use vektah\parser_combinator\language\proto\Field;
use vektah\parser_combinator\language\proto\Identifier;
use vektah\parser_combinator\language\proto\Message;
use vektah\parser_combinator\language\proto\Package;

/**
 * These tests input files were taken from https://metacpan.org/module/Google::ProtocolBuffers
 */
class ProtoExampleFilesTest extends TestCase
{
    private $parser;

    public function __construct()
    {
        $this->parser = new ProtoParser();
    }

    public function testSample1()
    {
        $input = '
             message Person {
              required string name  = 1;
              required int32 id     = 2; // Unique ID number for this person.
              optional string email = 3;

              enum PhoneType {
                MOBILE = 0;
                HOME = 1;
                WORK = 2;
              }

              message PhoneNumber {
                required string number = 1;
                optional PhoneType type = 2 [default = HOME];
              }

              repeated PhoneNumber phone = 4;
            }
        ';

        $expected = [
            new Message('Person', [
                new Field('required', 'string', 'name', 1),
                new Field('required', 'int32', 'id', 2),
                new Field('optional', 'string', 'email', 3),

                new Enum('PhoneType', [
                    0 => 'MOBILE',
                    1 => 'HOME',
                    2 => 'WORK'
                ]),

                new Message('PhoneNumber', [
                    new Field('required', 'string', 'number', 1),
                    new Field('optional', 'PhoneType', 'type', 2, new Identifier('HOME')),
                ]),

                new Field('repeated', 'PhoneNumber', 'phone', 4),
            ])
        ];

        $this->assertEquals($expected, $this->parser->parse($input));
    }

    public function testSample2()
    {
        $input = '
            package some_package;
            // message Plugh contains one regular field and three extensions
            message Plugh {
                optional int32 foo = 1;
                extensions 10 to max;
            }
            extend Plugh {
                optional int32 bar = 10;
            }
            message Thud {
                extend Plugh {
                    optional int32 baz = 11;
                }
            }

            // Note: the official Google\'s proto compiler does not allow
            // several package declarations in a file (as of version 2.0.1).
            // To compile this example with the official protoc, put lines
            // above to some other file, and import that file here.
            package another_package;
            // import \'other_file.proto\';

            extend some_package.Plugh {
            optional int32 qux = 12;
            }
        ';

        $expected = [
            new Package('some_package'),

            new Message('Plugh', [
                new Field('optional', 'int32', 'foo', 1),
                new Extensions(10, new Identifier('max')),
            ]),

            new Extend('Plugh', [
                new Field('optional', 'int32', 'bar', 10)
            ]),

            new Message('Thud', [
                new Extend('Plugh', [
                    new Field('optional', 'int32', 'baz', 11)
                ])
            ]),

            new Package('another_package'),

            new Extend('some_package.Plugh', [
                new Field('optional', 'int32', 'qux', 12)
            ])
        ];

        $this->assertEquals($expected, $this->parser->parse($input));
    }
}

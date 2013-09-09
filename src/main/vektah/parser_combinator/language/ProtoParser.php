<?php

namespace vektah\parser_combinator\language;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\combinator\Choice;
use vektah\parser_combinator\combinator\Many;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\formatter\Closure;
use vektah\parser_combinator\formatter\Concatenate;
use vektah\parser_combinator\formatter\Ignore;
use vektah\parser_combinator\language\proto\Enum;
use vektah\parser_combinator\language\proto\Extend;
use vektah\parser_combinator\language\proto\Extensions;
use vektah\parser_combinator\language\proto\Field;
use vektah\parser_combinator\language\proto\Identifier;
use vektah\parser_combinator\language\proto\Import;
use vektah\parser_combinator\language\proto\Message;
use vektah\parser_combinator\language\proto\Option;
use vektah\parser_combinator\language\proto\Package;
use vektah\parser_combinator\parser\CharParser;
use vektah\parser_combinator\parser\CharRangeParser;
use vektah\parser_combinator\parser\EofParser;
use vektah\parser_combinator\parser\Parser;
use vektah\parser_combinator\parser\PositiveMatch;
use vektah\parser_combinator\parser\RegexParser;
use vektah\parser_combinator\parser\StringParser;
use vektah\parser_combinator\parser\WhitespaceParser;
use vektah\parser_combinator\parser\literal\FloatLiteral;
use vektah\parser_combinator\parser\literal\IntLiteral;
use vektah\parser_combinator\parser\literal\StringLiteral;

/**
 * Parser for google protobuffers proto files.
 */
class ProtoParser
{
    /** @var Parser */
    private $rootParser;

    public function __construct()
    {
        $identifier = new Concatenate(new Sequence([
            new CharRangeParser(['a' => 'z', 'A' => 'Z', '_' => '_'], 1),
            new CharRangeParser(['a' => 'z', 'A' => 'Z', '0' => '9', '_' => '_', '.' => '.']),
        ]));
        $positive = new PositiveMatch();

        $comment = new Ignore(new Sequence(['//', new RegexParser('/^.*/')]));
        $ws = new Sequence([new WhitespaceParser(), new Choice([new Many([new Sequence([$comment, new WhitespaceParser()])]), ''])]);

        // Literals
        $bool = new Closure(new Choice(['true', 'false']), function($data) {
            return $data === 'true';
        });

        $null = new Closure(new StringParser('null'), function($data) {
            return null;
        });

        $int = new IntLiteral();
        $string = new StringLiteral();
        $float = new FloatLiteral(false);

        $literal = new Choice([$int, $float, $bool, $string, $null]);

        $label = new Choice(['required', 'optional', 'repeated']);
        $type = new Choice(['double', 'float', 'int32', 'int64', 'uint32', 'uint64', 'sint32', 'sint64', 'fixed32', 'fixed64', 'sfixed32', 'sfixed64', 'bool', 'string', 'bytes' /*, $userType */]);

        $var = new Choice([
            $literal,
            new Closure($identifier, function($data) {
                return new Identifier($data);
            })
        ]);

        $field = new Closure(new Sequence([
            $label,
            $positive,
            $ws,
            new Choice([$type, $identifier]),
            $ws,
            $identifier,
            $ws,
            new StringParser('=', true, false),
            $ws,
            $int,
            $ws,
            new Closure(new Choice([new Sequence(['[', $ws, 'default', $positive, $ws, '=', $ws, $var, $ws, ']', $ws]), '']), function($data) {
                return isset($data[3]) ? $data[3] : null;
            }),
            new StringParser(';', true, false),
            $ws
        ]), function($data) {
            $default = null;
            if(isset($data[4])) {
                $default = $data[4];
            }
            return new Field($data[0], $data[1], $data[2], $data[3], $default);
        });


        $extensions = new Closure(new Sequence(['extensions', $positive, $ws, $var, $ws, 'to', $ws, $var, $ws, ';', $ws]), function($data) {
            return new Extensions($data[1], $data[3]);
        });

        $extend = new Closure(new Sequence([
            'extend',
            $positive,
            $ws,
            $identifier,
            $ws,
            '{',
            $ws,
            new Many([$field]),
            $ws,
            '}',
            $ws,
        ]), function($data) {
            return new Extend($data[1], $data[3]);
        });

        $import = new Closure(new Sequence(['import', $positive, $ws, $string, $ws, ';', $ws]), function($data) {
            return new Import($data[1]);
        });

        $package = new Closure(new Sequence(['package', $positive, $ws, $identifier, $ws, ';', $ws]), function($data) {
            return new Package($data[1]);
        });

        $option = new Closure(new Sequence([
            'option',
            $positive,
            $ws,
            new Concatenate(new Sequence([
                new CharParser('(', 0, 1, false),
                $ws,
                new CharParser('.', 0, 1),
                $ws,
                $identifier,
                $ws,
                new CharParser(')', 0, 1, false),
            ])),
            $ws,
            '=',
            $ws,
            $literal,
            $ws,
            ';',
            $ws
        ]), function($data) {
            return new Option($data[1], $data[3]);
        });

        $enumField = new Closure(new Sequence([
            $identifier,
            $ws,
            '=',
            $ws,
            $int,
            $ws,
            ';',
            $ws,
        ]), function($data) {
            return [$data[0], $data[2]];
        });

        $enum = new Closure(new Sequence(['enum', $positive, $ws, $identifier, $ws,  '{', $ws, new Many([$option, $enumField]), $ws, '}', $ws]), function($data) {
            $keyed = [];

            foreach ($data[3] as $entry) {
                $keyed[$entry[1]] = $entry[0];
            }
            return new Enum($data[1], $keyed);
        });

        $definition = new Choice([$field, $enum, $extensions, $extend]);

        $message = new Closure(new Sequence(['message', $positive, $ws, $identifier, $ws, '{', $ws, new Many([$definition]), $ws, '}', $ws]), function($data) {
            $message = new Message($data[1], $data[3]);

            return $message;
        });

        $definition->append($message);

        $this->rootParser = new Closure(new Sequence([$ws, new Many([$option, $enum, $package, $import, $message, $extend]), $ws, new EofParser()]), function($data) {
            return $data[0];
        });
    }

    public function parse($input)
    {
        $result = $this->rootParser->parse(new Input($input));

        if ($result->errorMessage) {
            throw new ParseException($result->errorMessage);
        } else {
            return $result->data;
        }
    }
}

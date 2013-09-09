<?php

namespace vektah\parser_combinator\language;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\combinator\Choice;
use vektah\parser_combinator\combinator\Many;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\formatter\Closure;
use vektah\parser_combinator\language\proto\Field;
use vektah\parser_combinator\language\proto\Message;
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
        $identifier = new RegexParser('/^[A-Za-z_][\w_]*/');
        $positive = new PositiveMatch();

        $ws = new WhitespaceParser();

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

        $field = new Closure(new Sequence([$label, $positive, $ws, $type, $ws, $identifier, $ws, new StringParser('=', true, false), $ws, $int, $ws, new StringParser(';', true, false), $ws]), function($data) {
            return new Field($data[0], $data[1], $data[2], $data[3]);
        });


        $definition = $field;

        $message = new Closure(new Sequence([$ws, 'message', $positive, $ws, $identifier, $ws, '{', $ws, new Many([$definition]), $ws, '}', $ws]), function($data) {
            $message = new Message($data[1], $data[3]);

            return $message;
        });

        $this->rootParser = new Many([$message]);
    }

    public function parse(Input $input)
    {
        $result = $this->rootParser->parse($input);

        if ($result->errorMessage) {
            throw new ParseException($result->errorMessage);
        } else {
            return $result->data;
        }
    }
}

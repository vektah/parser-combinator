<?php

namespace vektah\parser_combinator\language;


use vektah\parser_combinator\Input;
use vektah\parser_combinator\combinator\Choice;
use vektah\parser_combinator\combinator\Many;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\formatter\Closure;
use vektah\parser_combinator\formatter\Flatten;
use vektah\parser_combinator\parser\EofParser;
use vektah\parser_combinator\parser\StringParser;
use vektah\parser_combinator\parser\WhitespaceParser;
use vektah\parser_combinator\parser\literal\FloatLiteral;
use vektah\parser_combinator\parser\literal\IntLiteral;
use vektah\parser_combinator\parser\literal\StringLiteral;

class JsonParser
{
    private $rootParser;

    public function __construct()
    {
        $whitespace = new WhitespaceParser();
        $left_brace = new StringParser('{', true, false);
        $right_brace = new StringParser('}', true ,false);
        $left_bracket = new StringParser('[', true, false);
        $right_bracket = new StringParser(']', true, false);
        $double_quote = new StringParser('"', true, false);
        $colon = new StringParser(':', true, false);
        $comma = new StringParser(',', true, false);

        // Types
        $bool = new Closure(new Choice(['true', 'false']), function($data) {
            return $data === 'true';
        });

        $null = new Closure(new StringParser('null'), function($data) {
            return null;
        });

        $string = new StringLiteral();
        $int = new IntLiteral();
        $float = new FloatLiteral();

        $value = new Choice([$bool, $null, $string, $float, $int]);

        $object = new Closure(new Sequence(
            [
                $whitespace,
                $left_brace,
                $whitespace,
                new Many(
                    [
                        new Sequence(
                            [
                                $string,
                                $whitespace,
                                $colon,
                                $whitespace,
                                $value,
                                $whitespace
                            ]
                        )
                    ],
                    0,
                    1
                ),
                new Many(
                    [
                        new Sequence(
                            [
                                $comma,
                                $whitespace,
                                $string,
                                $whitespace,
                                $colon,
                                $whitespace,
                                $value
                            ]
                        ),
                    ]
                ),
                $whitespace,
                $right_brace,
                $whitespace
            ]
        ), function ($data) {
            $result = [];

            foreach ($data as $value) {
                foreach ($value as $value1) {
                    $result[$value1[0]] = $value1[1];
                }
            }

            return $result;
        });
        $value->append($object);
        $array = new Flatten(
            new Sequence(
                [
                    $whitespace,
                    $left_bracket,
                    $whitespace,
                    // First one has no comma
                    new Many([$value], 0, 1),
                    new Many([new Sequence([$whitespace, $comma, $whitespace, $value])]),
                    $whitespace,
                    $right_bracket,
                    $whitespace
                ]
            )
        );
        $value->append($array);

        $this->rootParser = new Closure(new Sequence([$value, new EofParser()]), function($data) {
            return $data[0];
        });
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

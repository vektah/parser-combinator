<?php

namespace vektah\parser_combinator\language;


use vektah\parser_combinator\combinator\Choice;
use vektah\parser_combinator\combinator\Many;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\formatter\Closure;
use vektah\parser_combinator\formatter\Concatenate;
use vektah\parser_combinator\formatter\Flatten;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\EofParser;
use vektah\parser_combinator\parser\Parser;
use vektah\parser_combinator\parser\RegexParser;
use vektah\parser_combinator\parser\StringParser;

class JsonParser implements Parser
{
    private $rootParser;

    public function __construct()
    {
        $whitespace = new RegexParser('/^[ \t\n\r]*/', false);
        $left_brace = new StringParser('{', true, false);
        $right_brace = new StringParser('}', true ,false);
        $left_bracket = new StringParser('[', true, false);
        $right_bracket = new StringParser(']', true, false);
        $double_quote = new StringParser('"', true, false);
        $colon = new StringParser(':', true, false);
        $comma = new StringParser(',', true, false);

        // Types
        $true = new Closure(new StringParser('true'), function() {return true; });
        $false = new Closure(new StringParser('false'), function() {return false; });
        $null = new Closure(new StringParser('null'), function() {return null; });
        $string = new Closure(new Concatenate(
                new Sequence(
                    [
                        $double_quote,
                        new Many([new RegexParser('/^[^\"]+/'), new StringParser('\"')]),
                        $double_quote
                    ]
                )
            ),
            function($result) {
                return preg_replace_callback('/\\\\([\\\\a-zA-Z0-9])/', function($matches) {
                    // TODO: There must be a better way to do this...
                    return eval("return \"\\$matches[1]\";");
                }, $result);
            }
        );
        $number = new Closure(new RegexParser('#^-?(0|[1-9][0-9]*)(\.[0-9]+)?([eE][-+]?[0-9]+)?#'), function($data) {
            if (intval($data) == $data) {
                return (int)$data;
            } else {
                return (float)$data;
            }
        });

        $value = new Choice([$true, $false, $null, $string, $number]);

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

        $this->rootParser = new Sequence([$value, new EofParser()]);
        $this->rootParser->formatCallback(function($data) {
            return $data[0];
        });
    }

    public function parse(Input $input)
    {
        return $this->rootParser->parse($input);
    }
}

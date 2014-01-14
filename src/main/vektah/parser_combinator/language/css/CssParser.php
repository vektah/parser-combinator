<?php

namespace vektah\parser_combinator\language;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\combinator\Choice;
use vektah\parser_combinator\combinator\Many;
use vektah\parser_combinator\combinator\Not;
use vektah\parser_combinator\combinator\OptionalChoice;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\formatter\Ignore;
use vektah\parser_combinator\parser\CharParser;
use vektah\parser_combinator\parser\CharRangeParser;
use vektah\parser_combinator\parser\NegativeCharParser;
use vektah\parser_combinator\parser\PositiveMatch;
use vektah\parser_combinator\parser\WhitespaceParser;

/**
 * Parser for CSS files.
 */
class CssParser
{
    public function __construct()
    {
        $positive = new PositiveMatch();

        $comment = new Sequence(['/*', new NegativeCharParser('*/'), '*/']);

        $ws = new Ignore(new Sequence([new WhitespaceParser(), new Choice([new Many([new Sequence([$comment, new WhitespaceParser()])]), ''])]));

        $hex_digits = new CharRangeParser(['a' => 'f', 'A' => 'F', '0' => '9'], 1, 6);

        $escape = new Sequence(['\\', new Choice([
            new Not([new CharParser("\r\n"), $hex_digits]),
            new Sequence([$hex_digits, $ws])
        ])]);

        $alpha = new CharRangeParser(['a' => 'z', 'A' => 'Z', '_'], 1);

        $alphanum = new CharRangeParser(['a' => 'z', 'A' => 'Z', '_', '0' => '9'], 1);

        $digit = new CharRangeParser(['0' => '9'], 1);

        $ident = new Sequence([
            new OptionalChoice(['-']),
            new Choice([
                $alpha,
                $escape
            ]),
            new OptionalChoice([
                new Many([
                    $alphanum,
                    $escape
                ])
            ])
        ]);

        $function_token = new Sequence([$ident, '(']);

        $at_keyword_token = new Sequence('@', $ident);

        $hash_token = new Sequence('#', new Many([$alpha, $escape]));

        $string_token = new Sequence([
            new Choice([
                new Sequence([
                    '"',
                    new Many([
                        new Not(['"', "\r\n"]),
                        $escape,
                        '\n',
                        '\"'
                    ]),
                    '"'
                ]),
                new Sequence([
                    "'",
                    new Many([
                        new Not(["'", "\r\n"]),
                        $escape,
                        '\n',
                        "\\'"
                    ]),
                    "'"
                ])
            ])
        ]);

        $url_unquoted_token = new Many([
            new Not(['"', "'", '(', ')', '\\', $ws]),
            $escape
        ]);

        $url_token = new Sequence([
            $ident,
            '(',
            $ws,
            new OptionalChoice([
                $url_unquoted_token,
                $string_token
            ]),
            $ws,
            ')'
        ]);

        $number_token = new Sequence([
            new OptionalChoice(['+', '-']),
            new Choice([
                new Sequence([$digit, '.', $positive, $digit]),
                $digit,
                new Sequence(['.', $positive, $digit])
            ]),
            new OptionalChoice([
                new Sequence([
                    new Choice(['e', 'E']),
                    $positive,
                    new OptionalChoice(['+', '-']),
                    $digit
                ])
            ])
        ]);

        $dimension_token = new Sequence([$number_token, $ident]);

        $percentage_token = new Sequence([$number_token, "%"]);

        $unicode_range_token = new Sequence([
            new Choice(['u', 'U']),
            '+',
            $hex_digits,
        ]);

        $include_match_token = '~=';
        $dash_match_token = '|=';
        $prefix_match_token = '^=';
        $suffix_match_token = '$=';
        $substring_match_token = '*=';
        $column_token = '||';
        $cdo_token_token = '<!--';
        $cdc_token_token = '-->';



        $preserved_token = new Choice([
            $ident, $hash_token, $string_token, $url_token, $number_token, $percentage_token, $dimension_token,
            $unicode_range_token, $comment, $cdo_token_token, $cdc_token_token, ':', ';', $at_keyword_token, ')', '}',
            $include_match_token, $dash_match_token, $prefix_match_token, $suffix_match_token, $substring_match_token, $column_token
        ]);

        $component_value = new Sequence([
            $preserved_token
        ]);

        $component_values = new Many([$component_value]);

        $squiggle_block = new Sequence(['{', $component_values, '}']);
        $circle_block = new Sequence(['(', $component_values, ')']);
        $square_block = new Sequence(['[', $component_values, ']']);

        $component_value->append($squiggle_block);
        $component_value->append($circle_block);
        $component_value->append($square_block);

        $function_block = new Sequence($function_token, $positive, new OptionalChoice([$component_value]), ')');
        $component_value->append($function_block);



        $stylesheet = new Many([
            $cdc_token_token,
            $cdo_token_token,
            new CharParser("\n\t\r ", 1, null, false),
        ]);

        $this->rootParser = $stylesheet;
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

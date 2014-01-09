<?php

namespace vektah\parser_combinator\language\css;

use vektah\parser_combinator\combinator\Choice;
use vektah\parser_combinator\combinator\Many;
use vektah\parser_combinator\combinator\Not;
use vektah\parser_combinator\combinator\OptionalChoice;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\formatter\Closure;
use vektah\parser_combinator\formatter\Concatenate;
use vektah\parser_combinator\formatter\Flatten;
use vektah\parser_combinator\language\Grammar;
use vektah\parser_combinator\parser\CharParser;
use vektah\parser_combinator\parser\CharRangeParser;
use vektah\parser_combinator\parser\EofParser;
use vektah\parser_combinator\parser\PositiveMatch;
use vektah\parser_combinator\parser\StringParser;
use vektah\parser_combinator\parser\WhitespaceParser;

/**
 * Parser for CSS Selectors.
 */
class CssSelectorParser extends Grammar
{
    public function __construct()
    {
        $this->positive = new PositiveMatch();
        $this->ws = new WhitespaceParser();
        $this->nonascii = new Not(new CharRangeParser(["\0" => "\177"]));
        $this->unicode = new Sequence('\\', new CharRangeParser(['0' => '9', 'a' => 'f', 'A' => 'F'], 1, 6), $this->ws);
        $this->escape = new Choice($this->unicode, new Not(new CharRangeParser(['0' => '9', 'a' => 'f', 'A' => 'F', ["\r\n"], 1])));
        $this->nmstart = new Choice(new CharRangeParser(['a' => 'z', 'A' => 'Z', ['_']], 1), $this->nonascii, $this->escape);
        $this->nmchar = new Choice(new CharRangeParser(['a' => 'z', 'A' => 'Z', '0' => '9'], 1), $this->nonascii, $this->escape);
        $this->ident = new Concatenate(new Sequence(new OptionalChoice('-'), $this->nmstart, new Many($this->nmchar)));
        $this->prefix_match = '^=';
        $this->suffix_match = '$=';
        $this->dash_match = '|=';
        $this->substring_match = '*=';
        $this->includes = '!=';
        $this->name = new Concatenate(new Many([$this->nmchar], 1));
        $this->num = new Choice(
            new CharRangeParser(['0' => '9'], 1),
            new Sequence(
                new CharRangeParser(['0' => '9']),
                '.',
                $this->positive,
                new CharRangeParser(['0' => '9', 1])
            )
        );
        $this->dimension = new Sequence($this->num, $this->ident);

        $this->string = new Choice(
            new Sequence('"', new Many(new Not(new CharParser("\n\r\f\"")), '\n', '\r', '\f', '\"'), '"'),
            new Sequence("'", new Many(new Not(new CharParser("\n\r\f'")), '\n', '\r', '\f', '\''), "'")
        );

        $this->namespace_prefix = new Sequence(new OptionalChoice($this->ident, '*'), '|');

        $this->class = new Closure(new Sequence('.', $this->ident), function($data) {
            return new ClassSelector($data[1]);
        });

        $this->type_selector = new Closure(new Sequence(new OptionalChoice($this->namespace_prefix), $this->ident), function($data) {
            return new TypeSelector($data[1], $data[0]);
        });
        $this->universal = new Sequence(new OptionalChoice($this->namespace_prefix), '*');

        $this->hash = new Closure(new Sequence('#', $this->name), function($data) {
            return new HashSelector($data[1]);
        });

        $this->attrib = new Sequence(
            '[',
            $this->ws,
            new OptionalChoice($this->namespace_prefix),
            $this->ident,
            $this->ws,
            new OptionalChoice(
                new Sequence(
                    new Choice(
                        new Many($this->prefix_match, $this->suffix_match, $this->substring_match, $this->dash_match, '=', $this->includes)
                    ),
                    $this->ws,
                    new Choice($this->ident, $this->string),
                    $this->ws
                )
            ),
            ']'
        );
        $this->expression = new Sequence(new Choice('+', '-', $this->dimension, $this->num, $this->string, $this->ident), $this->ws);
        $this->functional_pseudo = new Sequence($this->ident, $this->ws, '(', $this->ws, new Many($this->expression), $this->ws, ')');
        $this->pseudo = new Sequence(':', new OptionalChoice(':'), new Choice($this->ident, $this->functional_pseudo));
        $this->negation_arg = new Choice($this->type_selector, $this->universal, $this->hash, $this->class, $this->attrib, $this->pseudo);
        $this->negation = new Sequence('NOT', $this->ws, $this->negation_arg, $this->ws, ')');

        $this->simple_selector_sequence = new Flatten(new Choice(
            new Sequence(
                new Choice($this->type_selector, $this->universal),
                new Many($this->hash, $this->class, $this->attrib, $this->pseudo, $this->negation)
            ),
            new Many([$this->hash, $this->class, $this->attrib, $this->pseudo, $this->negation], 1)
        ));

        $direct_descendant_combinator = new Closure(new StringParser('>'), function() {
            return new DirectDescendantOf();
        });

        $descendant_combinator = new Closure(new WhitespaceParser(1, true), function() {
            return new DescendantOf();
        });
        $this->combinator = new Closure(new Sequence(new Choice('+', '~', $direct_descendant_combinator, $descendant_combinator), $this->ws), function($data) {
            return $data[0];
        });

        $this->selector = new Closure(new Sequence($this->simple_selector_sequence, new Many(new Sequence($this->combinator, $this->simple_selector_sequence))), function($data) {
            $selectors = [];

            $selectors[] = $data[0];

            foreach ($data[1] as $children) {
                $selectors[] = $children[0];
                $selectors[] = $children[1];
            }

            return new Selector($selectors);
        });

        $this->selector_group = new Closure(new Sequence($this->selector, new Many(new Sequence(',', $this->ws, $this->selector))), function($data) {
            $result = [];

            $result[] = $data[0];

            foreach ($data[1] as $additional) {
                $result[] = $additional[2];
            }

            return $result;
        });

        $this->root = new Sequence($this->selector_group, new EofParser());
    }
}

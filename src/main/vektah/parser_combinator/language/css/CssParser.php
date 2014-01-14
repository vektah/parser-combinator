<?php

namespace vektah\parser_combinator\language\css;

use vektah\parser_combinator\combinator\Many;
use vektah\parser_combinator\combinator\Not;
use vektah\parser_combinator\combinator\OptionalChoice;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\formatter\Closure;
use vektah\parser_combinator\formatter\Concatenate;
use vektah\parser_combinator\formatter\Ignore;
use vektah\parser_combinator\language\Grammar;
use vektah\parser_combinator\parser\CharRangeParser;
use vektah\parser_combinator\parser\RepSep;
use vektah\parser_combinator\parser\SingletonTrait;
use vektah\parser_combinator\parser\WhitespaceParser;

/**
 * Parser for CSS files.
 */
class CssParser extends Grammar
{
    use SingletonTrait;

    public function __construct()
    {
        $this->selector = CssSelectorParser::instance();

        // Imported rules
        $this->ws = $this->selector->ws;
        $this->ident = $this->selector->ident;
        $this->string = $this->selector->string;
        $this->num = $this->selector->num;
        $this->dimension = $this->selector->dimension;
        $this->nonascii = $this->selector->nonascii;
        $this->escape = $this->selector->escape;
        $this->positive = $this->selector->positive;

        $this->hash = new Concatenate(new Sequence('#', new Many(new CharRangeParser(['a' => 'z', 'A' => 'Z', '0' => '0', ['_-']], 1), $this->nonascii, $this->escape)));
        $this->percentage = new Concatenate(new Sequence($this->num, '%'));

        $this->comment = new Ignore(new Sequence('/*', new Not('*/'), '*/', $this->ws));

        $this->value = new Many($this->ident, $this->string, $this->percentage, $this->dimension, $this->num, $this->hash, new WhitespaceParser(1));

        $this->declaration = new Closure(
            new Sequence(
                $this->ident,
                $this->ws,
                ':',
                $this->ws,
                $this->value,
                $this->ws,
                new OptionalChoice('!important')
            ),
            function($data) {
                return new CssDeclaration($data[0], implode(' ', $data[2]), $data[3] !== null);
            }
        );

        $this->ruleset = new Closure(
            new Sequence(
                $this->selector,
                $this->positive,
                $this->ws,
                '{',
                $this->ws,
                new RepSep($this->declaration, new Sequence(';', $this->ws)),
                '}',
                $this->ws
            ),
            function($data) {
                return new CssRuleSet($data[0], $data[2]);
            }
        );

        $this->root = new Closure(new Many($this->ruleset, $this->comment), function($data) {

            return new Css($data);
        });
    }

    /**
     * @param string $input
     * @return Css
     */
    public function parseString($input) {
        return parent::parseString($input);
    }
}

<?php

namespace vektah\parser_combinator\parser\literal;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;
use vektah\parser_combinator\combinator\Choice;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\formatter\Closure;
use vektah\parser_combinator\formatter\Concatenate;
use vektah\parser_combinator\formatter\Ignore;
use vektah\parser_combinator\parser\CharParser;
use vektah\parser_combinator\parser\CharRangeParser;
use vektah\parser_combinator\parser\Parser;
use vektah\parser_combinator\parser\PositiveMatch;

/**
 * Generic integer literal parser.
 *
 * Will capture:
 *  - 1234 as decimal int
 *  - -1234 as negative decimal int
 *  - 033 as octal
 *  - 090 as decimal
 *  - 0xFF as hex
 */
class IntLiteral extends Parser
{
    private $root;

    public function __construct($octal = true, $hex = true)
    {
        // Order here is important, if hex fails to match try oct, otherwise its an int.
        $this->root = new Choice();

        if ($hex) {
            $octInt = new Closure(new Concatenate(new Sequence([new Ignore('0'), new CharRangeParser(['0' => '7'], 1, 1), new PositiveMatch(), new CharRangeParser(['0' => '7'])])), function($data) {
                return octdec($data);
            });

            $this->root->append($octInt);
        }

        if ($hex) {
            $hexInt = new Closure(new Concatenate(new Sequence([new Ignore('0'), new CharParser('xX', 1, 1, false), new PositiveMatch(), new PositiveMatch(), new CharRangeParser(['A' => 'F', 'a' => 'f', '0' => '9'], 1), new CharRangeParser(['A' => 'F', 'a' => 'f', '0' => '9'])])), function($data) {
                return hexdec($data);
            });

            $this->root->append($hexInt);
        }

        $decInt =  new Closure(new Concatenate(new Sequence([new CharParser('-'), new CharRangeParser(['0' => '9'], 1)])), function ($data) {
            return (int)$data;
        });

        $this->root->append($decInt);

    }


    /**
     * Parse the given input
     *
     * @param Input $input
     *
     * @return Result result
     */
    public function parse(Input $input)
    {
        return $this->root->parse($input);
    }
}

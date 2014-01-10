<?php

namespace vektah\parser_combinator\combinator;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;
use vektah\parser_combinator\exception\GrammarException;
use vektah\parser_combinator\parser\Parser;
use vektah\parser_combinator\parser\StringParser;

abstract class Combinator extends Parser
{
    /** @var callable */
    public $formatCallback;

    /**
     * @var Parser[]
     */
    private $parsers;

    public function __construct($parsers = null)
    {
        if (!is_array($parsers)) {
            $parsers = func_get_args();
        }

        foreach ($parsers as $id => $parser) {
            if (is_string($parser)) {
                $parsers[$id] = new StringParser($parser);
            } elseif (!$parser instanceof Parser) {
                throw new GrammarException('There is an object that is not a parser in this combinator.');
            }
        }
        $this->parsers = $parsers;
    }

    public function append(Parser $parser)
    {
        $this->parsers[] = $parser;
    }

    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * @return Result
     */
    public function parse(Input $input)
    {
        $result = $this->combine($input);
        return $result;
    }

    abstract public function combine(Input $input);
}

<?php

namespace vektah\parser_combinator\combinator;

use vektah\parser_combinator\exception\GrammarException;
use vektah\parser_combinator\Input;
use vektah\parser_combinator\parser\Parser;

abstract class Combinator implements Parser
{
    /** @var callable */
    public $formatCallback;

    /**
     * @var Parser[]
     */
    private $parsers;

    public function __construct(array $parsers = [])
    {
        foreach ($parsers as $parser) {
            if (!$parser instanceof Parser) {
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

    public function formatCallback(callable $callback)
    {
        $this->formatCallback = $callback;
    }

    public function parse(Input $input)
    {
        $result = $this->combine($input);

        if ($this->formatCallback) {
            return call_user_func($this->formatCallback, $result);
        } else {
            return $result;
        }
    }

    abstract public function combine(Input $input);
}

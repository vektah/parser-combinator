<?php

namespace vektah\parser_combinator\language;

use vektah\parser_combinator\Input;
use vektah\parser_combinator\exception\GrammarException;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\parser\Parser;
use vektah\parser_combinator\parser\StringParser;

class Grammar
{
    /** @var Parser[] */
    private $parsers;

    public function __set($name, $parser) {
        if (is_string($parser)) {
            $parser = new StringParser($parser);
        }
        $this->parsers[$name] = $parser;
        if (!$parser->hasName()) {
            $parser->setName($name);
        }

        return $parser;
    }

    public function __get($name) {
        if (!isset($this->parsers[$name])) {
            throw new GrammarException("Node $name is undefined");
        }

        return $this->parsers[$name];
    }

    /**
     * @param string $input
     * @throws ParseException
     * @return mixed
     */
    public function parse($input)
    {
        $result = $this->root->parse(new Input($input));

        if ($result->errorMessage) {
            throw new ParseException($result->errorMessage . "\nParser Stack:\n - " . implode("\n - ", $result->getParsers()) . "\n");
        }

        return $result->data;
    }
}

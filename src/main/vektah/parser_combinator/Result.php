<?php

namespace vektah\parser_combinator;

use vektah\parser_combinator\parser\Parser;

class Result
{
    /** @var mixed The result data */
    public $data;

    /** @var bool when true backtracking is disabled so that errors are localized correctly */
    public $positiveMatch = false;

    /** @var bool if true the match was successful */
    public $match = false;

    /** @var string if set an error message to display */
    public $errorMessage;

    /** @var bool if true this parsers data will be included in the syntax tree */
    public $hasData;

    /** @var string The parser stack that generated this result */
    private $parsers = [];

    /** @var string the last named parser on the stack */
    private $lastParser;

    private function __construct()
    {

    }

    public function addParser(Parser $parser) {
        $name = $parser->getName();
        if ($name !== $this->lastParser) {
            $this->parsers[] = $name;
        }

        $this->lastParser = $name;

        return $this;
    }

    public function getParsers() {
        return $this->parsers;
    }

    public function getParserStack() {
        return implode('.', array_reverse($this->parsers));
    }

    public static function error($message, $positive = false)
    {
        $result = new Result();
        $result->errorMessage = $message;
        $result->hasData = false;
        $result->match = false;
        $result->positiveMatch = $positive;

        return $result;
    }

    public static function match($data, $positive = false)
    {
        $result = new Result();
        $result->hasData = true;
        $result->data = $data;
        $result->match = true;
        $result->positiveMatch = $positive;

        return $result;
    }

    public static function nonCapturingMatch($positive = false)
    {
        $result = new Result();
        $result->match = true;
        $result->hasData = false;
        $result->positiveMatch = $positive;

        return $result;
    }

    public function __toString() {
        if ($this->errorMessage) {
            return "Error($this->errorMessage)";
        } elseif ($this->positiveMatch) {
            return "PositiveMatch(" . print_r($this->data, true) . ")";
        } else {
            return "Match(" . print_r($this->data, true) . ")";
        }
    }
}

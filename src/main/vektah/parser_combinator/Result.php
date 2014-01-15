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

    /** @var int the location this result occurred at */
    public $offset;

    /** @var string The parser stack that generated this result */
    private $parsers = [];

    /** @var string the last named parser on the stack */
    private $lastParser;

    public function addParser(Parser $parser) {
        if ($parser !== $this->lastParser) {
            $this->parsers[] = $parser;
        }

        $this->lastParser = $parser;

        return $this;
    }

    public function getParsers() {
        return $this->parsers;
    }

    public function getInlineParserStack() {
        return implode('.', array_reverse(array_map(function($value) {
            return $value->getName();
        }, $this->parsers)));
    }

    public function getParserStack() {
        return " - " . implode("\n - ", array_reverse(array_map(function($value) {
            return $value->getName();
        }, $this->parsers)));
    }

    public static function error($message, $positive = false, $offset = null)
    {
        $result = new Result();
        $result->errorMessage = $message;
        $result->hasData = false;
        $result->match = false;
        $result->positiveMatch = $positive;
        $result->offset = $offset;

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

<?php

namespace vektah\parser_combinator;

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

    private function __construct()
    {

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
}

<?php

namespace vektah\parser_combinator\parser;


use vektah\parser_combinator\Input;
use vektah\parser_combinator\Result;
use vektah\parser_combinator\combinator\Sequence;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\formatter\Closure;

abstract class Parser
{
    private $name;

    /**
     * Parse the given input
     *
     * @param Input $input
     *
     * @return Result result
     */
    abstract public function parse(Input $input);

    /**
     * @param string $input
     *
     * @throws ParseException
     * @return mixed
     */
    public function parseString($input) {
        $parser = new Closure(new Sequence($this, new EofParser()), function($data) {
            return $data[0];
        });

        $result = $parser->parse(new Input($input));

        if ($result->errorMessage) {
            throw new ParseException($result->errorMessage . "\nParser Stack:\n - " . implode("\n - ", $result->getParsers()) . "\n");
        }
        return $result->data;
    }

    public function getName() {
        if (!$this->hasName()) {
            return join('', array_slice(explode('\\', get_class($this)), -1));
        }

        return $this->name;
    }

    public function hasName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public static function getParserStack()
    {
        $message = '';
        $trace = debug_backtrace();

        $lastName = null;
        foreach ($trace as $frame) {
            if (isset($frame['object']) && $frame['object'] instanceof Parser && $name = $frame['object']->getName()) {
                if ($name != $lastName) {
                    if ($frame['object']->hasName()) {
                        $message .= ' - #' . $name . "\n";
                    } else {
                        $message .= ' - ' . $name . "\n";
                    }

                }

                $lastName = $name;
            }
        }

        return substr($message, 0, strlen($message) - 1);
    }
}
